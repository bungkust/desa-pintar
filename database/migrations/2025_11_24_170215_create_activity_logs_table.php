<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            
            // User who performed the action
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            
            // Action type (e.g., 'status_changed', 'created', 'updated', 'deleted', 'assigned', 'commented')
            $table->string('action', 100);
            
            // Polymorphic relationship to any model
            $table->string('model_type')->nullable();
            $table->unsignedBigInteger('model_id')->nullable();
            $table->index(['model_type', 'model_id']);
            
            // For Complaint-specific activities (status changes)
            $table->foreignId('complaint_id')->nullable()->constrained('complaints')->cascadeOnDelete();
            $table->enum('status_from', ['backlog', 'verification', 'todo', 'in_progress', 'done', 'rejected'])->nullable();
            $table->enum('status_to', ['backlog', 'verification', 'todo', 'in_progress', 'done', 'rejected'])->nullable();
            
            // Additional data (flexible JSON for any extra info)
            $table->json('meta')->nullable();
            
            // Note/description for the activity
            $table->text('note')->nullable();
            
            // Image/attachment (for progress photos, etc.)
            $table->string('image')->nullable();
            
            // Request metadata
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index('complaint_id');
            $table->index('created_at');
            $table->index('action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
