<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Migrate ComplaintUpdate data to ActivityLog
        if (Schema::hasTable('complaint_updates')) {
            DB::statement("
                INSERT INTO activity_logs (
                    user_id,
                    action,
                    model_type,
                    model_id,
                    complaint_id,
                    status_from,
                    status_to,
                    note,
                    image,
                    ip_address,
                    created_at,
                    updated_at
                )
                SELECT 
                    updated_by as user_id,
                    'status_changed' as action,
                    'App\\\\Models\\\\Complaint' as model_type,
                    complaint_id as model_id,
                    complaint_id,
                    status_from,
                    status_to,
                    note,
                    image,
                    NULL as ip_address,
                    created_at,
                    updated_at
                FROM complaint_updates
            ");
        }

        // Migrate AuditLog data to ActivityLog (only if not already migrated)
        if (Schema::hasTable('audit_logs')) {
            DB::statement("
                INSERT INTO activity_logs (
                    user_id,
                    action,
                    model_type,
                    model_id,
                    complaint_id,
                    meta,
                    ip_address,
                    user_agent,
                    created_at,
                    updated_at
                )
                SELECT 
                    user_id,
                    action,
                    model_type,
                    model_id,
                    CASE 
                        WHEN model_type = 'App\\\\Models\\\\Complaint' THEN model_id
                        ELSE NULL
                    END as complaint_id,
                    meta,
                    ip_address,
                    user_agent,
                    created_at,
                    updated_at
                FROM audit_logs
                WHERE NOT EXISTS (
                    SELECT 1 FROM activity_logs al 
                    WHERE al.user_id = audit_logs.user_id 
                    AND al.action = audit_logs.action
                    AND al.model_type = audit_logs.model_type
                    AND al.model_id = audit_logs.model_id
                    AND al.created_at = audit_logs.created_at
                )
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Note: This migration is not reversible as we're merging data
        // If rollback is needed, data should be restored from backup
    }
};
