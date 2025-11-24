<?php

namespace App\Console\Commands;

use App\Models\MenuItem;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MenuFix extends Command
{
    protected $signature = 'menu:fix {--dry-run : Show what would be fixed without making changes}';

    protected $description = 'Auto-fix MenuItem type issues automatically';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->warn('🔍 DRY RUN MODE - No changes will be made');
            $this->newLine();
        } else {
            $this->info('🔧 Auto-fixing Menu Items...');
            $this->newLine();
        }

        $items = MenuItem::all();
        $fixed = 0;
        $errors = [];

        DB::beginTransaction();

        try {
            foreach ($items as $item) {
                $originalType = $item->type;
                $originalUrl = $item->url;
                $changes = [];

                // A. Anchor Fix (skip if dropdown)
                if ($item->url && $item->isAnchor() && $item->type !== 'anchor' && $item->type !== 'dropdown') {
                    $changes[] = "Type: {$item->type} → anchor";
                    if (!$dryRun) {
                        $item->type = 'anchor';
                    }
                }

                // B. Route Fix for Posts
                if ($item->isInternalPost() && $item->type !== 'route') {
                    $slug = $item->extractSlug();
                    if ($slug) {
                        $changes[] = "Type: {$item->type} → route (post.show, slug: {$slug})";
                        if (!$dryRun) {
                            $item->type = 'route';
                            $item->route_name = 'post.show';
                            $item->route_params = ['slug' => $slug];
                            $item->url = null;
                        }
                    }
                }

                // C. Dropdown Parent Fix
                if ($item->type === 'dropdown' && $item->url && $item->url !== '#') {
                    $changes[] = "URL: {$item->url} → #";
                    if (!$dryRun) {
                        $item->url = '#';
                    }
                }

                // D. External URL Fix
                if ($item->url && $item->isExternalUrl() && $item->type !== 'url') {
                    $changes[] = "Type: {$item->type} → url";
                    if (!$dryRun) {
                        $item->type = 'url';
                    }
                }

                // E. Parent-Child Fix
                if ($item->hasChildren() && $item->type !== 'dropdown') {
                    $changes[] = "Type: {$item->type} → dropdown (parent has children)";
                    if (!$dryRun) {
                        $item->type = 'dropdown';
                        $item->url = '#';
                        $item->route_name = null;
                        $item->route_params = null;
                    }
                }

                // Apply auto-correction method
                if (!$dryRun) {
                    $item->autoCorrectType();
                }

                // Check if any changes were made
                if (count($changes) > 0 || ($item->isDirty() && !$dryRun)) {
                    if ($dryRun) {
                        $this->line("ID {$item->id} — {$item->label}:");
                        foreach ($changes as $change) {
                            $this->line("  • {$change}");
                        }
                        $this->newLine();
                    } else {
                        $item->save();
                        $this->info("✅ Fixed ID {$item->id} — {$item->label}");
                        foreach ($changes as $change) {
                            $this->line("  • {$change}");
                        }
                        $fixed++;
                    }
                }
            }

            if ($dryRun) {
                $this->info('💡 Remove --dry-run flag to apply these fixes.');
                DB::rollBack();
            } else {
                DB::commit();
                $this->newLine();
                $this->info("✅ Successfully fixed {$fixed} menu item(s)!");
            }

            return Command::SUCCESS;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("❌ Error: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }
}