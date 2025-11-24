<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::connection()->getDriverName();
        $hasRealisasi = Schema::hasColumn('apbdes', 'realisasi');
        $hasAnggaran = Schema::hasColumn('apbdes', 'anggaran');
        
        // If columns already exist, skip migration (they were added in a previous failed run)
        if ($hasRealisasi && $hasAnggaran) {
            return;
        }
        
        if ($driver === 'sqlite') {
            // SQLite: Just add columns, enum is handled by application layer
            Schema::table('apbdes', function (Blueprint $table) use ($hasRealisasi, $hasAnggaran) {
                // Add new columns only if they don't exist - SQLite doesn't support 'after', so just add them
                if (!$hasRealisasi) {
                    $table->bigInteger('realisasi')->default(0);
                }
                if (!$hasAnggaran) {
                    $table->bigInteger('anggaran')->default(0);
                }
            });
            
            // Make amount nullable by recreating table (SQLite limitation)
            // For simplicity, we'll keep amount as is and just add new columns
            // The enum constraint is already handled by Laravel validation
        } else {
            // MySQL/MariaDB: Full support for MODIFY
            Schema::table('apbdes', function (Blueprint $table) use ($hasRealisasi, $hasAnggaran) {
                // Make amount nullable for backward compatibility
                if (Schema::hasColumn('apbdes', 'amount')) {
                    $table->bigInteger('amount')->nullable()->change();
                }
                
                // Add new columns
                if (!$hasRealisasi) {
                    $table->bigInteger('realisasi')->default(0)->after('category');
                }
                if (!$hasAnggaran) {
                    $table->bigInteger('anggaran')->default(0)->after('realisasi');
                }
            });

            // Modify enum to include 'pembiayaan'
            try {
                DB::statement("ALTER TABLE apbdes MODIFY COLUMN type ENUM('pendapatan', 'belanja', 'pembiayaan') NOT NULL");
            } catch (\Exception $e) {
                // Ignore if already modified or not supported
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::connection()->getDriverName();
        
        Schema::table('apbdes', function (Blueprint $table) {
            $table->dropColumn(['realisasi', 'anggaran']);
        });

        if ($driver !== 'sqlite') {
            // MySQL/MariaDB: Revert enum back to original
            Schema::table('apbdes', function (Blueprint $table) {
                $table->bigInteger('amount')->nullable(false)->change();
            });
            
            DB::statement("ALTER TABLE apbdes MODIFY COLUMN type ENUM('pendapatan', 'belanja') NOT NULL");
        }
    }
};
