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
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_code', 20)->unique()->index();
            $table->string('name')->nullable();
            $table->string('phone', 20)->nullable();
            $table->text('address')->nullable();
            $table->string('rt', 10)->nullable();
            $table->string('rw', 10)->nullable();
            $table->string('category', 100);
            $table->string('title');
            $table->text('description');
            $table->decimal('location_lat', 10, 8)->nullable();
            $table->decimal('location_lng', 11, 8)->nullable();
            $table->text('location_text')->nullable();
            $table->enum('status', ['backlog', 'verification', 'todo', 'in_progress', 'done', 'rejected'])
                ->default('backlog');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_anonymous')->default(false);
            $table->json('images')->nullable(); // Store array of image paths
            $table->timestamp('sla_deadline')->nullable(); // SLA deadline based on category
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for performance
            $table->index('status');
            $table->index('category');
            $table->index('created_at');
            $table->index(['location_lat', 'location_lng']); // For duplicate detection
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
