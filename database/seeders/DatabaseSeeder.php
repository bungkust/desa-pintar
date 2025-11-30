<?php

namespace Database\Seeders;

use App\Models\Apbdes;
use App\Models\HeroSlide;
use App\Models\MenuItem;
use App\Models\Official;
use App\Models\Post;
use App\Models\QuickLink;
use App\Models\Statistic;
use App\Models\StatisticDetail;
use App\Settings\GeneralSettings;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed General Settings
        try {
            $settings = app(GeneralSettings::class);
            if (!isset($settings->site_name) || empty($settings->site_name)) {
            $settings->fill([
                'site_name' => 'Pemerintah Kalurahan Donoharjo',
                'village_address' => 'Jl. Parasamya, Donoharjo, Ngaglik, Sleman, DIY 55581',
                'whatsapp' => '6281227666999',
                'logo_path' => null,
                'instagram' => null,
                ]);
                $settings->save();
            }
        } catch (\Exception $e) {
            // Settings will be configured via admin panel
        }

        // Seed Hero Slides
        if (HeroSlide::count() < 2) {
            HeroSlide::create([
                'title' => 'Donoharjo Asri & Berbudaya',
                'subtitle' => 'Mewujudkan masyarakat yang sejahtera dan mandiri.',
                'image' => 'https://placehold.co/1920x1080?text=Donoharjo+Asri',
                'is_active' => true,
                'order' => 1,
            ]);

            HeroSlide::create([
                'title' => 'Pelayanan Digital',
                'subtitle' => 'Urus surat lebih mudah dan transparan.',
                'image' => 'https://placehold.co/1920x1080?text=Pelayanan+Digital',
                'is_active' => true,
                'order' => 2,
            ]);
        }

        // Seed Quick Links
        if (QuickLink::count() < 4) {
            QuickLink::create([
                'label' => 'Layanan Surat',
                'icon_class' => 'heroicon-o-document-text',
                'url' => '#',
                'color' => '#3B82F6',
                'order' => 1,
                'is_active' => true,
            ]);

            QuickLink::create([
                'label' => 'Produk Hukum',
                'icon_class' => 'heroicon-o-scale',
                'url' => '#',
                'color' => '#EF4444',
                'order' => 2,
                'is_active' => true,
            ]);

            QuickLink::create([
                'label' => 'Potensi Desa',
                'icon_class' => 'heroicon-o-shopping-bag',
                'url' => '#',
                'color' => '#10B981',
                'order' => 3,
                'is_active' => true,
            ]);

            QuickLink::create([
                'label' => 'Pengaduan',
                'icon_class' => 'heroicon-o-chat-bubble-left',
                'url' => '#',
                'color' => '#F59E0B',
                'order' => 4,
                'is_active' => true,
            ]);
        }

        // Seed Statistics
        // Update atau create statistics dengan data terbaru (Januari 2020)
        Statistic::updateOrCreate(
            ['label' => 'Penduduk'],
            [
                'value' => '10.515',
                'icon' => 'heroicon-o-users',
                'category' => 'demografi',
                'order' => 1,
            ]
        );

        Statistic::updateOrCreate(
            ['label' => 'Laki-laki'],
            [
                'value' => '5.925',
                'icon' => 'heroicon-o-user-group',
                'category' => 'demografi',
                'order' => 2,
            ]
        );

        Statistic::updateOrCreate(
            ['label' => 'Perempuan'],
            [
                'value' => '4.590',
                'icon' => 'heroicon-o-user-group',
                'category' => 'demografi',
                'order' => 3,
            ]
        );

        Statistic::updateOrCreate(
            ['label' => 'Kepala Keluarga (KK)'],
            [
                'value' => '3.264',
                'icon' => 'heroicon-o-home',
                'category' => 'demografi',
                'order' => 4,
            ]
        );

        Statistic::updateOrCreate(
            ['label' => 'Luas Wilayah'],
            [
                'value' => '560 Ha',
                'icon' => 'heroicon-o-map',
                'category' => 'geografis',
                'order' => 5,
            ]
        );

        // Seed Statistic Details (Historical Data)
        $this->seedStatisticDetails();

        // Seed Officials
        if (Official::count() < 3) {
            Official::create([
                'name' => 'Hadi Rintoko',
                'position' => 'Lurah',
                'photo' => null,
                'order' => 1,
            ]);

            Official::create([
                'name' => 'Anang Patri',
                'position' => 'Carik',
                'photo' => null,
                'order' => 2,
            ]);

            Official::create([
                'name' => 'Dani Prasetyo',
                'position' => 'Kamituwa',
                'photo' => null,
                'order' => 3,
            ]);
        }

        // Seed APBDes 2025
        if (!Apbdes::where('year', 2025)->exists()) {
            // Pendapatan
            Apbdes::create([
                'year' => 2025,
                'type' => 'pendapatan',
                'category' => 'Dana Desa',
                'amount' => 1200000000,
            ]);

            Apbdes::create([
                'year' => 2025,
                'type' => 'pendapatan',
                'category' => 'PAD',
                'amount' => 150000000,
            ]);

            // Belanja
            Apbdes::create([
                'year' => 2025,
                'type' => 'belanja',
                'category' => 'Pembangunan',
                'amount' => 800000000,
            ]);

            Apbdes::create([
                'year' => 2025,
                'type' => 'belanja',
                'category' => 'Pemberdayaan',
                'amount' => 400000000,
            ]);
        }

        // Seed Pelayanan Menu Structure
        $this->seedPelayananMenu();

        // Seed Statistik Lengkap Menu Item
        $this->seedStatistikLengkapMenu();
    }

    /**
     * Seed Statistik Lengkap menu item
     */
    private function seedStatistikLengkapMenu(): void
    {
        MenuItem::updateOrCreate(
            ['label' => 'Statistik Lengkap'],
            [
                'url' => '/statistik-lengkap',
                'type' => 'url',
                'order' => 5,
                'is_active' => true,
                'parent_id' => null,
            ]
        );
    }

    /**
     * Seed Pelayanan menu structure with submenus and posts
     */
    private function seedPelayananMenu(): void
    {
        // Check if Pelayanan menu already exists
        $pelayananMenu = MenuItem::where('label', 'Pelayanan')->whereNull('parent_id')->first();
        
        if (!$pelayananMenu) {
            // Create parent Pelayanan menu
            $pelayananMenu = MenuItem::create([
                'label' => 'Pelayanan',
                'url' => '#pelayanan',
                'type' => 'anchor',
                'order' => 10,
                'is_active' => true,
                'parent_id' => null,
            ]);
        }

        // Create posts first (we'll link to them)
        $posts = $this->createPelayananPosts();

        // Create submenu items
        $submenus = [
            [
                'label' => 'Maklumat Pelayanan',
                'url' => '/posts/maklumat-pelayanan',
                'type' => 'url',
                'order' => 1,
                'post_slug' => 'maklumat-pelayanan',
            ],
            [
                'label' => 'Akte Kelahiran',
                'url' => '/posts/persyaratan-pengajuan-akta-kelahiran',
                'type' => 'url',
                'order' => 2,
                'post_slug' => 'persyaratan-pengajuan-akta-kelahiran',
            ],
            [
                'label' => 'Akte Kematian',
                'url' => '/posts/persyaratan-pengajuan-akta-kematian',
                'type' => 'url',
                'order' => 3,
                'post_slug' => 'persyaratan-pengajuan-akta-kematian',
            ],
            [
                'label' => 'Pengantar Nikah',
                'url' => '/posts/persyaratan-pengantar-nikah',
                'type' => 'url',
                'order' => 4,
                'post_slug' => 'persyaratan-pengantar-nikah',
            ],
            [
                'label' => 'SKTM (Keterangan Tidak Mampu)',
                'url' => '/posts/persyaratan-pelayanan-surat-keterangan-tidak-mampu',
                'type' => 'url',
                'order' => 5,
                'post_slug' => 'persyaratan-pelayanan-surat-keterangan-tidak-mampu',
            ],
            [
                'label' => 'Pajak PBB',
                'url' => 'https://pbb.donoharjo.sides.id/PbbSemuaList',
                'type' => 'url',
                'order' => 6,
                'post_slug' => null,
            ],
            [
                'label' => 'Peladi Makarti / Ketenagakerjaan',
                'url' => 'https://sinkal.jogjaprov.go.id/donoharjo/layanan-ketenagakerjaan',
                'type' => 'url',
                'order' => 7,
                'post_slug' => null,
            ],
            [
                'label' => 'Survey IKM',
                'url' => 'https://docs.google.com/forms/d/e/1FAIpQLSdONOHARJO_SURVEY_IKM/viewform',
                'type' => 'url',
                'order' => 8,
                'post_slug' => null,
            ],
            [
                'label' => 'Hasil Survey IKM',
                'url' => '/posts/hasil-survey-kepuasan-masyarakat',
                'type' => 'url',
                'order' => 9,
                'post_slug' => 'hasil-survey-kepuasan-masyarakat',
            ],
            [
                'label' => 'Survey Korupsi',
                'url' => 'https://docs.google.com/forms/d/e/1FAIpQLSdONOHARJO_SURVEY_KORUPSI/viewform',
                'type' => 'url',
                'order' => 10,
                'post_slug' => null,
            ],
            [
                'label' => 'Hasil SPAK (Survei Persepsi Anti Korupsi)',
                'url' => '/posts/hasil-survey-indeks-persepsi-anti-korupsi',
                'type' => 'url',
                'order' => 11,
                'post_slug' => 'hasil-survey-indeks-persepsi-anti-korupsi',
            ],
        ];

        foreach ($submenus as $submenu) {
            MenuItem::updateOrCreate(
                [
                    'label' => $submenu['label'],
                    'parent_id' => $pelayananMenu->id,
                ],
                [
                    'url' => $submenu['url'],
                    'type' => $submenu['type'],
                    'order' => $submenu['order'],
                    'is_active' => true,
                ]
            );
        }
    }

    /**
     * Create posts for Pelayanan menu items
     */
    private function createPelayananPosts(): array
    {
        $posts = [];

        // Maklumat Pelayanan
        $posts['maklumat-pelayanan'] = Post::updateOrCreate(
            ['slug' => 'maklumat-pelayanan'],
            [
                'title' => 'Maklumat Pelayanan Desa Donoharjo',
                'content' => $this->getMaklumatPelayananContent(),
                'published_at' => now()->subDays(30),
            ]
        );

        // Akte Kelahiran
        $posts['persyaratan-pengajuan-akta-kelahiran'] = Post::updateOrCreate(
            ['slug' => 'persyaratan-pengajuan-akta-kelahiran'],
            [
                'title' => 'Persyaratan Pengajuan Akta Kelahiran',
                'content' => $this->getAkteKelahiranContent(),
                'published_at' => now()->subDays(25),
            ]
        );

        // Akte Kematian
        $posts['persyaratan-pengajuan-akta-kematian'] = Post::updateOrCreate(
            ['slug' => 'persyaratan-pengajuan-akta-kematian'],
            [
                'title' => 'Persyaratan Pengajuan Akta Kematian',
                'content' => $this->getAkteKematianContent(),
                'published_at' => now()->subDays(20),
            ]
        );

        // Pengantar Nikah
        $posts['persyaratan-pengantar-nikah'] = Post::updateOrCreate(
            ['slug' => 'persyaratan-pengantar-nikah'],
            [
                'title' => 'Persyaratan Pengantar Nikah',
                'content' => $this->getPengantarNikahContent(),
                'published_at' => now()->subDays(15),
            ]
        );

        // SKTM
        $posts['persyaratan-pelayanan-surat-keterangan-tidak-mampu'] = Post::updateOrCreate(
            ['slug' => 'persyaratan-pelayanan-surat-keterangan-tidak-mampu'],
            [
                'title' => 'Persyaratan Pelayanan Surat Keterangan Tidak Mampu (SKTM)',
                'content' => $this->getSKTMContent(),
                'published_at' => now()->subDays(10),
            ]
        );

        // Hasil Survey IKM
        $posts['hasil-survey-kepuasan-masyarakat'] = Post::updateOrCreate(
            ['slug' => 'hasil-survey-kepuasan-masyarakat'],
            [
                'title' => 'Hasil Survey Kepuasan Masyarakat (IKM)',
                'content' => $this->getHasilSurveyIKMContent(),
                'published_at' => now()->subDays(5),
            ]
        );

        // Hasil SPAK
        $posts['hasil-survey-indeks-persepsi-anti-korupsi'] = Post::updateOrCreate(
            ['slug' => 'hasil-survey-indeks-persepsi-anti-korupsi'],
            [
                'title' => 'Hasil Survey Indeks Persepsi Anti Korupsi (SPAK)',
                'content' => $this->getHasilSPAKContent(),
                'published_at' => now()->subDays(3),
            ]
        );

        return $posts;
    }

    private function getMaklumatPelayananContent(): string
    {
        return '<h2>Maklumat Pelayanan Desa Donoharjo</h2>
        <p>Dalam rangka meningkatkan kualitas pelayanan kepada masyarakat, Pemerintah Desa Donoharjo menyampaikan maklumat pelayanan sebagai berikut:</p>
        
        <h3>1. Jam Pelayanan</h3>
        <ul>
            <li>Senin - Kamis: 08:00 - 14:00 WIB</li>
            <li>Jumat: 08:00 - 11:30 WIB</li>
            <li>Sabtu: 08:00 - 12:00 WIB</li>
        </ul>
        
        <h3>2. Lokasi Pelayanan</h3>
        <p>Kantor Desa Donoharjo<br>
        Jl. Parasamya, Donoharjo, Ngaglik, Sleman, DIY 55581</p>
        
        <h3>3. Jenis Pelayanan</h3>
        <ul>
            <li>Pengurusan Surat Keterangan</li>
            <li>Pengurusan Akta Kelahiran</li>
            <li>Pengurusan Akta Kematian</li>
            <li>Pengantar Nikah</li>
            <li>Surat Keterangan Tidak Mampu (SKTM)</li>
            <li>Pelayanan Ketenagakerjaan</li>
        </ul>
        
        <h3>4. Persyaratan</h3>
        <p>Setiap jenis pelayanan memiliki persyaratan yang berbeda. Silakan lihat detail persyaratan pada menu masing-masing layanan.</p>
        
        <h3>5. Kontak</h3>
        <p>Untuk informasi lebih lanjut, silakan hubungi:<br>
        WhatsApp: 0812-2766-6999<br>
        Email: info@donoharjo.desa.id</p>
        
        <p><strong>Kami berkomitmen memberikan pelayanan terbaik untuk masyarakat Desa Donoharjo.</strong></p>';
    }

    private function getAkteKelahiranContent(): string
    {
        return '<h2>Persyaratan Pengajuan Akta Kelahiran</h2>
        <p>Berikut adalah persyaratan yang harus dipenuhi untuk mengajukan Akta Kelahiran di Desa Donoharjo:</p>
        
        <h3>Persyaratan Umum</h3>
        <ol>
            <li>Surat Keterangan Kelahiran dari Bidan/Dokter/Rumah Sakit</li>
            <li>Kartu Keluarga (KK) asli dan fotokopi</li>
            <li>KTP kedua orang tua (asli dan fotokopi)</li>
            <li>Surat Nikah orang tua (asli dan fotokopi)</li>
            <li>Surat Keterangan dari RT/RW setempat</li>
        </ol>
        
        <h3>Khusus untuk Kelahiran di Luar Rumah Sakit</h3>
        <ul>
            <li>Surat Keterangan dari Bidan/Dukun yang menolong persalinan</li>
            <li>Surat Keterangan dari 2 (dua) orang saksi</li>
            <li>Surat Keterangan dari RT/RW</li>
        </ul>
        
        <h3>Waktu Pengurusan</h3>
        <p>Akta Kelahiran dapat diurus maksimal 60 hari setelah kelahiran. Jika melebihi batas waktu, akan dikenakan biaya tambahan.</p>
        
        <h3>Biaya</h3>
        <ul>
            <li>Pengurusan dalam batas waktu: <strong>GRATIS</strong></li>
            <li>Pengurusan melebihi batas waktu: Sesuai ketentuan yang berlaku</li>
        </ul>
        
        <h3>Lokasi Pengurusan</h3>
        <p>Kantor Desa Donoharjo<br>
        Jl. Parasamya, Donoharjo, Ngaglik, Sleman, DIY 55581</p>
        
        <p><strong>Catatan:</strong> Semua dokumen harus dalam kondisi lengkap dan valid. Pastikan fotokopi dokumen jelas dan dapat dibaca.</p>';
    }

    private function getAkteKematianContent(): string
    {
        return '<h2>Persyaratan Pengajuan Akta Kematian</h2>
        <p>Berikut adalah persyaratan yang harus dipenuhi untuk mengajukan Akta Kematian di Desa Donoharjo:</p>
        
        <h3>Persyaratan yang Diperlukan</h3>
        <ol>
            <li>Surat Keterangan Kematian dari Rumah Sakit/Dokter/Bidan (jika meninggal di rumah sakit)</li>
            <li>Surat Keterangan Kematian dari RT/RW setempat</li>
            <li>Kartu Keluarga (KK) asli dan fotokopi</li>
            <li>KTP almarhum/almarhumah (asli dan fotokopi)</li>
            <li>KTP pelapor (asli dan fotokopi)</li>
            <li>Surat Keterangan dari 2 (dua) orang saksi (jika diperlukan)</li>
        </ol>
        
        <h3>Khusus untuk Kematian di Luar Rumah Sakit</h3>
        <ul>
            <li>Surat Keterangan dari Dokter/Bidan yang memeriksa</li>
            <li>Surat Keterangan dari RT/RW</li>
            <li>Surat Keterangan dari 2 (dua) orang saksi</li>
        </ul>
        
        <h3>Waktu Pengurusan</h3>
        <p>Akta Kematian sebaiknya diurus segera setelah kematian terjadi, maksimal 30 hari setelah kematian.</p>
        
        <h3>Biaya</h3>
        <p>Pengurusan Akta Kematian di Desa Donoharjo adalah <strong>GRATIS</strong>.</p>
        
        <h3>Lokasi Pengurusan</h3>
        <p>Kantor Desa Donoharjo<br>
        Jl. Parasamya, Donoharjo, Ngaglik, Sleman, DIY 55581</p>
        
        <p><strong>Catatan:</strong> Pelapor harus merupakan keluarga dekat atau pihak yang mengetahui peristiwa kematian. Semua dokumen harus dalam kondisi lengkap dan valid.</p>';
    }

    private function getPengantarNikahContent(): string
    {
        return '<h2>Persyaratan Pengantar Nikah</h2>
        <p>Berikut adalah persyaratan yang harus dipenuhi untuk mengajukan Surat Pengantar Nikah di Desa Donoharjo:</p>
        
        <h3>Persyaratan untuk Calon Pengantin Pria</h3>
        <ol>
            <li>KTP asli dan fotokopi</li>
            <li>Kartu Keluarga (KK) asli dan fotokopi</li>
            <li>Akta Kelahiran (asli dan fotokopi)</li>
            <li>Surat Keterangan Belum Menikah dari RT/RW</li>
            <li>Pas foto 3x4 (2 lembar, background merah)</li>
            <li>Surat Keterangan dari tempat kerja (jika bekerja)</li>
        </ol>
        
        <h3>Persyaratan untuk Calon Pengantin Wanita</h3>
        <ol>
            <li>KTP asli dan fotokopi</li>
            <li>Kartu Keluarga (KK) asli dan fotokopi</li>
            <li>Akta Kelahiran (asli dan fotokopi)</li>
            <li>Surat Keterangan Belum Menikah dari RT/RW</li>
            <li>Pas foto 3x4 (2 lembar, background merah)</li>
            <li>Surat Keterangan dari tempat kerja (jika bekerja)</li>
        </ol>
        
        <h3>Persyaratan Khusus</h3>
        <ul>
            <li>Jika salah satu atau kedua calon pengantin pernah menikah, wajib melampirkan Akta Cerai atau Akta Kematian pasangan</li>
            <li>Jika calon pengantin berusia di bawah 21 tahun, wajib melampirkan izin dari orang tua/wali</li>
            <li>Surat Keterangan dari RT/RW yang menyatakan bahwa calon pengantin berdomisili di Desa Donoharjo</li>
        </ul>
        
        <h3>Waktu Pengurusan</h3>
        <p>Surat Pengantar Nikah dapat diurus pada hari kerja selama jam pelayanan. Proses pengurusan biasanya memakan waktu 1-2 hari kerja.</p>
        
        <h3>Biaya</h3>
        <p>Pengurusan Surat Pengantar Nikah di Desa Donoharjo adalah <strong>GRATIS</strong>.</p>
        
        <h3>Lokasi Pengurusan</h3>
        <p>Kantor Desa Donoharjo<br>
        Jl. Parasamya, Donoharjo, Ngaglik, Sleman, DIY 55581</p>
        
        <p><strong>Catatan:</strong> Kedua calon pengantin harus hadir saat pengajuan. Pastikan semua dokumen lengkap dan valid sebelum datang ke kantor desa.</p>';
    }

    private function getSKTMContent(): string
    {
        return '<h2>Persyaratan Pelayanan Surat Keterangan Tidak Mampu (SKTM)</h2>
        <p>Berikut adalah persyaratan yang harus dipenuhi untuk mengajukan Surat Keterangan Tidak Mampu (SKTM) di Desa Donoharjo:</p>
        
        <h3>Persyaratan yang Diperlukan</h3>
        <ol>
            <li>KTP pemohon (asli dan fotokopi)</li>
            <li>Kartu Keluarga (KK) asli dan fotokopi</li>
            <li>Surat Keterangan dari RT/RW yang menyatakan kondisi ekonomi keluarga</li>
            <li>Surat Keterangan Penghasilan dari tempat kerja (jika bekerja)</li>
            <li>Surat Keterangan Tidak Bekerja dari RT/RW (jika tidak bekerja)</li>
            <li>Fotokopi rekening listrik/PDAM (3 bulan terakhir)</li>
            <li>Fotokopi tagihan telepon/internet (jika ada)</li>
        </ol>
        
        <h3>Kriteria Penerima SKTM</h3>
        <ul>
            <li>Keluarga dengan penghasilan di bawah Upah Minimum Regional (UMR)</li>
            <li>Keluarga yang tidak memiliki pekerjaan tetap</li>
            <li>Keluarga dengan anggota yang mengalami sakit kronis atau disabilitas</li>
            <li>Keluarga yang mengalami bencana atau musibah</li>
            <li>Lansia yang tidak memiliki sumber penghasilan</li>
        </ul>
        
        <h3>Kegunaan SKTM</h3>
        <p>SKTM dapat digunakan untuk:</p>
        <ul>
            <li>Mendaftar program bantuan sosial</li>
            <li>Mendapatkan keringanan biaya pendidikan</li>
            <li>Mendapatkan keringanan biaya kesehatan</li>
            <li>Mengajukan bantuan lainnya dari pemerintah</li>
        </ul>
        
        <h3>Waktu Pengurusan</h3>
        <p>SKTM dapat diurus pada hari kerja selama jam pelayanan. Proses pengurusan biasanya memakan waktu 3-5 hari kerja setelah dokumen lengkap.</p>
        
        <h3>Biaya</h3>
        <p>Pengurusan SKTM di Desa Donoharjo adalah <strong>GRATIS</strong>.</p>
        
        <h3>Lokasi Pengurusan</h3>
        <p>Kantor Desa Donoharjo<br>
        Jl. Parasamya, Donoharjo, Ngaglik, Sleman, DIY 55581</p>
        
        <p><strong>Catatan:</strong> SKTM berlaku selama 6 (enam) bulan sejak tanggal diterbitkan. Setelah itu, pemohon harus mengajukan perpanjangan jika masih memerlukan.</p>';
    }

    private function getHasilSurveyIKMContent(): string
    {
        return '<h2>Hasil Survey Kepuasan Masyarakat (IKM) Desa Donoharjo</h2>
        <p>Berikut adalah hasil Survey Indeks Kepuasan Masyarakat (IKM) yang dilaksanakan di Desa Donoharjo pada periode 2024:</p>
        
        <h3>Rangkuman Hasil Survey</h3>
        <p>Survey IKM dilaksanakan dengan melibatkan 250 responden yang terdiri dari berbagai kalangan masyarakat Desa Donoharjo.</p>
        
        <h3>Aspek yang Dinilai</h3>
        <ol>
            <li><strong>Prosedur Pelayanan</strong>: 85% responden menyatakan puas</li>
            <li><strong>Persyaratan</strong>: 82% responden menyatakan persyaratan sudah jelas</li>
            <li><strong>Kejelasan Informasi</strong>: 88% responden menyatakan informasi mudah dipahami</li>
            <li><strong>Kesopanan dan Keramahan</strong>: 90% responden menyatakan petugas ramah</li>
            <li><strong>Keadilan Mendapatkan Pelayanan</strong>: 87% responden menyatakan adil</li>
            <li><strong>Kedisiplinan Petugas</strong>: 89% responden menyatakan petugas disiplin</li>
            <li><strong>Kecepatan Pelayanan</strong>: 83% responden menyatakan pelayanan cepat</li>
            <li><strong>Kepastian Biaya</strong>: 91% responden menyatakan biaya jelas</li>
            <li><strong>Kepastian Jadwal</strong>: 86% responden menyatakan jadwal jelas</li>
        </ol>
        
        <h3>Nilai IKM Keseluruhan</h3>
        <p><strong>Nilai IKM: 86,5</strong></p>
        <p>Berdasarkan hasil survey, Indeks Kepuasan Masyarakat (IKM) Desa Donoharjo mencapai nilai <strong>86,5</strong> dari skala 100, yang menunjukkan tingkat kepuasan masyarakat yang <strong>sangat baik</strong>.</p>
        
        <h3>Rekomendasi Perbaikan</h3>
        <ul>
            <li>Meningkatkan kecepatan pelayanan dengan menambah jumlah petugas</li>
            <li>Memperjelas informasi persyaratan melalui media sosial dan website</li>
            <li>Meningkatkan sistem antrian untuk mengurangi waktu tunggu</li>
            <li>Melakukan pelatihan berkala untuk petugas pelayanan</li>
        </ul>
        
        <h3>Komitmen Pemerintah Desa</h3>
        <p>Pemerintah Desa Donoharjo berkomitmen untuk terus meningkatkan kualitas pelayanan berdasarkan hasil survey ini. Kami akan terus berinovasi dan memperbaiki sistem pelayanan untuk memberikan yang terbaik bagi masyarakat.</p>
        
        <p><strong>Terima kasih atas partisipasi masyarakat dalam survey ini. Masukan dan saran dari masyarakat sangat berarti bagi kami untuk terus berkembang.</strong></p>';
    }

    /**
     * Seed historical statistic details
     */
    private function seedStatisticDetails(): void
    {
        // Get statistics
        $penduduk = Statistic::where('label', 'Penduduk')->first();
        $kk = Statistic::where('label', 'Kepala Keluarga')->first();
        $luas = Statistic::where('label', 'Luas Wilayah')->first();

        if ($penduduk && StatisticDetail::where('statistic_id', $penduduk->id)->count() == 0) {
            // Penduduk: 2020-2025 (trend naik)
            $pendudukData = [
                ['year' => 2020, 'value' => '12.850'],
                ['year' => 2021, 'value' => '13.050'],
                ['year' => 2022, 'value' => '13.200'],
                ['year' => 2023, 'value' => '13.350'],
                ['year' => 2024, 'value' => '13.400'],
                ['year' => 2025, 'value' => '13.450'],
            ];
            foreach ($pendudukData as $data) {
                StatisticDetail::create([
                    'statistic_id' => $penduduk->id,
                    'year' => $data['year'],
                    'value' => $data['value'],
                ]);
            }
        }

        if ($kk && StatisticDetail::where('statistic_id', $kk->id)->count() == 0) {
            // Kepala Keluarga: 2020-2025 (trend naik)
            $kkData = [
                ['year' => 2020, 'value' => '4.000'],
                ['year' => 2021, 'value' => '4.050'],
                ['year' => 2022, 'value' => '4.100'],
                ['year' => 2023, 'value' => '4.150'],
                ['year' => 2024, 'value' => '4.180'],
                ['year' => 2025, 'value' => '4.200'],
            ];
            foreach ($kkData as $data) {
                StatisticDetail::create([
                    'statistic_id' => $kk->id,
                    'year' => $data['year'],
                    'value' => $data['value'],
                ]);
            }
        }

        if ($luas && StatisticDetail::where('statistic_id', $luas->id)->count() == 0) {
            // Luas Wilayah: tetap 560 Ha (tidak berubah)
            for ($year = 2020; $year <= 2025; $year++) {
                StatisticDetail::create([
                    'statistic_id' => $luas->id,
                    'year' => $year,
                    'value' => '560 Ha',
                ]);
            }
        }
    }

    private function getHasilSPAKContent(): string
    {
        return '<h2>Hasil Survey Indeks Persepsi Anti Korupsi (SPAK) Desa Donoharjo</h2>
        <p>Berikut adalah hasil Survey Persepsi Anti Korupsi (SPAK) yang dilaksanakan di Desa Donoharjo pada periode 2024:</p>
        
        <h3>Rangkuman Hasil Survey</h3>
        <p>Survey SPAK dilaksanakan dengan melibatkan 200 responden yang terdiri dari berbagai kalangan masyarakat Desa Donoharjo, termasuk tokoh masyarakat, pengusaha, dan warga biasa.</p>
        
        <h3>Aspek yang Dinilai</h3>
        <ol>
            <li><strong>Transparansi Informasi</strong>: 88% responden menyatakan informasi transparan</li>
            <li><strong>Akuntabilitas Pengelolaan Keuangan</strong>: 85% responden menyatakan akuntabel</li>
            <li><strong>Partisipasi Masyarakat</strong>: 82% responden menyatakan partisipasi baik</li>
            <li><strong>Keterbukaan Proses Pengadaan</strong>: 84% responden menyatakan proses terbuka</li>
            <li><strong>Pencegahan Konflik Kepentingan</strong>: 87% responden menyatakan tidak ada konflik</li>
            <li><strong>Penanganan Pengaduan</strong>: 83% responden menyatakan pengaduan ditangani dengan baik</li>
            <li><strong>Integritas Aparatur</strong>: 89% responden menyatakan aparatur berintegritas</li>
        </ol>
        
        <h3>Nilai SPAK Keseluruhan</h3>
        <p><strong>Nilai SPAK: 85,4</strong></p>
        <p>Berdasarkan hasil survey, Indeks Persepsi Anti Korupsi (SPAK) Desa Donoharjo mencapai nilai <strong>85,4</strong> dari skala 100, yang menunjukkan tingkat persepsi anti korupsi yang <strong>sangat baik</strong>.</p>
        
        <h3>Indikator Kunci</h3>
        <ul>
            <li><strong>Transparansi</strong>: Pemerintah Desa Donoharjo telah mempublikasikan APBDes secara rutin melalui website dan papan informasi</li>
            <li><strong>Akuntabilitas</strong>: Laporan keuangan desa dapat diakses oleh masyarakat</li>
            <li><strong>Partisipasi</strong>: Masyarakat dilibatkan dalam perencanaan pembangunan melalui musyawarah desa</li>
            <li><strong>Pengawasan</strong>: Terdapat mekanisme pengawasan yang melibatkan BPD dan masyarakat</li>
        </ul>
        
        <h3>Rekomendasi Perbaikan</h3>
        <ul>
            <li>Meningkatkan sosialisasi program anti korupsi kepada masyarakat</li>
            <li>Memperkuat sistem pengawasan internal</li>
            <li>Meningkatkan partisipasi masyarakat dalam pengawasan</li>
            <li>Melakukan pelatihan anti korupsi untuk aparatur desa</li>
        </ul>
        
        <h3>Komitmen Pemerintah Desa</h3>
        <p>Pemerintah Desa Donoharjo berkomitmen untuk terus meningkatkan tata kelola pemerintahan yang bersih, transparan, dan akuntabel. Kami akan terus berupaya mencegah praktik korupsi dan meningkatkan integritas aparatur desa.</p>
        
        <p><strong>Kami mengucapkan terima kasih kepada seluruh masyarakat yang telah berpartisipasi dalam survey ini. Komitmen kami adalah memberikan pelayanan yang bersih dan bebas dari korupsi.</strong></p>';
    }
}

