# Design System - Desa Donoharjo

## Overview
Design system yang konsisten untuk semua halaman website Desa Donoharjo.

## Layout Structure

### Base Layout
```blade
@extends('layouts.app')
```

**Props yang bisa digunakan:**
- `$pageTitle` - Judul halaman
- `$metaTitle` - Meta title untuk SEO
- `$metaDescription` - Meta description untuk SEO
- `$canonicalUrl` - Canonical URL
- `$menuItems` - Menu items untuk navbar
- `$settings` - General settings

**Stacks:**
- `@stack('styles')` - Untuk custom CSS per halaman
- `@stack('scripts')` - Untuk custom JS per halaman

## Komponen-Komponen

### 1. Page Header
**File:** `components/sections/page-header.blade.php`

Header section dengan gradient background untuk halaman detail.

```blade
@include('components.sections.page-header', [
    'title' => 'Judul Halaman',
    'description' => 'Deskripsi singkat',
    'gradient' => 'from-blue-50 via-emerald-50 to-teal-50', // optional
    'actions' => '<button>Action</button>', // optional
])
```

### 2. Section Wrapper
**File:** `components/sections/section.blade.php`

Wrapper untuk section konten dengan spacing dan styling konsisten.

```blade
<x-sections.section title="Judul Section" subtitle="Subtitle optional">
    <!-- Konten -->
</x-sections.section>
```

**Props:**
- `title` - Judul section (optional)
- `subtitle` - Subtitle section (optional)
- `background` - Background class (default: `bg-white`)
- `spacing` - Spacing class (default: `py-12 md:py-16`)

### 3. Stat Card
**File:** `components/cards/stat-card.blade.php`

Card untuk menampilkan statistik dengan icon.

```blade
@include('components.cards.stat-card', [
    'title' => 'Judul',
    'value' => '1000',
    'label' => 'Label',
    'icon' => 'M...', // SVG path
    'iconColor' => 'text-emerald-600',
    'gradient' => 'from-gray-50 to-blue-50',
])
```

### 4. APBDes Summary Card
**File:** `components/cards/apbdes-summary-card.blade.php`

Card khusus untuk ringkasan APBDes dengan progress bar.

```blade
@include('components.cards.apbdes-summary-card', [
    'title' => 'Pendapatan',
    'realisasi' => 1000000,
    'anggaran' => 1200000,
    'percentage' => 83.33,
])
```

### 5. Data Table
**File:** `components/tables/data-table.blade.php`

Table dengan styling konsisten.

```blade
@include('components.tables.data-table', [
    'headers' => ['Kolom 1', 'Kolom 2'],
    'rows' => [
        ['Data 1', 'Data 2'],
        ['Data 3', 'Data 4'],
    ],
    'headerBg' => 'bg-emerald-50', // optional
    'emptyMessage' => 'Belum ada data', // optional
])
```

### 6. Chart Container
**File:** `components/charts/chart-container.blade.php`

Container untuk chart dengan styling konsisten.

```blade
@include('components.charts.chart-container', [
    'title' => 'Judul Chart',
    'chartId' => 'my-chart',
    'height' => 'h-64 md:h-80', // optional
])
```

### 7. Breadcrumb
**File:** `components/breadcrumb.blade.php`

Navigasi breadcrumb.

```blade
@include('components.breadcrumb', [
    'items' => [
        ['label' => 'Beranda', 'url' => '/'],
        ['label' => 'Transparansi', 'url' => '/#transparansi'],
        ['label' => 'APBDes'], // tanpa url untuk item aktif
    ],
])
```

### 8. Back Button
**File:** `components/buttons/back-button.blade.php`

Tombol kembali dengan styling konsisten.

```blade
@include('components.buttons.back-button', [
    'href' => '/',
    'label' => 'Kembali',
    'variant' => 'gray', // gray, blue, green
])
```

### 9. Empty State
**File:** `components/empty-state.blade.php`

State ketika tidak ada data.

```blade
@include('components.empty-state', [
    'title' => 'Data Belum Tersedia',
    'message' => 'Belum ada data yang tersedia.',
    'icon' => '<svg>...</svg>', // optional
    'action' => '<a href="#">Link</a>', // optional
])
```

### 10. Year Selector
**File:** `components/selects/year-selector.blade.php`

Selector untuk memilih tahun.

```blade
@include('components.selects.year-selector', [
    'currentYear' => 2025,
    'availableYears' => [2025, 2024, 2023],
    'routeName' => 'apbdes.show', // optional
    'label' => 'Pilih Tahun:', // optional
])
```

## Spacing Standards

### Section Spacing
- Default: `py-12 md:py-16`
- Large: `py-16 md:py-20`
- Small: `py-8 md:py-12`

### Container
- Standard: `container mx-auto px-4 md:px-6 lg:px-8`

### Grid Gaps
- Small: `gap-4 md:gap-6`
- Medium: `gap-6 md:gap-8`
- Large: `gap-8 md:gap-10`

## Typography Standards

### Headings
- H1 (Page Title): `text-3xl md:text-4xl lg:text-5xl font-bold`
- H2 (Section Title): `text-2xl md:text-3xl lg:text-4xl font-bold`
- H3 (Subsection Title): `text-xl md:text-2xl font-bold`
- H4 (Small Title): `text-lg font-semibold`

### Colors
- Primary Text: `text-gray-900`
- Secondary Text: `text-gray-600`
- Muted Text: `text-gray-500`

## Color Palette

### Backgrounds
- White: `bg-white`
- Light Gradient: `bg-gradient-to-br from-blue-50 via-emerald-50 to-teal-50`
- Card Gradient: `bg-gradient-to-br from-gray-50 to-blue-50`

### Accent Colors
- Emerald: `text-emerald-600`, `bg-emerald-50`
- Blue: `text-blue-600`, `bg-blue-50`
- Green (Success): `text-green-600`, `bg-green-500`
- Orange (Warning): `text-orange-600`, `bg-orange-500`
- Red (Danger): `text-red-600`, `bg-red-500`

## Shadow & Border Standards

### Cards
- Default: `shadow-md`
- Hover: `hover:shadow-lg`
- Large: `shadow-lg`
- Border: `border border-gray-100`
- Radius: `rounded-xl` atau `rounded-2xl`

### Tables
- Border: `border border-gray-200 rounded-lg`
- Header: `bg-emerald-50`

## Responsive Breakpoints

- Mobile: Default (base)
- Tablet: `md:` (768px+)
- Desktop: `lg:` (1024px+)
- Large Desktop: `xl:` (1280px+)

## Best Practices

1. **Selalu gunakan layout base:**
   ```blade
   @extends('layouts.app')
   ```

2. **Gunakan komponen yang sudah ada** daripada membuat markup manual

3. **Konsisten dengan spacing** - gunakan standard spacing yang sudah ditetapkan

4. **Responsive design** - selalu test di mobile, tablet, dan desktop

5. **Accessibility** - gunakan semantic HTML dan ARIA labels

6. **SEO** - selalu sertakan meta tags dan canonical URL
