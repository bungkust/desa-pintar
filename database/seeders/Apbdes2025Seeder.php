<?php

namespace Database\Seeders;

use App\Models\Apbdes;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class Apbdes2025Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $year = 2025;
        
        // Hapus data lama tahun 2025 jika ada
        Apbdes::where('year', $year)->delete();
        
        // Data Pendapatan (Rincian Detail per Sumber)
        $pendapatanData = [
            ['Hasil Usaha Desa', 83310352, 240000000],
            ['Hasil Aset Desa', 109732252, 180800000],
            ['Dana Desa', 1788120000, 1788120000],
            ['Bagi Hasil Pajak & Retribusi', 369857259, 336926591],
            ['Alokasi Dana Desa', 1486116487, 1624646000],
            ['Bantuan Keuangan Provinsi', 1115000000, 1070000000],
            ['Bantuan Keuangan Kabupaten/Kota', 2025000000, 1845000000],
            ['Penerimaan Kerjasama Antar Desa', 0, 15000000],
            ['Bunga Bank', 0, 12000000],
        ];
        
        foreach ($pendapatanData as $data) {
            Apbdes::create([
                'year' => $year,
                'type' => 'pendapatan',
                'category' => $data[0],
                'realisasi' => $data[1],
                'anggaran' => $data[2],
                'amount' => null, // Legacy field
            ]);
        }
        
        // Data Belanja (Rincian per Bidang)
        $belanjaData = [
            ['Penyelenggaraan Pemerintahan Desa', 2192503302, 2656022053],
            ['Pelaksanaan Pembangunan Desa', 3175682832, 3624895000],
            ['Pembinaan Kemasyarakatan', 319047500, 516708500],
            ['Pemberdayaan Masyarakat', 78531500, 273188100],
            ['Penanggulangan Bencana/Darurat', 165000000, 195000000],
        ];
        
        foreach ($belanjaData as $data) {
            Apbdes::create([
                'year' => $year,
                'type' => 'belanja',
                'category' => $data[0],
                'realisasi' => $data[1],
                'anggaran' => $data[2],
                'amount' => null, // Legacy field
            ]);
        }
        
        // Data Pembiayaan
        // Total pembiayaan dari summary: Realisasi 153,321,062 | Anggaran 153,321,062
        Apbdes::create([
            'year' => $year,
            'type' => 'pembiayaan',
            'category' => 'Pembiayaan',
            'realisasi' => 153321062,
            'anggaran' => 153321062,
            'amount' => null, // Legacy field
        ]);
        
        $this->command->info('Data APBDes 2025 berhasil di-seed!');
        $this->command->info('Pendapatan: ' . count($pendapatanData) . ' items');
        $this->command->info('Belanja: ' . count($belanjaData) . ' items');
        $this->command->info('Pembiayaan: 1 item');
    }
}
