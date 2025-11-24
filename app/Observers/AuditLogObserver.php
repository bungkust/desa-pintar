<?php

namespace App\Observers;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;

class AuditLogObserver
{
    /**
     * Handle the model "created" event.
     */
    public function created(Model $model): void
    {
        $this->logAction('created', $model);
    }

    /**
     * Handle the model "updated" event.
     */
    public function updated(Model $model): void
    {
        $this->logAction('updated', $model);
    }

    /**
     * Handle the model "deleted" event.
     */
    public function deleted(Model $model): void
    {
        $this->logAction('deleted', $model);
    }

    /**
     * Handle the model "restored" event.
     */
    public function restored(Model $model): void
    {
        $this->logAction('restored', $model);
    }

    /**
     * Handle the model "force deleted" event.
     */
    public function forceDeleted(Model $model): void
    {
        $this->logAction('force_deleted', $model);
    }

    /**
     * Log admin action
     * Sanitizes all user-provided data to prevent log injection attacks
     */
    protected function logAction(string $action, Model $model): void
    {
        if (!auth()->check()) {
            return; // Only log authenticated admin actions
        }

        $user = auth()->user();
        $modelName = get_class($model);
        $modelId = $model->getKey();
        
        // Sanitize changes to prevent log injection
        $changes = $action === 'updated' && $model->wasChanged() 
            ? $this->sanitizeForLogging($model->getChanges()) 
            : null;

        Log::info('Admin Action', [
            'action' => $this->sanitizeString($action),
            'model' => $modelName, // Class name is safe
            'model_id' => $modelId, // ID is safe (integer)
            'user_id' => $user->id, // ID is safe (integer)
            'user_email' => $this->sanitizeString($user->email),
            'user_name' => $this->sanitizeString($user->name),
            'changes' => $changes,
            'ip_address' => $this->sanitizeString(request()->ip()),
            'user_agent' => $this->sanitizeString(request()->userAgent()),
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Sanitize string to prevent log injection
     * Removes newlines and control characters that could be used to inject log entries
     */
    protected function sanitizeString(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        // Remove newlines and carriage returns (prevent log injection)
        $value = str_replace(["\n", "\r", "\r\n"], ' ', $value);
        
        // Remove control characters (except space and tab)
        $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $value);
        
        // Limit length to prevent DoS via extremely long log entries
        return mb_substr($value, 0, 1000, 'UTF-8');
    }

    /**
     * Sanitize array/object for logging
     * Recursively sanitizes all string values in the data structure
     */
    protected function sanitizeForLogging(mixed $data): mixed
    {
        if (is_array($data)) {
            return array_map([$this, 'sanitizeForLogging'], $data);
        }

        if (is_string($data)) {
            return $this->sanitizeString($data);
        }

        // For other types (int, bool, null, etc.), return as-is
        return $data;
    }
}
