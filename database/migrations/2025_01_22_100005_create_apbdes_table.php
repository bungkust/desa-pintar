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
        Schema::create('apbdes', function (Blueprint $table) {
            $table->id();
            $table->integer('year');
            $table->enum('type', ['pendapatan', 'belanja']);
            $table->string('category');
            $table->bigInteger('amount');
            $table->timestamps();
            
            $table->index('year');
            $table->index('type');
            $table->index(['year', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apbdes');
    }
};

