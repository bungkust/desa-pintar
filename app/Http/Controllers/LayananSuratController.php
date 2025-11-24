<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Settings\GeneralSettings;
use Illuminate\Support\Facades\Cache;

class LayananSuratController
{
    public function index()
    {
        // Get Menu Items for navbar (cached)
        $menuItems = Cache::remember('menu_items', 3600, function () {
            return MenuItem::where('is_active', true)
                ->whereNull('parent_id')
                ->orderBy('order')
                ->with(['children' => function ($query) {
                    $query->where('is_active', true)
                          ->orderBy('order')
                          ->with(['children' => function ($subQuery) {
                              $subQuery->where('is_active', true)
                                       ->orderBy('order');
                          }]);
                }])
                ->get();
        });

        // Get settings (cached)
        $settings = Cache::rememberForever('general_settings', function () {
            try {
                return app(GeneralSettings::class);
            } catch (\Exception $e) {
                return (object) [
                    'site_name' => 'Pemerintah Kalurahan Donoharjo',
                    'village_address' => 'Jl. Parasamya, Donoharjo, Ngaglik, Sleman, DIY 55581',
                    'whatsapp' => '6281227666999',
                    'logo_path' => null,
                    'instagram' => null,
                ];
            }
        });

        // Dummy data for Layanan Surat
        $layananSurat = [
            [
                'nama' => 'Surat Keterangan Domisili',
                'deskripsi' => 'Surat keterangan tempat tinggal untuk keperluan administrasi',
                'syarat' => [
                    'Fotokopi KTP',
                    'Fotokopi KK',
                    'Surat pengantar RT/RW',
                ],
                'waktu' => '1-2 hari kerja',
            ],
            [
                'nama' => 'Surat Keterangan Usaha',
                'deskripsi' => 'Surat keterangan usaha untuk keperluan perizinan',
                'syarat' => [
                    'Fotokopi KTP',
                    'Fotokopi KK',
                    'Surat pengantar RT/RW',
                    'Fotokopi Izin Usaha (jika ada)',
                ],
                'waktu' => '2-3 hari kerja',
            ],
            [
                'nama' => 'Surat Keterangan Tidak Mampu (SKTM)',
                'deskripsi' => 'Surat keterangan tidak mampu untuk keperluan bantuan sosial',
                'syarat' => [
                    'Fotokopi KTP',
                    'Fotokopi KK',
                    'Surat pengantar RT/RW',
                    'Surat keterangan penghasilan',
                ],
                'waktu' => '1-2 hari kerja',
            ],
            [
                'nama' => 'Surat Keterangan Kelakuan Baik',
                'deskripsi' => 'Surat keterangan kelakuan baik untuk keperluan administrasi',
                'syarat' => [
                    'Fotokopi KTP',
                    'Fotokopi KK',
                    'Surat pengantar RT/RW',
                ],
                'waktu' => '1-2 hari kerja',
            ],
            [
                'nama' => 'Surat Keterangan Kematian',
                'deskripsi' => 'Surat keterangan kematian untuk keperluan administrasi',
                'syarat' => [
                    'Fotokopi KTP almarhum/almarhumah',
                    'Fotokopi KK',
                    'Surat keterangan dokter (jika ada)',
                    'Surat pengantar RT/RW',
                ],
                'waktu' => '1 hari kerja',
            ],
            [
                'nama' => 'Surat Pengantar KTP',
                'deskripsi' => 'Surat pengantar untuk pembuatan KTP baru atau perpanjangan',
                'syarat' => [
                    'Fotokopi KK',
                    'Pas foto 3x4 (2 lembar)',
                    'Surat pengantar RT/RW',
                ],
                'waktu' => '1 hari kerja',
            ],
        ];

        return view('layanan-surat', [
            'layananSurat' => $layananSurat,
            'menuItems' => $menuItems,
            'settings' => $settings,
            'pageTitle' => 'Layanan Surat - ' . ($settings->site_name ?? 'Desa Donoharjo'),
            'metaTitle' => 'Layanan Surat - ' . ($settings->site_name ?? 'Desa Donoharjo'),
            'metaDescription' => 'Informasi layanan surat yang tersedia di ' . ($settings->site_name ?? 'Desa Donoharjo'),
            'canonicalUrl' => url()->current(),
        ]);
    }
}

