<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            line-height: 1.5;
            font-size: 10pt;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .logo {
            font-size: 24pt;
            font-weight: bold;
            color: #0d6efd;
            margin: 0;
            letter-spacing: 2px;
        }
        .title {
            font-size: 16pt;
            font-weight: bold;
            color: #1e3a8a;
            margin: 5px 0;
            text-transform: uppercase;
        }
        .meta {
            font-size: 9pt;
            color: #666;
            margin: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background-color: #0d6efd;
            color: white;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9pt;
            padding: 10px 12px;
            border: 1px solid #0d6efd;
        }
        td {
            padding: 10px 12px;
            border-bottom: 1px solid #e0e0e0;
            border-left: 1px solid #e0e0e0;
            border-right: 1px solid #e0e0e0;
            font-size: 10pt;
        }
        tr:nth-child(even) td {
            background-color: #f8f9fa;
        }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .text-start { text-align: left; }
        .fw-bold { font-weight: bold; }
        .total-row td {
            background-color: #e9ecef;
            font-weight: bold;
            border-top: 2px solid #0d6efd;
            border-bottom: 2px solid #0d6efd;
            color: #000;
        }
        .footer {
            position: fixed;
            bottom: -30px;
            left: 0;
            right: 0;
            font-size: 8pt;
            color: #888;
            text-align: center;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .page-number:after { content: counter(page); }
    </style>
</head>
<body>

    <div class="header">
        <h1 class="logo">CINETIX</h1>
        <h2 class="title">{{ $title }}</h2>
        <p class="meta">Tanggal Cetak: {{ date('d M Y, H:i') }} WIB | Sistem Manajemen Bioskop</p>
    </div>

    @if($type === 'monthly')
        <table>
            <thead>
                <tr>
                    <th class="text-start" style="width: 35%;">Bulan & Tahun</th>
                    <th class="text-center" style="width: 30%;">Total Transaksi</th>
                    <th class="text-end" style="width: 35%;">Total Omset Pendapatan</th>
                </tr>
            </thead>
            <tbody>
                @php 
                    $totalBookings = 0; 
                    $totalRevenue = 0; 
                @endphp
                @forelse($data as $row)
                    @php 
                        $totalBookings += $row->total_bookings; 
                        $totalRevenue += $row->total_revenue; 
                    @endphp
                    <tr>
                        <td class="text-start fw-bold">{{ \Carbon\Carbon::parse($row->month_year . '-01')->translatedFormat('F Y') }}</td>
                        <td class="text-center">{{ number_format($row->total_bookings, 0, ',', '.') }} Transaksi</td>
                        <td class="text-end fw-bold" style="color: #0d6efd;">Rp {{ number_format($row->total_revenue, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center" style="padding: 30px;">Belum ada data transaksi bulanan.</td>
                    </tr>
                @endforelse
                @if(count($data) > 0)
                    <tr class="total-row">
                        <td class="text-start">TOTAL KESELURUHAN</td>
                        <td class="text-center">{{ number_format($totalBookings, 0, ',', '.') }} Transaksi</td>
                        <td class="text-end" style="color: #0d6efd;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
                    </tr>
                @endif
            </tbody>
        </table>
    @else
        <table>
            <thead>
                <tr>
                    <th class="text-center" style="width: 15%;">ID Film</th>
                    <th class="text-start" style="width: 40%;">Judul Film</th>
                    <th class="text-center" style="width: 20%;">Tiket Terjual</th>
                    <th class="text-end" style="width: 25%;">Total Pendapatan</th>
                </tr>
            </thead>
            <tbody>
                @php 
                    $totalTickets = 0; 
                    $totalRevenue = 0; 
                @endphp
                @forelse($data as $row)
                    @php 
                        $totalTickets += $row->tickets_sold; 
                        $totalRevenue += ($row->total_revenue ?? 0); 
                    @endphp
                    <tr>
                        <td class="text-center fw-bold" style="color: #666;">#{{ $row->id }}</td>
                        <td class="text-start fw-bold">{{ $row->title }}</td>
                        <td class="text-center">{{ number_format($row->tickets_sold, 0, ',', '.') }} Tiket</td>
                        <td class="text-end fw-bold" style="color: #0d6efd;">Rp {{ number_format($row->total_revenue ?? 0, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center" style="padding: 30px;">Belum ada data penjualan film.</td>
                    </tr>
                @endforelse
                @if(count($data) > 0)
                    <tr class="total-row">
                        <td colspan="2" class="text-start">TOTAL KESELURUHAN</td>
                        <td class="text-center">{{ number_format($totalTickets, 0, ',', '.') }} Tiket</td>
                        <td class="text-end" style="color: #0d6efd;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
                    </tr>
                @endif
            </tbody>
        </table>
    @endif

    <div class="footer">
        CineTix Cinema Management System &copy; {{ date('Y') }} &bull; Halaman <span class="page-number"></span>
    </div>

</body>
</html>
