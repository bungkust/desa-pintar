# Design System - Desa Donoharjo

## 📋 Overview

Design system yang konsisten untuk semua halaman website Desa Donoharjo. Semua komponen mengikuti standar layout dan styling yang sama.

## 🎨 Komponen yang Tersedia

### 1. Base Layout
**File:** `resources/views/layouts/app.blade.php`

Layout dasar untuk semua halaman dengan navbar, main content area, dan footer.

**Penggunaan:**
```blade
@extends('layouts.app')

@section('content')
    <!-- Konten -->
@endsection
```

### 2. Page Header
**File:** `resources/views/components/sections/page-header.blade.php`

Header section dengan gradient background untuk halaman detail.

**Penggunaan:**
```blade
@include('components.sections.page-header', [
    'title' => 'Judul Halaman',
    'description' => 'Deskripsi dengan <strong>HTML</strong>',
    'gradient' => 'from-blue-50 via-emerald-50 to-teal-50',
])
```

### 3. Section Wrapper
**File:** `resources/views/components/sections/section.blade.php`

Wrapper untuk section konten dengan spacing dan styling konsisten.

**Penggunaan:**
```blade
<x-sections.section title="Judul Section">
    <!-- Konten -->
</x-sections.section>
```

**Props:**
- `title` (optional) - Judul section
- `subtitle` (optional) - Subtitle section
- `background` (optional, default: `bg-white`) - Background class
- `spacing` (optional, default: `py-12 md:py-16`) - Spacing class

### 4. Stat Card
**File:** `resources/views/components/cards/stat-card.blade.php`

Card untuk menampilkan statistik dengan icon.

**Penggunaan:**
```blade
@include('components.cards.stat-card', [
    'title' => 'Judul',
    'value' => '1000',
    'label' => 'Label',
    'icon' => 'M...', // SVG path
])
```

### 5. APBDes Summary Card
**File:** `resources/views/components/cards/apbdes-summary-card.blade.php`

Card khusus untuk ringkasan APBDes dengan progress bar dan dynamic color.

**Penggunaan:**
```blade
@include('components.cards.apbdes-summary-card', [
    'title' => 'Pendapatan',
    'realisasi' => 1000000,
    'anggaran' => 1200000,
    'percentage' => 83.33,
])
```

### 6. Data Table
**File:** `resources/views/components/tables/data-table.blade.php`

Table dengan styling konsisten.

**Penggunaan:**
```blade
@include('components.tables.data-table', [
    'headers' => ['Kolom 1', 'Kolom 2'],
    'rows' => [
        ['Data 1', 'Data 2'],
    ],
])
```

### 7. Chart Container
**File:** `resources/views/components/charts/chart-container.blade.php`

Container untuk chart dengan styling konsisten.

**Penggunaan:**
```blade
@include('components.charts.chart-container', [
    'title' => 'Judul Chart',
    'chartId' => 'my-chart',
])
```

### 8. Empty State
**File:** `resources/views/components/empty-state.blade.php`

State ketika tidak ada data.

**Penggunaan:**
```blade
@include('components.empty-state', [
    'title' => 'Data Belum Tersedia',
    'message' => 'Belum ada data',
    'action' => '<a href="/">Link</a>', // optional
])
```

### 9. Back Button
**File:** `resources/views/components/buttons/back-button.blade.php`

Tombol kembali dengan styling konsisten.

**Penggunaan:**
```blade
@include('components.buttons.back-button', [
    'href' => '/',
    'label' => 'Kembali',
    'variant' => 'gray', // gray, blue, green
])
```

### 10. Year Selector
**File:** `resources/views/components/selects/year-selector.blade.php`

Selector untuk memilih tahun.

**Penggunaan:**
```blade
@include('components.selects.year-selector', [
    'currentYear' => 2025,
    'availableYears' => [2025, 2024, 2023],
    'routeName' => 'apbdes.show',
])
```

## 📐 Standard Layout

### Container
```blade
<div class="container mx-auto px-4 md:px-6 lg:px-8">
```

### Section Spacing
- Default: `py-12 md:py-16`
- Large: `py-16 md:py-20`

### Grid Layout
```blade
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
```

## 🎨 Color Palette

### Background Gradients
- Header: `bg-gradient-to-br from-blue-50 via-emerald-50 to-teal-50`
- Cards: `bg-gradient-to-br from-gray-50 to-blue-50`

### Status Colors
- Success (≥80%): Green (`text-green-600`, `bg-green-500`)
- Warning (50-79%): Orange (`text-orange-600`, `bg-orange-500`)
- Danger (<50%): Red (`text-red-600`, `bg-red-500`)

## ✅ Status Implementasi

- ✅ Base Layout (`layouts/app.blade.php`)
- ✅ Page Header Component
- ✅ Section Wrapper Component
- ✅ Stat Card Component
- ✅ APBDes Summary Card Component
- ✅ Data Table Component
- ✅ Chart Container Component
- ✅ Empty State Component
- ✅ Back Button Component
- ✅ Year Selector Component
- ✅ Footer Component (extracted)
- ✅ Navbar Component (existing)
- ✅ APBDes Page (menggunakan design system)
- ⏳ Statistik Lengkap Page (perlu diupdate)
- ⏳ Welcome Page (perlu diupdate)
- ⏳ Post Page (perlu diupdate)

## 📝 Next Steps

1. Update halaman statistik-lengkap untuk menggunakan design system
2. Update halaman welcome untuk menggunakan design system
3. Update halaman post untuk menggunakan design system
4. Buat view composer untuk menyediakan $settings dan $menuItems secara global
