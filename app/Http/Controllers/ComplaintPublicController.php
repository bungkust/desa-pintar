<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\MenuItem;
use App\Settings\GeneralSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ComplaintPublicController
{
    /**
     * Show complaint landing page
     */
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

        // Categories for dropdown
        $categories = [
            'infrastruktur' => 'Infrastruktur & Jalan',
            'sampah' => 'Sampah & Kebersihan',
            'air' => 'Air & Sanitasi',
            'listrik' => 'Listrik & Penerangan',
            'keamanan' => 'Keamanan & Ketertiban',
            'sosial' => 'Sosial & Kesejahteraan',
            'pendidikan' => 'Pendidikan',
            'kesehatan' => 'Kesehatan',
            'lainnya' => 'Lainnya',
        ];

        // Get statistics for transparency (cached for 5 minutes)
        $stats = Cache::remember('complaint_stats_public', 300, function () {
            return [
                'total' => Complaint::count(),
                'selesai' => Complaint::where('status', 'done')->count(),
                'sedang_diproses' => Complaint::whereIn('status', ['verification', 'todo', 'in_progress'])->count(),
            ];
        });

        return view('complaints.index', [
            'menuItems' => $menuItems,
            'settings' => $settings,
            'categories' => $categories,
            'stats' => $stats,
            'pageTitle' => 'Pengaduan Masyarakat - ' . ($settings->site_name ?? 'Desa Donoharjo'),
            'metaTitle' => 'Pengaduan Masyarakat - ' . ($settings->site_name ?? 'Desa Donoharjo'),
            'metaDescription' => 'Laporkan masalah atau keluhan Anda kepada pemerintah desa. Kami siap membantu menyelesaikan masalah Anda dengan cepat dan transparan.',
            'canonicalUrl' => url()->current(),
        ]);
    }

    /**
     * Show complaint submission form
     */
    public function create()
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

        // Categories for dropdown
        $categories = [
            'infrastruktur' => 'Infrastruktur & Jalan',
            'sampah' => 'Sampah & Kebersihan',
            'air' => 'Air & Sanitasi',
            'listrik' => 'Listrik & Penerangan',
            'keamanan' => 'Keamanan & Ketertiban',
            'sosial' => 'Sosial & Kesejahteraan',
            'pendidikan' => 'Pendidikan',
            'kesehatan' => 'Kesehatan',
            'lainnya' => 'Lainnya',
        ];

        // Get statistics for transparency (cached for 5 minutes)
        $stats = Cache::remember('complaint_stats_public', 300, function () {
            return [
                'total' => Complaint::count(),
                'selesai' => Complaint::where('status', 'done')->count(),
                'sedang_diproses' => Complaint::where('status', 'in_progress')->count(),
            ];
        });

        return view('complaints.form', [
            'menuItems' => $menuItems,
            'settings' => $settings,
            'categories' => $categories,
            'stats' => $stats,
            'pageTitle' => 'Pengaduan Masyarakat - ' . ($settings->site_name ?? 'Desa Donoharjo'),
            'metaTitle' => 'Pengaduan Masyarakat - ' . ($settings->site_name ?? 'Desa Donoharjo'),
            'metaDescription' => 'Laporkan masalah atau keluhan Anda kepada pemerintah desa. Kami siap membantu menyelesaikan masalah Anda.',
            'canonicalUrl' => url()->current(),
        ]);
    }

    /**
     * Store complaint submission
     */
    public function store(Request $request)
    {
        // Rate limiting: 3 submissions per 24h per IP/phone
        $rateLimitKey = 'complaint_submission:' . $request->ip() . ':' . md5($request->input('phone', 'no-phone'));
        
        if (RateLimiter::tooManyAttempts($rateLimitKey, 3)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            return back()
                ->withInput()
                ->withErrors(['rate_limit' => "Terlalu banyak pengajuan. Silakan coba lagi dalam " . ceil($seconds / 3600) . " jam."]);
        }

        // Honeypot field (should be empty)
        if ($request->filled('website')) {
            return back()->withInput()->withErrors(['spam' => 'Spam detected.']);
        }

        // Validation
        // Normalize numeric-only fields before validation
        $request->merge([
            'phone' => $request->filled('phone') ? preg_replace('/\D+/', '', $request->input('phone')) : $request->input('phone'),
            'rt' => $request->filled('rt') ? preg_replace('/\D+/', '', $request->input('rt')) : $request->input('rt'),
            'rw' => $request->filled('rw') ? preg_replace('/\D+/', '', $request->input('rw')) : $request->input('rw'),
        ]);

        $validator = Validator::make($request->all(), [
            'name' => ['nullable', 'string', 'max:255'],
            'phone' => ['required_if:is_anonymous,0', 'nullable', 'string', 'digits_between:10,15', 'regex:/^[0-9]+$/'],
            'address' => ['nullable', 'string', 'max:500'],
            'rt' => ['nullable', 'string', 'max:10', 'regex:/^[0-9]+$/'],
            'rw' => ['nullable', 'string', 'max:10', 'regex:/^[0-9]+$/'],
            'category' => ['required', 'string', 'in:infrastruktur,sampah,air,listrik,keamanan,sosial,pendidikan,kesehatan,lainnya'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'location_text' => ['required', 'string', 'max:500'],
            'location_lat' => ['nullable', 'numeric', 'between:-90,90'],
            'location_lng' => ['nullable', 'numeric', 'between:-180,180'],
            'images' => ['nullable', 'array', 'max:3'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg,webp', 'max:2048'], // 2MB per image
            'is_anonymous' => ['boolean'],
        ], [
            'phone.required_if' => 'Nomor telepon wajib diisi jika tidak melaporkan sebagai anonim.',
            'category.required' => 'Kategori wajib dipilih.',
            'title.required' => 'Judul pengaduan wajib diisi.',
            'description.required' => 'Deskripsi pengaduan wajib diisi.',
            'location_text.required' => 'Lokasi pengaduan wajib diisi.',
            'phone.regex' => 'Nomor WhatsApp hanya boleh berisi angka.',
            'phone.digits_between' => 'Nomor WhatsApp harus terdiri dari 10-15 digit.',
            'rt.regex' => 'RT hanya boleh berisi angka.',
            'rw.regex' => 'RW hanya boleh berisi angka.',
            'images.max' => 'Maksimal 3 gambar.',
            'images.*.max' => 'Ukuran gambar maksimal 2MB.',
        ]);

        if ($validator->fails()) {
            RateLimiter::hit($rateLimitKey, 86400); // 24 hours
            return back()->withInput()->withErrors($validator);
        }

        $validated = $validator->validated();

        // Check for duplicate complaints (within 50-100m)
        if (!empty($validated['location_lat']) && !empty($validated['location_lng'])) {
            $duplicates = Complaint::findDuplicates(
                $validated['location_lat'],
                $validated['location_lng'],
                100, // 100 meters
                7 // last 7 days
            );

            if ($duplicates->isNotEmpty()) {
                RateLimiter::hit($rateLimitKey, 86400);
                return back()
                    ->withInput()
                    ->withErrors(['duplicate' => 'Pengaduan serupa sudah pernah dilaporkan di lokasi ini. Silakan cek status pengaduan sebelumnya dengan kode tracking: ' . $duplicates->first()->tracking_code]);
            }
        }

        // Handle image uploads
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('complaints/images', 'public');
                if ($path) {
                    $imagePaths[] = $path;
                }
            }
        }

        // Create complaint
        try {
            DB::beginTransaction();

            $complaint = Complaint::create([
                'name' => $validated['is_anonymous'] ? null : ($validated['name'] ?? null),
                'phone' => $validated['is_anonymous'] ? null : ($validated['phone'] ?? null),
                'address' => $validated['is_anonymous'] ? null : ($validated['address'] ?? null),
                'rt' => $validated['rt'] ?? null,
                'rw' => $validated['rw'] ?? null,
                'category' => $validated['category'],
                'title' => $validated['title'],
                'description' => $validated['description'],
                'location_lat' => $validated['location_lat'] ?? null,
                'location_lng' => $validated['location_lng'] ?? null,
                'location_text' => $validated['location_text'],
                'status' => 'backlog',
                'is_anonymous' => $validated['is_anonymous'] ?? false,
                'images' => !empty($imagePaths) ? $imagePaths : null,
            ]);

            // Create initial status update
            $complaint->updates()->create([
                'status_from' => null,
                'status_to' => 'backlog',
                'note' => 'Pengaduan diterima dan sedang dalam antrian.',
                'updated_by' => null,
            ]);

            // Log audit
            Log::info('Complaint created', [
                'complaint_id' => $complaint->id,
                'tracking_code' => $complaint->tracking_code,
                'category' => $complaint->category,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            RateLimiter::hit($rateLimitKey, 86400); // 24 hours

            return redirect()
                ->route('complaints.track', ['code' => $complaint->tracking_code])
                ->with('success', 'Pengaduan berhasil dikirim! Kode tracking Anda: ' . $complaint->tracking_code);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Complaint creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            RateLimiter::hit($rateLimitKey, 86400);

            return back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan saat mengirim pengaduan. Silakan coba lagi.']);
        }
    }

    /**
     * Show tracking page
     */
    public function track($code)
    {
        // Validate tracking code format
        if (!preg_match('/^ADU-[A-Z0-9]{6}$/', $code)) {
            abort(404);
        }

        $complaint = Complaint::where('tracking_code', $code)->firstOrFail();

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

        // Load updates and comments
        $complaint->load(['updates.updatedBy', 'comments.user']);

        // Filter comments - only show admin comments to public (privacy)
        $publicComments = $complaint->comments->filter(function ($comment) {
            return $comment->isFromAdmin();
        });

        return view('complaints.track', [
            'complaint' => $complaint,
            'updates' => $complaint->updates,
            'comments' => $publicComments,
            'menuItems' => $menuItems,
            'settings' => $settings,
            'pageTitle' => 'Lacak Pengaduan ' . $code . ' - ' . ($settings->site_name ?? 'Desa Donoharjo'),
            'metaTitle' => 'Lacak Pengaduan ' . $code,
            'metaDescription' => 'Status pengaduan: ' . $complaint->title,
            'canonicalUrl' => url()->current(),
        ]);
    }

    /**
     * Show tracking form (to enter tracking code)
     */
    public function showTracking()
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

        return view('complaints.tracking-form', [
            'menuItems' => $menuItems,
            'settings' => $settings,
            'pageTitle' => 'Lacak Pengaduan - ' . ($settings->site_name ?? 'Desa Donoharjo'),
            'metaTitle' => 'Lacak Pengaduan - ' . ($settings->site_name ?? 'Desa Donoharjo'),
            'metaDescription' => 'Lacak status pengaduan Anda menggunakan kode tracking.',
            'canonicalUrl' => url()->current(),
        ]);
    }
}
