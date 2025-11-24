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
        Schema::create('statistic_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('statistic_id')->constrained()->onDelete('cascade');
            $table->integer('year');
            $table->string('value');
            $table->json('additional_data')->nullable();
            $table->timestamps();
            
            // Indexes for performance
            $table->index('statistic_id');
            $table->index('year');
            $table->unique(['statistic_id', 'year']); // Prevent duplicate year for same statistic
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('statistic_details');
    }
};
