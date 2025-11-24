<?php

namespace Database\Seeders;

use App\Models\Agenda;
use Illuminate\Database\Seeder;

class AgendaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing agendas if any
        Agenda::truncate();

        $now = now();
        
        // Agenda 1: Rapat Koordinasi Desa (Pemerintahan) - Featured
        Agenda::create([
            'title' => 'Rapat Koordinasi Bulanan Pemerintah Desa',
            'description' => 'Rapat koordinasi bulanan yang membahas program kerja, laporan kegiatan, dan rencana pembangunan desa. Diikuti oleh perangkat desa, BPD, dan tokoh masyarakat.',
            'category' => 'pemerintahan',
            'date' => $now->copy()->addDays(3)->format('Y-m-d'),
            'start_time' => '09:00',
            'end_time' => '12:00',
            'location' => 'Aula Kantor Desa Donoharjo, Jl. Parasamya',
            'organizer' => 'Pemerintah Desa Donoharjo',
            'contact_person' => 'Anang Patri (Carik) - 0812-3456-7890',
            'google_maps_url' => 'https://maps.google.com/?q=Kantor+Desa+Donoharjo',
            'is_featured' => true,
            'is_recurring' => false,
            'recurring_type' => null,
        ]);

        // Agenda 2: Posyandu Balita (Kesehatan)
        Agenda::create([
            'title' => 'Posyandu Balita dan Lansia',
            'description' => 'Kegiatan posyandu untuk penimbangan balita, pemberian vitamin A, imunisasi, dan pemeriksaan kesehatan lansia. Gratis untuk seluruh warga desa.',
            'category' => 'kesehatan',
            'date' => $now->copy()->addDays(5)->format('Y-m-d'),
            'start_time' => '08:00',
            'end_time' => '11:00',
            'location' => 'Posyandu Melati, RT 05 RW 02',
            'organizer' => 'Kader Posyandu Melati',
            'contact_person' => 'Ibu Siti - 0813-1234-5678',
            'google_maps_url' => null,
            'is_featured' => false,
            'is_recurring' => true,
            'recurring_type' => 'monthly',
        ]);

        // Agenda 3: Kerja Bakti Lingkungan (Lingkungan) - Featured
        Agenda::create([
            'title' => 'Kerja Bakti Bersih-Bersih Lingkungan',
            'description' => 'Kegiatan gotong royong membersihkan lingkungan desa, saluran air, dan penanaman pohon. Diikuti oleh seluruh warga desa. Siapkan peralatan kebersihan seperti sapu, cangkul, dan karung sampah.',
            'category' => 'lingkungan',
            'date' => $now->copy()->addDays(7)->format('Y-m-d'),
            'start_time' => '07:00',
            'end_time' => '10:00',
            'location' => 'Seluruh Wilayah Desa Donoharjo',
            'organizer' => 'Pemerintah Desa & Karang Taruna',
            'contact_person' => 'Dani Prasetyo (Kamituwa) - 0812-9876-5432',
            'google_maps_url' => null,
            'is_featured' => true,
            'is_recurring' => false,
            'recurring_type' => null,
        ]);

        // Agenda 4: Pentas Seni Budaya (Budaya)
        Agenda::create([
            'title' => 'Pentas Seni Budaya Tradisional',
            'description' => 'Pertunjukan seni budaya tradisional menampilkan tari tradisional, wayang kulit, dan musik gamelan. Acara ini diselenggarakan dalam rangka melestarikan budaya lokal dan mempererat silaturahmi warga.',
            'category' => 'budaya',
            'date' => $now->copy()->addDays(10)->format('Y-m-d'),
            'start_time' => '19:00',
            'end_time' => '22:00',
            'location' => 'Lapangan Desa Donoharjo',
            'organizer' => 'Sanggar Seni Budaya Donoharjo',
            'contact_person' => 'Pak Joko - 0815-1111-2222',
            'google_maps_url' => null,
            'is_featured' => false,
            'is_recurring' => false,
            'recurring_type' => null,
        ]);

        // Agenda 5: Sosialisasi Program (Umum)
        Agenda::create([
            'title' => 'Sosialisasi Program Bantuan Sosial',
            'description' => 'Sosialisasi program bantuan sosial dari pemerintah pusat dan daerah. Informasi mengenai PKH, BPNT, dan program bantuan lainnya. Terbuka untuk seluruh warga desa.',
            'category' => 'umum',
            'date' => $now->copy()->addDays(12)->format('Y-m-d'),
            'start_time' => '14:00',
            'end_time' => '16:00',
            'location' => 'Aula Kantor Desa Donoharjo',
            'organizer' => 'Pemerintah Desa & Dinas Sosial',
            'contact_person' => 'Anang Patri - 0812-3456-7890',
            'google_maps_url' => 'https://maps.google.com/?q=Kantor+Desa+Donoharjo',
            'is_featured' => false,
            'is_recurring' => false,
            'recurring_type' => null,
        ]);

        // Agenda 6: Vaksinasi (Kesehatan)
        Agenda::create([
            'title' => 'Vaksinasi Gratis untuk Warga',
            'description' => 'Program vaksinasi gratis untuk warga desa. Vaksinasi COVID-19 booster dan vaksinasi rutin lainnya. Bawa KTP dan kartu vaksin sebelumnya jika ada.',
            'category' => 'kesehatan',
            'date' => $now->copy()->addDays(14)->format('Y-m-d'),
            'start_time' => '08:00',
            'end_time' => '14:00',
            'location' => 'Puskesmas Donoharjo',
            'organizer' => 'Puskesmas & Pemerintah Desa',
            'contact_person' => 'Bidan Desa - 0813-9999-8888',
            'google_maps_url' => null,
            'is_featured' => false,
            'is_recurring' => false,
            'recurring_type' => null,
        ]);

        // Agenda 7: Musyawarah Desa (Pemerintahan)
        Agenda::create([
            'title' => 'Musyawarah Desa (Musdes)',
            'description' => 'Musyawarah desa untuk membahas Rencana Kerja Pemerintah Desa (RKPD) dan Anggaran Pendapatan dan Belanja Desa (APBDes). Diikuti oleh perangkat desa, BPD, tokoh masyarakat, dan perwakilan warga.',
            'category' => 'pemerintahan',
            'date' => $now->copy()->addDays(18)->format('Y-m-d'),
            'start_time' => '09:00',
            'end_time' => '15:00',
            'location' => 'Aula Kantor Desa Donoharjo',
            'organizer' => 'Pemerintah Desa & BPD',
            'contact_person' => 'Hadi Rintoko (Lurah) - 0812-2766-6999',
            'google_maps_url' => 'https://maps.google.com/?q=Kantor+Desa+Donoharjo',
            'is_featured' => true,
            'is_recurring' => false,
            'recurring_type' => null,
        ]);

        // Agenda 8: Pasar Murah (Umum)
        Agenda::create([
            'title' => 'Pasar Murah Sembako',
            'description' => 'Pasar murah sembako dengan harga terjangkau. Tersedia beras, minyak, gula, telur, dan kebutuhan pokok lainnya. Terbuka untuk seluruh warga desa.',
            'category' => 'umum',
            'date' => $now->copy()->addDays(20)->format('Y-m-d'),
            'start_time' => '07:00',
            'end_time' => '12:00',
            'location' => 'Lapangan Desa Donoharjo',
            'organizer' => 'Pemerintah Desa & Koperasi',
            'contact_person' => 'Pak Budi - 0814-5555-6666',
            'google_maps_url' => null,
            'is_featured' => false,
            'is_recurring' => true,
            'recurring_type' => 'monthly',
        ]);

        // Agenda 9: Pelatihan Wirausaha (Umum)
        Agenda::create([
            'title' => 'Pelatihan Kewirausahaan untuk Pemuda',
            'description' => 'Pelatihan kewirausahaan untuk pemuda desa. Materi meliputi manajemen usaha, pemasaran digital, dan pengelolaan keuangan. Gratis dan terbuka untuk pemuda usia 18-35 tahun.',
            'category' => 'umum',
            'date' => $now->copy()->addDays(25)->format('Y-m-d'),
            'start_time' => '09:00',
            'end_time' => '15:00',
            'location' => 'Aula Kantor Desa Donoharjo',
            'organizer' => 'Pemerintah Desa & Dinas Koperasi',
            'contact_person' => 'Anang Patri - 0812-3456-7890',
            'google_maps_url' => 'https://maps.google.com/?q=Kantor+Desa+Donoharjo',
            'is_featured' => false,
            'is_recurring' => false,
            'recurring_type' => null,
        ]);

        // Agenda 10: Penanaman Pohon (Lingkungan)
        Agenda::create([
            'title' => 'Gerakan Penanaman 1000 Pohon',
            'description' => 'Gerakan penanaman pohon di sepanjang jalan desa dan area publik. Kegiatan ini bertujuan untuk meningkatkan kualitas udara dan keindahan lingkungan desa. Bawa cangkul dan bibit pohon jika ada.',
            'category' => 'lingkungan',
            'date' => $now->copy()->addDays(30)->format('Y-m-d'),
            'start_time' => '07:30',
            'end_time' => '10:30',
            'location' => 'Jalan Utama Desa & Area Publik',
            'organizer' => 'Pemerintah Desa & Karang Taruna',
            'contact_person' => 'Dani Prasetyo - 0812-9876-5432',
            'google_maps_url' => null,
            'is_featured' => false,
            'is_recurring' => false,
            'recurring_type' => null,
        ]);
    }
}
