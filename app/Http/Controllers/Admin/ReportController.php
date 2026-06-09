<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Film;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Get filters from request
        $filmId = $request->get('film_id');
        $year = $request->get('year');
        $month = $request->get('month');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Base query for confirmed bookings
        $bookingQuery = Booking::where('status', 'confirmed');

        // Apply filters
        if ($filmId) {
            $bookingQuery->whereHas('ticketBookings.schedule', function($q) use ($filmId) {
                $q->where('film_id', $filmId);
            });
        }

        if ($startDate && $endDate) {
            $bookingQuery->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        } elseif ($year) {
            $bookingQuery->whereYear('created_at', $year);
            if ($month) {
                $bookingQuery->whereMonth('created_at', $month);
            }
        }

        // 1. Rekap Keseluruhan (Filtered)
        $totalRevenue = $bookingQuery->sum('total_amount');
        $totalBookings = $bookingQuery->count();
        
        // Sum total tickets sold for filtered bookings
        $totalTicketsQuery = DB::table('ticket_bookings')
            ->join('bookings', 'ticket_bookings.booking_id', '=', 'bookings.id')
            ->where('bookings.status', 'confirmed');
            
        if ($filmId) {
            $totalTicketsQuery->join('schedules', 'ticket_bookings.schedule_id', '=', 'schedules.id')
                ->where('schedules.film_id', $filmId);
        }
        if ($startDate && $endDate) {
            $totalTicketsQuery->whereBetween('bookings.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        } elseif ($year) {
            $totalTicketsQuery->whereYear('bookings.created_at', $year);
            if ($month) {
                $totalTicketsQuery->whereMonth('bookings.created_at', $month);
            }
        }
        $totalTickets = $totalTicketsQuery->count();

        // 2. Rekap per Film (Filtered)
        $filmReportsQuery = Film::select('films.id', 'films.title')
            ->withCount(['schedules as tickets_sold' => function ($query) use ($startDate, $endDate, $year, $month) {
                $query->join('ticket_bookings', 'schedules.id', '=', 'ticket_bookings.schedule_id')
                      ->join('bookings', 'ticket_bookings.booking_id', '=', 'bookings.id')
                      ->where('bookings.status', 'confirmed');
                      
                if ($startDate && $endDate) {
                    $query->whereBetween('bookings.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
                } elseif ($year) {
                    $query->whereYear('bookings.created_at', $year);
                    if ($month) {
                        $query->whereMonth('bookings.created_at', $month);
                    }
                }
            }])
            ->withSum(['schedules as total_revenue' => function ($query) use ($startDate, $endDate, $year, $month) {
                $query->join('ticket_bookings', 'schedules.id', '=', 'ticket_bookings.schedule_id')
                      ->join('bookings', 'ticket_bookings.booking_id', '=', 'bookings.id')
                      ->where('bookings.status', 'confirmed');
                      
                if ($startDate && $endDate) {
                    $query->whereBetween('bookings.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
                } elseif ($year) {
                    $query->whereYear('bookings.created_at', $year);
                    if ($month) {
                        $query->whereMonth('bookings.created_at', $month);
                    }
                }
            }], 'ticket_bookings.price_at_sale');

        if ($filmId) {
            $filmReportsQuery->where('films.id', $filmId);
        }
        $filmReports = $filmReportsQuery->get()->filter(function ($film) {
            return $film->tickets_sold > 0;
        });

        // 3. Rekap per Bulan & Tahun (Filtered)
        $monthlyReportsQuery = Booking::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month_year'),
                DB::raw('COUNT(id) as total_bookings'),
                DB::raw('SUM(total_amount) as total_revenue')
            )
            ->where('status', 'confirmed');
            
        if ($filmId) {
            $monthlyReportsQuery->whereHas('ticketBookings.schedule', function($q) use ($filmId) {
                $q->where('film_id', $filmId);
            });
        }
        if ($startDate && $endDate) {
            $monthlyReportsQuery->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        } elseif ($year) {
            $monthlyReportsQuery->whereYear('created_at', $year);
            if ($month) {
                $monthlyReportsQuery->whereMonth('created_at', $month);
            }
        }
        
        $monthlyReports = $monthlyReportsQuery->groupBy('month_year')
            ->orderBy('month_year', 'desc')
            ->get();

        // 4. Rekap Harian (Filtered)
        $dailyReportsQuery = Booking::select(
                DB::raw('DATE(created_at) as booking_date'),
                DB::raw('COUNT(id) as total_bookings'),
                DB::raw('SUM(total_amount) as total_revenue')
            )
            ->where('status', 'confirmed');

        if ($filmId) {
            $dailyReportsQuery->whereHas('ticketBookings.schedule', function($q) use ($filmId) {
                $q->where('film_id', $filmId);
            });
        }
        if ($startDate && $endDate) {
            $dailyReportsQuery->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        } elseif ($year) {
            $dailyReportsQuery->whereYear('created_at', $year);
            if ($month) {
                $dailyReportsQuery->whereMonth('created_at', $month);
            }
        } else {
            // Default: past 30 days
            $dailyReportsQuery->where('created_at', '>=', now()->subDays(30));
        }

        $dailyReports = $dailyReportsQuery->groupBy('booking_date')
            ->orderBy('booking_date', 'desc')
            ->get();

        // 5. Detail Transaksi (Filtered & Paginated)
        $detailedBookingsQuery = Booking::with([
            'user', 
            'ticketBookings.schedule.film', 
            'latestPayment'
        ])->where('status', 'confirmed');

        if ($filmId) {
            $detailedBookingsQuery->whereHas('ticketBookings.schedule', function($q) use ($filmId) {
                $q->where('film_id', $filmId);
            });
        }

        if ($startDate && $endDate) {
            $detailedBookingsQuery->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        } elseif ($year) {
            $detailedBookingsQuery->whereYear('created_at', $year);
            if ($month) {
                $detailedBookingsQuery->whereMonth('created_at', $month);
            }
        }

        $detailedBookings = $detailedBookingsQuery->latest('created_at')->paginate(10)->withQueryString();

        // Get dropdown lists
        $films = Film::all();
        
        $availableYears = Booking::select(DB::raw('YEAR(created_at) as year'))
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->filter()
            ->toArray();

        // Fallback if years is empty
        if (empty($availableYears)) {
            $availableYears = [date('Y')];
        }

        return view('admin.reports.index', compact(
            'totalRevenue', 
            'totalTickets', 
            'totalBookings', 
            'filmReports', 
            'monthlyReports',
            'dailyReports',
            'detailedBookings',
            'films',
            'availableYears',
            'filmId',
            'year',
            'month',
            'startDate',
            'endDate'
        ));
    }

    public function export(Request $request)
    {
        $type = $request->get('type', 'film'); // 'film', 'monthly', or 'daily'
        $format = $request->get('format', 'excel'); // 'excel' or 'pdf'
        
        $filmId = $request->get('film_id');
        $year = $request->get('year');
        $month = $request->get('month');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $titleSuffix = "";
        if ($startDate && $endDate) {
            $titleSuffix = " Periode " . date('d M Y', strtotime($startDate)) . " s/d " . date('d M Y', strtotime($endDate));
        } elseif ($year) {
            if ($month) {
                $titleSuffix = " Bulan " . \Carbon\Carbon::parse("$year-$month-01")->translatedFormat('F Y');
            } else {
                $titleSuffix = " Tahun " . $year;
            }
        }

        if ($type === 'detailed') {
            $query = Booking::with(['user', 'ticketBookings.schedule.film', 'latestPayment'])
                ->where('status', 'confirmed');

            if ($filmId) {
                $query->whereHas('ticketBookings.schedule', function($q) use ($filmId) {
                    $q->where('film_id', $filmId);
                });
            }
            if ($startDate && $endDate) {
                $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            } elseif ($year) {
                $query->whereYear('created_at', $year);
                if ($month) {
                    $query->whereMonth('created_at', $month);
                }
            }

            $data = $query->latest('created_at')->get();
            $title = "Laporan Detail Transaksi Penjualan" . $titleSuffix;
            $filenameBase = "laporan_detail_transaksi_" . date('Ymd');
        } elseif ($type === 'monthly') {
            $query = Booking::select(
                    DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month_year'),
                    DB::raw('COUNT(id) as total_bookings'),
                    DB::raw('SUM(total_amount) as total_revenue')
                )
                ->where('status', 'confirmed');

            if ($filmId) {
                $query->whereHas('ticketBookings.schedule', function($q) use ($filmId) {
                    $q->where('film_id', $filmId);
                });
            }
            if ($startDate && $endDate) {
                $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            } elseif ($year) {
                $query->whereYear('created_at', $year);
                if ($month) {
                    $query->whereMonth('created_at', $month);
                }
            }

            $data = $query->groupBy('month_year')
                ->orderBy('month_year', 'desc')
                ->get();

            $title = "Laporan Pendapatan Bulanan" . $titleSuffix;
            $filenameBase = "laporan_pendapatan_bulanan_" . date('Ymd');
        } elseif ($type === 'daily') {
            $query = Booking::select(
                    DB::raw('DATE(created_at) as booking_date'),
                    DB::raw('COUNT(id) as total_bookings'),
                    DB::raw('SUM(total_amount) as total_revenue')
                )
                ->where('status', 'confirmed');

            if ($filmId) {
                $query->whereHas('ticketBookings.schedule', function($q) use ($filmId) {
                    $q->where('film_id', $filmId);
                });
            }
            if ($startDate && $endDate) {
                $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            } elseif ($year) {
                $query->whereYear('created_at', $year);
                if ($month) {
                    $query->whereMonth('created_at', $month);
                }
            } else {
                $query->where('created_at', '>=', now()->subDays(30));
            }

            $data = $query->groupBy('booking_date')
                ->orderBy('booking_date', 'desc')
                ->get();

            $title = "Laporan Pendapatan Harian" . $titleSuffix;
            $filenameBase = "laporan_pendapatan_harian_" . date('Ymd');
        } else {
            // Default: export per film
            $query = Film::select('films.id', 'films.title')
                ->withCount(['schedules as tickets_sold' => function ($q) use ($startDate, $endDate, $year, $month) {
                    $q->join('ticket_bookings', 'schedules.id', '=', 'ticket_bookings.schedule_id')
                      ->join('bookings', 'ticket_bookings.booking_id', '=', 'bookings.id')
                      ->where('bookings.status', 'confirmed');
                      
                    if ($startDate && $endDate) {
                        $q->whereBetween('bookings.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
                    } elseif ($year) {
                        $q->whereYear('bookings.created_at', $year);
                        if ($month) {
                            $q->whereMonth('bookings.created_at', $month);
                        }
                    }
                }])
                ->withSum(['schedules as total_revenue' => function ($q) use ($startDate, $endDate, $year, $month) {
                    $q->join('ticket_bookings', 'schedules.id', '=', 'ticket_bookings.schedule_id')
                      ->join('bookings', 'ticket_bookings.booking_id', '=', 'bookings.id')
                      ->where('bookings.status', 'confirmed');
                      
                    if ($startDate && $endDate) {
                        $q->whereBetween('bookings.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
                    } elseif ($year) {
                        $q->whereYear('bookings.created_at', $year);
                        if ($month) {
                            $q->whereMonth('bookings.created_at', $month);
                        }
                    }
                }], 'ticket_bookings.price_at_sale');

            if ($filmId) {
                $query->where('films.id', $filmId);
            }

            $data = $query->get();

            $title = "Laporan Penjualan Tiket per Film" . $titleSuffix;
            $filenameBase = "laporan_penjualan_per_film_" . date('Ymd');
        }

        if ($format === 'pdf') {
            return $this->exportPdf($data, $type, $title, $filenameBase . '.pdf');
        }

        return $this->exportExcel($data, $type, $title, $filenameBase . '.xlsx');
    }

    private function exportExcel($data, $type, $title, $filename)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(substr($title, 0, 30));

        // Styling definitions
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0D6EFD']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]],
        ];

        $titleStyle = [
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '1E3A8A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ];

        $metaStyle = [
            'font' => ['size' => 10, 'italic' => true, 'color' => ['rgb' => '666666']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ];

        $borderStyle = [
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E0E0E0']]],
        ];

        $totalStyle = [
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '000000']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8F9FA']],
            'borders' => [
                'top' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                'bottom' => ['borderStyle' => Border::BORDER_DOUBLE, 'color' => ['rgb' => '000000']],
            ],
        ];

        if ($type === 'monthly') {
            // Title Block
            $sheet->mergeCells('A1:C1');
            $sheet->setCellValue('A1', strtoupper($title));
            $sheet->getStyle('A1:C1')->applyFromArray($titleStyle);
            $sheet->getRowDimension(1)->setRowHeight(35);

            // Subtitle / Meta
            $sheet->mergeCells('A2:C2');
            $sheet->setCellValue('A2', 'Tanggal Cetak: ' . date('d M Y, H:i') . ' WIB | Sistem Manajemen Bioskop CineTix');
            $sheet->getStyle('A2:C2')->applyFromArray($metaStyle);

            // Headers
            $headers = ['Bulan & Tahun', 'Total Transaksi (Booking)', 'Total Omset Pendapatan (Rp)'];
            $sheet->fromArray($headers, null, 'A4');
            $sheet->getStyle('A4:C4')->applyFromArray($headerStyle);
            $sheet->getRowDimension(4)->setRowHeight(28);

            // Data Rows
            $rowNum = 5;
            $totalBookings = 0;
            $totalRevenue = 0;

            foreach ($data as $row) {
                $monthFormatted = \Carbon\Carbon::parse($row->month_year . '-01')->translatedFormat('F Y');
                $sheet->setCellValue('A' . $rowNum, $monthFormatted);
                $sheet->setCellValue('B' . $rowNum, $row->total_bookings);
                $sheet->setCellValue('C' . $rowNum, $row->total_revenue);

                // Formatting
                $sheet->getStyle('A' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('B' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('B' . $rowNum)->getNumberFormat()->setFormatCode('#,##0');
                $sheet->getStyle('C' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle('C' . $rowNum)->getNumberFormat()->setFormatCode('"Rp" #,##0');
                $sheet->getStyle('A' . $rowNum . ':C' . $rowNum)->applyFromArray($borderStyle);
                $sheet->getRowDimension($rowNum)->setRowHeight(22);

                $totalBookings += $row->total_bookings;
                $totalRevenue += $row->total_revenue;
                $rowNum++;
            }

            // Total Row
            $sheet->setCellValue('A' . $rowNum, 'TOTAL KESELURUHAN');
            $sheet->setCellValue('B' . $rowNum, $totalBookings);
            $sheet->setCellValue('C' . $rowNum, $totalRevenue);
            $sheet->getStyle('A' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('B' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('B' . $rowNum)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle('C' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('C' . $rowNum)->getNumberFormat()->setFormatCode('"Rp" #,##0');
            $sheet->getStyle('A' . $rowNum . ':C' . $rowNum)->applyFromArray($totalStyle);
            $sheet->getRowDimension($rowNum)->setRowHeight(25);

            // Auto-fit columns
            foreach (['A', 'B', 'C'] as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
        } elseif ($type === 'daily') {
            // Title Block
            $sheet->mergeCells('A1:C1');
            $sheet->setCellValue('A1', strtoupper($title));
            $sheet->getStyle('A1:C1')->applyFromArray($titleStyle);
            $sheet->getRowDimension(1)->setRowHeight(35);

            // Subtitle / Meta
            $sheet->mergeCells('A2:C2');
            $sheet->setCellValue('A2', 'Tanggal Cetak: ' . date('d M Y, H:i') . ' WIB | Sistem Manajemen Bioskop CineTix');
            $sheet->getStyle('A2:C2')->applyFromArray($metaStyle);

            // Headers
            $headers = ['Tanggal', 'Total Transaksi (Booking)', 'Total Omset Pendapatan (Rp)'];
            $sheet->fromArray($headers, null, 'A4');
            $sheet->getStyle('A4:C4')->applyFromArray($headerStyle);
            $sheet->getRowDimension(4)->setRowHeight(28);

            // Data Rows
            $rowNum = 5;
            $totalBookings = 0;
            $totalRevenue = 0;

            foreach ($data as $row) {
                $dateFormatted = \Carbon\Carbon::parse($row->booking_date)->translatedFormat('d F Y');
                $sheet->setCellValue('A' . $rowNum, $dateFormatted);
                $sheet->setCellValue('B' . $rowNum, $row->total_bookings);
                $sheet->setCellValue('C' . $rowNum, $row->total_revenue);

                // Formatting
                $sheet->getStyle('A' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('B' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('B' . $rowNum)->getNumberFormat()->setFormatCode('#,##0');
                $sheet->getStyle('C' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle('C' . $rowNum)->getNumberFormat()->setFormatCode('"Rp" #,##0');
                $sheet->getStyle('A' . $rowNum . ':C' . $rowNum)->applyFromArray($borderStyle);
                $sheet->getRowDimension($rowNum)->setRowHeight(22);

                $totalBookings += $row->total_bookings;
                $totalRevenue += $row->total_revenue;
                $rowNum++;
            }

            // Total Row
            $sheet->setCellValue('A' . $rowNum, 'TOTAL KESELURUHAN');
            $sheet->setCellValue('B' . $rowNum, $totalBookings);
            $sheet->setCellValue('C' . $rowNum, $totalRevenue);
            $sheet->getStyle('A' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('B' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('B' . $rowNum)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle('C' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('C' . $rowNum)->getNumberFormat()->setFormatCode('"Rp" #,##0');
            $sheet->getStyle('A' . $rowNum . ':C' . $rowNum)->applyFromArray($totalStyle);
            $sheet->getRowDimension($rowNum)->setRowHeight(25);

            // Auto-fit columns
            foreach (['A', 'B', 'C'] as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
        } else {
            // Film Report
            // Title Block
            $sheet->mergeCells('A1:D1');
            $sheet->setCellValue('A1', strtoupper($title));
            $sheet->getStyle('A1:D1')->applyFromArray($titleStyle);
            $sheet->getRowDimension(1)->setRowHeight(35);

            // Subtitle / Meta
            $sheet->mergeCells('A2:D2');
            $sheet->setCellValue('A2', 'Tanggal Cetak: ' . date('d M Y, H:i') . ' WIB | Sistem Manajemen Bioskop CineTix');
            $sheet->getStyle('A2:D2')->applyFromArray($metaStyle);

            // Headers
            $headers = ['ID Film', 'Judul Film', 'Tiket Terjual', 'Total Pendapatan (Rp)'];
            $sheet->fromArray($headers, null, 'A4');
            $sheet->getStyle('A4:D4')->applyFromArray($headerStyle);
            $sheet->getRowDimension(4)->setRowHeight(28);

            // Data Rows
            $rowNum = 5;
            $totalTickets = 0;
            $totalRevenue = 0;

            foreach ($data as $row) {
                $sheet->setCellValue('A' . $rowNum, '#' . $row->id);
                $sheet->setCellValue('B' . $rowNum, $row->title);
                $sheet->setCellValue('C' . $rowNum, $row->tickets_sold);
                $sheet->setCellValue('D' . $rowNum, $row->total_revenue ?? 0);

                // Formatting
                $sheet->getStyle('A' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('B' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('C' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('C' . $rowNum)->getNumberFormat()->setFormatCode('#,##0');
                $sheet->getStyle('D' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle('D' . $rowNum)->getNumberFormat()->setFormatCode('"Rp" #,##0');
                $sheet->getStyle('A' . $rowNum . ':D' . $rowNum)->applyFromArray($borderStyle);
                $sheet->getRowDimension($rowNum)->setRowHeight(22);

                $totalTickets += $row->tickets_sold;
                $totalRevenue += ($row->total_revenue ?? 0);
                $rowNum++;
            }

            // Total Row
            $sheet->mergeCells('A' . $rowNum . ':B' . $rowNum);
            $sheet->setCellValue('A' . $rowNum, 'TOTAL KESELURUHAN');
            $sheet->setCellValue('C' . $rowNum, $totalTickets);
            $sheet->setCellValue('D' . $rowNum, $totalRevenue);
            $sheet->getStyle('A' . $rowNum . ':B' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C' . $rowNum)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle('D' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('D' . $rowNum)->getNumberFormat()->setFormatCode('"Rp" #,##0');
            $sheet->getStyle('A' . $rowNum . ':D' . $rowNum)->applyFromArray($totalStyle);
            $sheet->getRowDimension($rowNum)->setRowHeight(25);

            // Auto-fit columns
            foreach (['A', 'B', 'C', 'D'] as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
        }

        $writer = new Xlsx($spreadsheet);
        
        $callback = function () use ($writer) {
            $writer->save('php://output');
        };

        return new StreamedResponse($callback, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    private function exportPdf($data, $type, $title, $filename)
    {
        $pdf = Pdf::loadView('admin.reports.pdf', compact('data', 'type', 'title'));
        $pdf->setPaper('A4', 'portrait');
        return $pdf->download($filename);
    }
}
