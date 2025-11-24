<?php

namespace App\Console\Commands;

use App\Models\MenuItem;
use Illuminate\Console\Command;

class MenuAudit extends Command
{
    protected $signature = 'menu:audit';

    protected $description = 'Audit MenuItem types and detect incorrect configurations';

    public function handle()
    {
        $this->info('🔍 Auditing Menu Items...');
        $this->newLine();

        $items = MenuItem::with('parent', 'children')->get();
        $issues = [];
        $warnings = [];

        foreach ($items as $item) {
            // A. URL that is actually an Anchor (skip if dropdown or route)
            if ($item->url && $item->isAnchor() && $item->type !== 'anchor' && $item->type !== 'dropdown') {
                $issues[] = [
                    'id' => $item->id,
                    'label' => $item->label,
                    'issue' => 'TYPE_MISMATCH',
                    'expected' => 'anchor',
                    'current' => $item->type,
                    'url' => $item->url,
                    'message' => "Expected TYPE = ANCHOR, Current TYPE = {$item->type}",
                ];
            }

            // B. URL that is actually a Route (internal post)
            if ($item->url && $item->isInternalPost() && $item->type !== 'route') {
                $slug = $item->extractSlug();
                $issues[] = [
                    'id' => $item->id,
                    'label' => $item->label,
                    'issue' => 'TYPE_MISMATCH',
                    'expected' => 'route',
                    'current' => $item->type,
                    'url' => $item->url,
                    'message' => "Expected TYPE = ROUTE (post.show), Current TYPE = {$item->type}. URL: {$item->url}",
                ];
            }

            // C. Dropdown parents with URLs
            if ($item->type === 'dropdown' && $item->url && $item->url !== '#') {
                $warnings[] = [
                    'id' => $item->id,
                    'label' => $item->label,
                    'issue' => 'DROPDOWN_URL',
                    'message' => "Dropdown parent should NOT have URL. Current URL = {$item->url}",
                ];
            }

            // D. External URL should be type 'url'
            if ($item->url && $item->isExternalUrl() && $item->type !== 'url') {
                $issues[] = [
                    'id' => $item->id,
                    'label' => $item->label,
                    'issue' => 'TYPE_MISMATCH',
                    'expected' => 'url',
                    'current' => $item->type,
                    'url' => $item->url,
                    'message' => "Expected TYPE = URL (external), Current TYPE = {$item->type}",
                ];
            }

            // E. Wrong level parent-child relationship
            if ($item->parent && $item->parent->type !== 'dropdown' && $item->parent->hasChildren()) {
                $warnings[] = [
                    'id' => $item->parent->id,
                    'label' => $item->parent->label,
                    'issue' => 'PARENT_TYPE_MISMATCH',
                    'message' => "Parent menu has children but type is '{$item->parent->type}'. Parent with children should be type 'dropdown'.",
                ];
            }
        }

        // Display issues
        if (count($issues) > 0) {
            $this->warn('⚠️  Found ' . count($issues) . ' issue(s):');
            $this->newLine();
            
            foreach ($issues as $issue) {
                $this->line("[WARN] ID {$issue['id']} — {$issue['label']}");
                $this->line("  {$issue['message']}");
                $this->newLine();
            }
        } else {
            $this->info('✅ No type issues found.');
            $this->newLine();
        }

        // Display warnings
        if (count($warnings) > 0) {
            $this->warn('⚠️  Found ' . count($warnings) . ' warning(s):');
            $this->newLine();
            
            foreach ($warnings as $warning) {
                $this->line("[WARN] ID {$warning['id']} — {$warning['label']}");
                $this->line("  {$warning['message']}");
                $this->newLine();
            }
        }

        $total = count($issues) + count($warnings);
        if ($total > 0) {
            $this->info("💡 Run 'php artisan menu:fix' to auto-fix these issues.");
            return Command::FAILURE;
        }

        $this->info('✅ All menu items are correctly configured!');
        return Command::SUCCESS;
    }
}