<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\Complaint;

class ComplaintObserver
{
    /**
     * Handle the Complaint "updating" event.
     */
    public function updating(Complaint $complaint): void
    {
        // Store original assigned_to before update
        if ($complaint->isDirty('assigned_to')) {
            $complaint->setAttribute('_old_assigned_to', $complaint->getOriginal('assigned_to'));
        }
    }

    /**
     * Handle the Complaint "updated" event.
     */
    public function updated(Complaint $complaint): void
    {
        // Check if assignment was changed using getChanges()
        $changes = $complaint->getChanges();
        
        if (isset($changes['assigned_to'])) {
            $oldAssigned = $complaint->getOriginal('assigned_to');
            $newAssigned = $changes['assigned_to'];

            // Only create log if actually changed
            if ($oldAssigned != $newAssigned) {
                $assignedUser = $newAssigned ? \App\Models\User::find($newAssigned) : null;
                
                if ($assignedUser) {
                    $note = "Ditugaskan kepada: {$assignedUser->name}";
                } elseif ($oldAssigned) {
                    $note = "Assignment dihapus";
                } else {
                    $note = "Ditugaskan";
                }

                try {
                    ActivityLog::create([
                        'user_id' => auth()->id(),
                        'action' => 'assigned',
                        'model_type' => Complaint::class,
                        'model_id' => $complaint->id,
                        'complaint_id' => $complaint->id,
                        'note' => $note,
                        'ip_address' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                    ]);
                } catch (\Exception $e) {
                    // Log error but don't break the update
                    \Log::error('Failed to create assignment activity log', [
                        'complaint_id' => $complaint->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }
    }
}
