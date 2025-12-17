<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Services\AdminReportService;
use App\Core\Middleware\AdminMiddleware;

class AdminReportController extends Controller
{
    public function __construct(
        private AdminReportService $service
    ) {
    }

    public function index(Request $request, Response $response)
    {
        $this->setLayout('main');
        $this->setTitle('Report | Library Booking App');

        $params = array_merge($request->getQuery(), $request->getBody());

        $filters = [
            'start_date' => $params['start_date'] ?? '',
            'end_date' => $params['end_date'] ?? '',
            'status' => $params['status'] ?? '',
            'chart_type' => $params['chart_type'] ?? 'booking',
        ];

        $summary = $this->service->getSummary($filters);
        $chartData = $this->service->getChartData($filters);
        $reportRows = $this->service->getTableData($filters);

        return $this->render('Admin/Report', [
            'filters' => $filters,
            'summary' => $summary,
            'chartData' => $chartData,
            'reportRows' => $reportRows,
            'chartType' => $filters['chart_type'],
        ]);
    }

    public function exportCsv(Request $request, Response $response)
    {
        $params = $request->getQuery();

        $filters = [
            'start_date' => $params['start_date'] ?? '',
            'end_date' => $params['end_date'] ?? '',
            'status' => $params['status'] ?? '',
            'chart_type' => $params['chart_type'] ?? 'booking',
        ];

        $reportRows = $this->service->getTableData($filters);
        $summary = $this->service->getSummary($filters);

        // Set headers for CSV download
        $filename = 'laporan_booking_' . date('Y-m-d_His') . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // Add BOM for Excel UTF-8 support
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Summary section
        fputcsv($output, ['LAPORAN BOOKING - LIBRARY BOOKING APP']);
        fputcsv($output, ['Periode', ($filters['start_date'] ?: 'Semua') . ' s/d ' . ($filters['end_date'] ?: 'Semua')]);
        fputcsv($output, ['Diekspor', date('d/m/Y H:i:s')]);
        fputcsv($output, []);

        // Summary stats
        fputcsv($output, ['RINGKASAN']);
        fputcsv($output, ['Total Booking', $summary['total_bookings'] ?? 0]);
        fputcsv($output, ['Completed', $summary['completed'] ?? 0]);
        fputcsv($output, ['No Show', $summary['no_show'] ?? 0]);
        fputcsv($output, []);

        // Table header
        fputcsv($output, ['DETAIL DATA']);
        if ($filters['chart_type'] === 'room') {
            fputcsv($output, ['Nama Ruangan', 'Total Booking']);
            foreach ($reportRows as $row) {
                fputcsv($output, [$row->nama_ruangan ?? 'Unknown', $row->total ?? 0]);
            }
        } elseif ($filters['chart_type'] === 'user') {
            fputcsv($output, ['Nama User', 'Email', 'Total Booking']);
            foreach ($reportRows as $row) {
                fputcsv($output, [$row->nama ?? 'Unknown', $row->email ?? '', $row->total ?? 0]);
            }
        } else {
            fputcsv($output, ['ID', 'User', 'Ruangan', 'Tanggal', 'Waktu', 'Status']);
            foreach ($reportRows as $row) {
                fputcsv($output, [
                    $row->id_booking ?? '',
                    $row->nama ?? '',
                    $row->nama_ruangan ?? '',
                    $row->tanggal_penggunaan_ruang ?? '',
                    ($row->waktu_mulai ?? '') . ' - ' . ($row->waktu_selesai ?? ''),
                    $row->status ?? ''
                ]);
            }
        }

        fclose($output);
        exit;
    }

    public function exportPdf(Request $request, Response $response)
    {
        $params = $request->getQuery();

        $filters = [
            'start_date' => $params['start_date'] ?? '',
            'end_date' => $params['end_date'] ?? '',
            'status' => $params['status'] ?? '',
            'chart_type' => $params['chart_type'] ?? 'booking',
        ];

        $reportRows = $this->service->getTableData($filters);
        $summary = $this->service->getSummary($filters);

        // Generate HTML for PDF
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Laporan Booking</title>
            <style>
                body { font-family: Arial, sans-serif; font-size: 12px; }
                h1 { color: #059669; font-size: 18px; }
                .summary { background: #f3f4f6; padding: 10px; margin-bottom: 20px; }
                table { width: 100%; border-collapse: collapse; margin-top: 10px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background: #059669; color: white; }
                tr:nth-child(even) { background: #f9fafb; }
                .footer { margin-top: 20px; font-size: 10px; color: #666; }
            </style>
        </head>
        <body>
            <h1>LAPORAN BOOKING - LIBRARY BOOKING APP</h1>
            <p>Periode: ' . ($filters['start_date'] ?: 'Semua') . ' s/d ' . ($filters['end_date'] ?: 'Semua') . '</p>
            <p>Diekspor: ' . date('d/m/Y H:i:s') . '</p>
            
            <div class="summary">
                <strong>Ringkasan:</strong><br>
                Total Booking: ' . ($summary['total_bookings'] ?? 0) . ' | 
                Completed: ' . ($summary['completed'] ?? 0) . ' | 
                No Show: ' . ($summary['no_show'] ?? 0) . '
            </div>
            
            <table>
                <thead><tr>';

        if ($filters['chart_type'] === 'room') {
            $html .= '<th>Nama Ruangan</th><th>Total Booking</th>';
        } elseif ($filters['chart_type'] === 'user') {
            $html .= '<th>Nama User</th><th>Email</th><th>Total Booking</th>';
        } else {
            $html .= '<th>ID</th><th>User</th><th>Ruangan</th><th>Tanggal</th><th>Waktu</th><th>Status</th>';
        }

        $html .= '</tr></thead><tbody>';

        foreach ($reportRows as $row) {
            $html .= '<tr>';
            if ($filters['chart_type'] === 'room') {
                $html .= '<td>' . htmlspecialchars($row->nama_ruangan ?? 'Unknown') . '</td>';
                $html .= '<td>' . ($row->total ?? 0) . '</td>';
            } elseif ($filters['chart_type'] === 'user') {
                $html .= '<td>' . htmlspecialchars($row->nama ?? 'Unknown') . '</td>';
                $html .= '<td>' . htmlspecialchars($row->email ?? '') . '</td>';
                $html .= '<td>' . ($row->total ?? 0) . '</td>';
            } else {
                $html .= '<td>' . ($row->id_booking ?? '') . '</td>';
                $html .= '<td>' . htmlspecialchars($row->nama ?? '') . '</td>';
                $html .= '<td>' . htmlspecialchars($row->nama_ruangan ?? '') . '</td>';
                $html .= '<td>' . ($row->tanggal_penggunaan_ruang ?? '') . '</td>';
                $html .= '<td>' . ($row->waktu_mulai ?? '') . ' - ' . ($row->waktu_selesai ?? '') . '</td>';
                $html .= '<td>' . htmlspecialchars($row->status ?? '') . '</td>';
            }
            $html .= '</tr>';
        }

        $html .= '</tbody></table>
            <div class="footer">Generated by Library Booking App</div>
        </body>
        </html>';

        // Output as HTML with print-friendly styling (browser can print to PDF)
        header('Content-Type: text/html; charset=utf-8');
        echo $html;
        exit;
    }
}
