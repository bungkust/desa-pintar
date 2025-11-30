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
        // Update hero_slides table to replace broken via.placeholder.com URLs with placehold.co
        if (Schema::hasTable('hero_slides')) {
            DB::table('hero_slides')
                ->where('image', 'like', '%via.placeholder.com%')
                ->get()
                ->each(function ($slide) {
                    $newImage = str_replace('via.placeholder.com', 'placehold.co', $slide->image);
                    DB::table('hero_slides')
                        ->where('id', $slide->id)
                        ->update(['image' => $newImage]);
                });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('hero_slides')) {
            DB::table('hero_slides')
                ->where('image', 'like', '%placehold.co%')
                ->get()
                ->each(function ($slide) {
                    $newImage = str_replace('placehold.co', 'via.placeholder.com', $slide->image);
                    DB::table('hero_slides')
                        ->where('id', $slide->id)
                        ->update(['image' => $newImage]);
                });
        }
    }
};
