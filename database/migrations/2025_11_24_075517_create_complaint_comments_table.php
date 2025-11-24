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
        Schema::create('complaint_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('complaint_id')->constrained()->cascadeOnDelete();
            $table->enum('sender_type', ['admin', 'warga']);
            $table->string('sender_name')->nullable(); // For admin comments
            $table->text('message');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // Admin user who commented
            $table->timestamps();
            
            $table->index('complaint_id');
            $table->index('sender_type');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaint_comments');
    }
};
