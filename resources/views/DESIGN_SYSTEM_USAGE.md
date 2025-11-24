# Design System Usage Guide

## Cara Menggunakan Design System

### 1. Extend Base Layout

Semua halaman harus extend dari `layouts.app`:

```blade
@extends('layouts.app')

@php
    $pageTitle = 'Judul Halaman - Site Name';
    $metaTitle = 'Meta Title untuk SEO';
    $metaDescription = 'Meta description untuk SEO';
    $canonicalUrl = url()->current(); // optional
@endphp

@section('content')
    <!-- Konten halaman di sini -->
@endsection
```

### 2. Menggunakan Komponen-Komponen

#### Page Header
```blade
@include('components.sections.page-header', [
    'title' => 'Judul Halaman',
    'description' => 'Deskripsi dengan <strong>HTML</strong>',
    'gradient' => 'from-blue-50 via-emerald-50 to-teal-50', // optional
])
```

#### Section Wrapper
```blade
<x-sections.section title="Judul Section" subtitle="Subtitle optional">
    <!-- Konten -->
</x-sections.section>
```

#### Stat Card
```blade
@include('components.cards.stat-card', [
    'title' => 'Judul',
    'value' => '1000',
    'label' => 'Label',
    'icon' => 'M...', // SVG path
])
```

#### APBDes Summary Card
```blade
@include('components.cards.apbdes-summary-card', [
    'title' => 'Pendapatan',
    'realisasi' => 1000000,
    'anggaran' => 1200000,
    'percentage' => 83.33,
])
```

#### Data Table
```blade
@include('components.tables.data-table', [
    'headers' => ['Kolom 1', 'Kolom 2'],
    'rows' => [
        ['Data 1', 'Data 2'],
        ['Data 3', 'Data 4'],
    ],
])
```

#### Chart Container
```blade
@include('components.charts.chart-container', [
    'title' => 'Judul Chart',
    'chartId' => 'my-chart',
])
```

#### Empty State
```blade
@include('components.empty-state', [
    'title' => 'Data Belum Tersedia',
    'message' => 'Pesan error atau empty',
    'action' => '<a href="/">Link</a>', // optional, HTML string
])
```

#### Back Button
```blade
@include('components.buttons.back-button', [
    'href' => '/',
    'label' => 'Kembali',
    'variant' => 'gray', // gray, blue, green
])
```

#### Year Selector
```blade
@include('components.selects.year-selector', [
    'currentYear' => 2025,
    'availableYears' => [2025, 2024, 2023],
    'routeName' => 'apbdes.show',
])
```

### 3. Standard Patterns

#### Grid Layout untuk Cards
```blade
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
    <!-- Cards -->
</div>
```

#### Container Standard
```blade
<div class="container mx-auto px-4 md:px-6 lg:px-8">
    <!-- Content -->
</div>
```

### 4. Update Halaman yang Ada

Semua halaman harus diupdate untuk menggunakan design system ini agar konsisten.

## Struktur File

```
resources/views/
├── layouts/
│   └── app.blade.php (base layout)
├── components/
│   ├── sections/
│   │   ├── page-header.blade.php
│   │   └── section.blade.php
│   ├── cards/
│   │   ├── stat-card.blade.php
│   │   └── apbdes-summary-card.blade.php
│   ├── tables/
│   │   └── data-table.blade.php
│   ├── charts/
│   │   └── chart-container.blade.php
│   ├── buttons/
│   │   └── back-button.blade.php
│   ├── selects/
│   │   └── year-selector.blade.php
│   ├── breadcrumb.blade.php
│   ├── empty-state.blade.php
│   ├── footer.blade.php
│   └── navbar.blade.php
└── apbdes.blade.php (contoh implementasi)
```
