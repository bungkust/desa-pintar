<?php

namespace App\Filament\Resources\MenuItemResource\Pages;

use App\Filament\Resources\MenuItemResource;
use App\Models\MenuItem;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\DB;

class ListMenuItems extends ListRecords
{
    protected static string $resource = MenuItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    /**
     * Override reorderTable to ensure order is unique per parent_id
     */
    public function reorderTable(array $order): void
    {
        if (! $this->getTable()->isReorderable()) {
            return;
        }

        DB::transaction(function () use ($order) {
            // Get all records being reordered
            $records = MenuItem::whereIn('id', $order)->get()->keyBy('id');
            
            // Group by parent_id and preserve original order within each group
            $groupedByParent = [];
            foreach ($order as $index => $recordId) {
                if (isset($records[$recordId])) {
                    $record = $records[$recordId];
                    $parentId = $record->parent_id ?? 'null';
                    
                    if (!isset($groupedByParent[$parentId])) {
                        $groupedByParent[$parentId] = [];
                    }
                    
                    $groupedByParent[$parentId][] = $recordId;
                }
            }
            
            // Update order for each parent group separately
            // Order is assigned sequentially starting from 1 within each group
            foreach ($groupedByParent as $parentId => $recordIds) {
                $parentCondition = $parentId === 'null' ? null : (int) $parentId;
                
                foreach ($recordIds as $orderIndex => $recordId) {
                    MenuItem::where('id', $recordId)
                        ->update([
                            'order' => $orderIndex + 1,
                        ]);
                }
            }
        });
    }
}
