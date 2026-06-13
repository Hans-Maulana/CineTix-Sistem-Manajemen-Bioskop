<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #384252;
            line-height: 1.5;
            font-size: 9pt;
            margin: 0;
            padding: 0;
        }
        .header {
            background-color: #1A1953;
            color: #ffffff;
            padding: 20px;
            text-align: center;
            border-radius: 10px;
            margin-bottom: 25px;
        }
        .logo {
            font-size: 22pt;
            font-weight: 800;
            color: #d4b06a;
            margin: 0;
            letter-spacing: 2px;
        }
        .title {
            font-size: 13pt;
            font-weight: bold;
            color: #ffffff;
            margin: 5px 0 0 0;
            text-transform: uppercase;
        }
        .meta {
            font-size: 8pt;
            color: #b5bdd0;
            margin: 5px 0 0 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background-color: #1A1953;
            color: white;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8.5pt;
            padding: 10px 12px;
            border: 1px solid #1A1953;
        }
        td {
            padding: 10px 12px;
            border-bottom: 1px solid #eef0f7;
            border-left: 1px solid #eef0f7;
            border-right: 1px solid #eef0f7;
            font-size: 9pt;
        }
        tr:nth-child(even) td {
            background-color: #fafbfd;
        }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .text-start { text-align: left; }
        .fw-bold { font-weight: bold; }
        .total-row td {
            background-color: #f3f4fa;
            font-weight: bold;
            border-top: 2px solid #1A1953;
            border-bottom: 2px solid #1A1953;
            color: #1A1953;
        }
        .footer {
            position: fixed;
            bottom: -30px;
            left: 0;
            right: 0;
            font-size: 7.5pt;
            color: #8a93a6;
            text-align: center;
            border-top: 1px solid #e6e8f0;
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
    @elseif($type === 'daily')
        <table>
            <thead>
                <tr>
                    <th class="text-start" style="width: 35%;">Tanggal</th>
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
                        <td class="text-start fw-bold">{{ \Carbon\Carbon::parse($row->booking_date)->translatedFormat('d F Y') }}</td>
                        <td class="text-center">{{ number_format($row->total_bookings, 0, ',', '.') }} Transaksi</td>
                        <td class="text-end fw-bold" style="color: #0d6efd;">Rp {{ number_format($row->total_revenue, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center" style="padding: 30px;">Belum ada data transaksi harian.</td>
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
    @elseif($type === 'detailed')
        <table>
            <thead>
                <tr>
                    <th class="text-center" style="width: 12%;">Booking ID</th>
                    <th class="text-start" style="width: 20%;">Customer</th>
                    <th class="text-start" style="width: 28%;">Film & Show</th>
                    <th class="text-center" style="width: 12%;">Kursi</th>
                    <th class="text-end" style="width: 15%;">Total Bayar</th>
                    <th class="text-center" style="width: 13%;">Waktu</th>
                </tr>
            </thead>
            <tbody>
                @php 
                    $totalRevenue = 0; 
                @endphp
                @forelse($data as $booking)
                    @php 
                        $payment = $booking->latestPayment;
                        $totalRevenue += ($payment?->amount ?? 0);
                        $schedule = $booking->ticketBookings->first()?->schedule;
                        $film = $schedule?->film;
                    @endphp
                    <tr>
                        <td class="text-center fw-bold">#{{ $booking->id }}</td>
                        <td class="text-start">
                            {{ $booking->customerName() }}
                            <br><span style="font-size: 8pt; color: #666;">({{ $booking->isGuest() ? 'Guest' : 'Member' }})</span>
                        </td>
                        <td class="text-start">
                            {{ $film?->title ?? '-' }}
                            <br><span style="font-size: 8pt; color: #666;">{{ $schedule?->studio->name ?? '-' }} &bull; {{ $schedule ? $schedule->start_time->format('H:i') : '-' }}</span>
                        </td>
                        <td class="text-center">
                            @php 
                                $seats = $booking->ticketBookings->map(function($tb) {
                                    return $tb?->seat?->seat_code;
                                })->filter()->implode(', ');
                            @endphp
                            {{ $seats }}
                        </td>
                        <td class="text-end fw-bold">Rp {{ number_format($payment?->amount ?? 0, 0, ',', '.') }}</td>
                        <td class="text-center">
                            {{ $booking->created_at->translatedFormat('d M Y') }}
                            <br><span style="font-size: 8pt; color: #666;">{{ $booking->created_at->format('H:i') }} WIB</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center" style="padding: 30px;">Belum ada detail transaksi yang tercatat.</td>
                    </tr>
                @endforelse
                @if(count($data) > 0)
                    <tr class="total-row">
                        <td colspan="4" class="text-start">TOTAL OMSET DARI TRANSAKSI DI ATAS</td>
                        <td class="text-end" style="color: #0d6efd;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
                        <td></td>
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
