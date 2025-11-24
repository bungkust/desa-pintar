<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pengaduan Masyarakat - {{ $month }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        h1 {
            color: #059669;
            font-size: 24px;
            margin-bottom: 10px;
        }
        h2 {
            color: #047857;
            font-size: 18px;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #059669;
            color: white;
        }
        .stats {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        .stat-box {
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 4px;
            flex: 1;
        }
        .stat-label {
            font-size: 10px;
            color: #666;
        }
        .stat-value {
            font-size: 20px;
            font-weight: bold;
            color: #059669;
        }
    </style>
</head>
<body>
    <h1>Laporan Pengaduan Masyarakat</h1>
    <p>Periode: {{ $startDate->format('d F Y') }} - {{ $endDate->format('d F Y') }}</p>
    
    <div class="stats">
        <div class="stat-box">
            <div class="stat-label">Total Pengaduan</div>
            <div class="stat-value">{{ $stats['total'] }}</div>
        </div>
        <div class="stat-box">
            <div class="stat-label">Selesai</div>
            <div class="stat-value">{{ $stats['completed'] }}</div>
        </div>
        <div class="stat-box">
            <div class="stat-label">Pending</div>
            <div class="stat-value">{{ $stats['pending'] }}</div>
        </div>
    </div>

    <h2>Pengaduan Berdasarkan Kategori</h2>
    <table>
        <thead>
            <tr>
                <th>Kategori</th>
                <th>Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach($stats['by_category'] as $category => $count)
                <tr>
                    <td>{{ ucfirst($category) }}</td>
                    <td>{{ $count }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Daftar Pengaduan</h2>
    <table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Judul</th>
                <th>Kategori</th>
                <th>Status</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($complaints as $complaint)
                <tr>
                    <td>{{ $complaint->tracking_code }}</td>
                    <td>{{ $complaint->title }}</td>
                    <td>{{ ucfirst($complaint->category) }}</td>
                    <td>{{ ucfirst($complaint->status) }}</td>
                    <td>{{ $complaint->created_at->format('d/m/Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

