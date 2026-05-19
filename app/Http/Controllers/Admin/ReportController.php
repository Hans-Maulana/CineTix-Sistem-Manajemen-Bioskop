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
        // 1. Rekap Keseluruhan
        $totalRevenue = Booking::where('status', 'confirmed')->sum('total_amount');
        $totalTickets = DB::table('ticket_bookings')
            ->join('bookings', 'ticket_bookings.booking_id', '=', 'bookings.id')
            ->where('bookings.status', 'confirmed')
            ->count();
        $totalBookings = Booking::where('status', 'confirmed')->count();

        // 2. Rekap per Film
        $filmReports = Film::select('films.id', 'films.title')
            ->withCount(['schedules as tickets_sold' => function ($query) {
                $query->join('ticket_bookings', 'schedules.id', '=', 'ticket_bookings.schedule_id')
                      ->join('bookings', 'ticket_bookings.booking_id', '=', 'bookings.id')
                      ->where('bookings.status', 'confirmed');
            }])
            ->withSum(['schedules as total_revenue' => function ($query) {
                $query->join('ticket_bookings', 'schedules.id', '=', 'ticket_bookings.schedule_id')
                      ->join('bookings', 'ticket_bookings.booking_id', '=', 'bookings.id')
                      ->where('bookings.status', 'confirmed');
            }], 'ticket_bookings.price_at_sale')
            ->get();

        // 3. Rekap per Bulan & Tahun
        $monthlyReports = Booking::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month_year'),
                DB::raw('COUNT(id) as total_bookings'),
                DB::raw('SUM(total_amount) as total_revenue')
            )
            ->where('status', 'confirmed')
            ->groupBy('month_year')
            ->orderBy('month_year', 'desc')
            ->get();

        return view('admin.reports.index', compact('totalRevenue', 'totalTickets', 'totalBookings', 'filmReports', 'monthlyReports'));
    }

    public function export(Request $request)
    {
        $type = $request->get('type', 'film'); // 'film' atau 'monthly'
        $format = $request->get('format', 'excel'); // 'excel' atau 'pdf'

        if ($type === 'monthly') {
            $data = Booking::select(
                    DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month_year'),
                    DB::raw('COUNT(id) as total_bookings'),
                    DB::raw('SUM(total_amount) as total_revenue')
                )
                ->where('status', 'confirmed')
                ->groupBy('month_year')
                ->orderBy('month_year', 'desc')
                ->get();

            $title = "Laporan Pendapatan Bulanan";
            $filenameBase = "laporan_pendapatan_bulanan_" . date('Ymd');
        } else {
            // Default: export per film
            $data = Film::select('films.id', 'films.title')
                ->withCount(['schedules as tickets_sold' => function ($query) {
                    $query->join('ticket_bookings', 'schedules.id', '=', 'ticket_bookings.schedule_id')
                          ->join('bookings', 'ticket_bookings.booking_id', '=', 'bookings.id')
                          ->where('bookings.status', 'confirmed');
                }])
                ->withSum(['schedules as total_revenue' => function ($query) {
                    $query->join('ticket_bookings', 'schedules.id', '=', 'ticket_bookings.schedule_id')
                          ->join('bookings', 'ticket_bookings.booking_id', '=', 'bookings.id')
                          ->where('bookings.status', 'confirmed');
                }], 'ticket_bookings.price_at_sale')
                ->get();

            $title = "Laporan Penjualan Tiket per Film";
            $filenameBase = "laporan_penjualan_per_film_" . date('Ymd');
        }

        if ($format === 'pdf') {
            return $this->exportPdf($data, $type, $title, $filenameBase . '.pdf');
        }

        // Default: Excel
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
