<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Services\AdminReportService;

class AdminReportController extends Controller
{
    public function __construct(
        private AdminReportService $service
    ) {
    }

    /**
     * Display the report dashboard
     */
    public function index(Request $request, Response $response): string
    {
        $this->setLayout('main');
        $this->setTitle('Laporan | Library Booking App');

        $params = array_merge($request->getQuery(), $request->getBody());

        $filters = [
            'start_date' => $params['start_date'] ?? '',
            'end_date' => $params['end_date'] ?? '',
            'status' => $params['status'] ?? '',
            'chart_type' => $params['chart_type'] ?? 'day',
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

    /**
     * Export report data to CSV
     */
    public function exportCsv(Request $request, Response $response): void
    {
        $params = $request->getQuery();

        $filters = [
            'start_date' => $params['start_date'] ?? '',
            'end_date' => $params['end_date'] ?? '',
            'status' => $params['status'] ?? '',
            'chart_type' => $params['chart_type'] ?? 'day',
        ];

        $reportRows = $this->service->getTableData($filters);
        $summary = $this->service->getSummary($filters);
        $chartData = $this->service->getChartData($filters);

        $chartTypes = [
            'day' => 'Per Hari',
            'week' => 'Minggu',
            'month' => 'Per Bulan',
            'semester' => 'Per Semester',
            'year' => 'Per Tahun',
            'room' => 'Ruangan Favorit',
            'department' => 'Per Jurusan',
            'purpose' => 'Tujuan Booking',
            'hours' => 'Jam Sibuk'
        ];

        $filename = 'laporan_booking_' . date('Y-m-d_His') . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM for Excel UTF-8

        // Title
        fputcsv($output, ['LAPORAN BOOKING - LIBRARY BOOKING APP'], ',', '"', '\\');
        fputcsv($output, [], ',', '"', '\\');

        // Metadata table
        fputcsv($output, ['INFORMASI LAPORAN'], ',', '"', '\\');
        fputcsv($output, ['Item', 'Keterangan'], ',', '"', '\\');
        fputcsv($output, ['Tanggal Mulai', $filters['start_date'] ?: 'Semua'], ',', '"', '\\');
        fputcsv($output, ['Tanggal Akhir', $filters['end_date'] ?: 'Semua'], ',', '"', '\\');
        fputcsv($output, ['Status Filter', $filters['status'] ?: 'Semua (kecuali draft/pending)'], ',', '"', '\\');
        fputcsv($output, ['Tipe Chart', $chartTypes[$filters['chart_type']] ?? 'Per Hari'], ',', '"', '\\');
        fputcsv($output, ['Tanggal Export', date('d/m/Y H:i:s')], ',', '"', '\\');
        fputcsv($output, [], ',', '"', '\\');
        fputcsv($output, [], ',', '"', '\\');

        // Summary table
        fputcsv($output, ['RINGKASAN'], ',', '"', '\\');
        fputcsv($output, ['Status', 'Jumlah'], ',', '"', '\\');
        fputcsv($output, ['Total Booking', $summary['total'] ?? 0], ',', '"', '\\');
        fputcsv($output, ['Verified', $summary['verified'] ?? 0], ',', '"', '\\');
        fputcsv($output, ['Active', $summary['active'] ?? 0], ',', '"', '\\');
        fputcsv($output, ['Completed', $summary['completed'] ?? 0], ',', '"', '\\');
        fputcsv($output, ['No Show', $summary['no_show'] ?? 0], ',', '"', '\\');
        fputcsv($output, ['Cancelled', $summary['cancelled'] ?? 0], ',', '"', '\\');
        fputcsv($output, [], ',', '"', '\\');
        fputcsv($output, [], ',', '"', '\\');

        // Chart data table
        fputcsv($output, ['DATA ' . strtoupper($chartTypes[$filters['chart_type']] ?? 'Per Hari')], ',', '"', '\\');
        fputcsv($output, ['Kategori', 'Jumlah Booking'], ',', '"', '\\');
        if (!empty($chartData['labels'])) {
            for ($i = 0; $i < count($chartData['labels']); $i++) {
                fputcsv($output, [$chartData['labels'][$i], $chartData['values'][$i]], ',', '"', '\\');
            }
        } else {
            fputcsv($output, ['Tidak ada data', '-'], ',', '"', '\\');
        }
        fputcsv($output, [], ',', '"', '\\');
        fputcsv($output, [], ',', '"', '\\');

        // Detail booking table
        fputcsv($output, ['DETAIL BOOKING (' . count($reportRows) . ' data)'], ',', '"', '\\');
        fputcsv($output, ['Kode Booking', 'Nama User', 'Jurusan', 'Ruangan', 'Tujuan', 'Tanggal Penggunaan', 'Waktu', 'Status'], ',', '"', '\\');

        foreach ($reportRows as $row) {
            fputcsv($output, [
                $row['kode_booking'] ?? '-',
                $row['user_name'] ?? '-',
                $row['user_jurusan'] ?? '-',
                $row['room_name'] ?? '-',
                $row['tujuan'] ?? '-',
                $row['tanggal'] ?? '-',
                ($row['waktu_mulai'] ?? '') . ' - ' . ($row['waktu_selesai'] ?? ''),
                strtoupper($row['status'] ?? '-'),
            ], ',', '"', '\\');
        }

        fclose($output);
        exit;
    }

    /**
     * Export report data to PDF (HTML for browser print)
     */
    public function exportPdf(Request $request, Response $response): void
    {
        $params = $request->getQuery();

        $filters = [
            'start_date' => $params['start_date'] ?? '',
            'end_date' => $params['end_date'] ?? '',
            'status' => $params['status'] ?? '',
            'chart_type' => $params['chart_type'] ?? 'day',
        ];

        $reportRows = $this->service->getTableData($filters);
        $summary = $this->service->getSummary($filters);
        $chartData = $this->service->getChartData($filters);

        $chartTypes = [
            'day' => 'Per Hari',
            'week' => 'Per Minggu',
            'month' => 'Per Bulan',
            'semester' => 'Per Semester',
            'year' => 'Per Tahun',
            'room' => 'Ruangan Favorit',
            'department' => 'Per Jurusan',
            'purpose' => 'Tujuan Booking',
            'hours' => 'Jam Sibuk'
        ];

        // Create chart visualization
        $chartHtml = '';
        if (!empty($chartData['labels'])) {
            $maxValue = max($chartData['values']) ?: 1;
            $chartHtml = '<div style="margin: 20px 0; padding: 15px; background: #f9fafb; border-radius: 8px;">';
            $chartHtml .= '<h3 style="margin:0 0 10px 0; color:#059669;">Data Chart: ' . ($chartTypes[$filters['chart_type']] ?? 'Per Hari') . '</h3>';
            $chartHtml .= '<table style="width:100%; border:none;">';

            for ($i = 0; $i < min(count($chartData['labels']), 15); $i++) {
                $label = $chartData['labels'][$i];
                $value = $chartData['values'][$i];
                $percentage = ($maxValue > 0) ? ($value / $maxValue * 100) : 0;

                $chartHtml .= '<tr style="border:none;">';
                $chartHtml .= '<td style="border:none; padding:4px 8px; width:150px; font-size:10px;">' . htmlspecialchars($label) . '</td>';
                $chartHtml .= '<td style="border:none; padding:4px;">';
                $chartHtml .= '<div style="background:#10b981; height:18px; width:' . $percentage . '%; min-width:20px; border-radius:3px; display:inline-block;"></div>';
                $chartHtml .= ' <span style="margin-left:5px; font-weight:bold; color:#059669;">' . $value . '</span>';
                $chartHtml .= '</td>';
                $chartHtml .= '</tr>';
            }

            $chartHtml .= '</table></div>';
        }

        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Laporan Booking</title>
            <style>
                body { font-family: Arial, sans-serif; font-size: 11px; margin: 20px; }
                h1 { color: #059669; font-size: 18px; margin-bottom: 5px; }
                .meta { color: #666; margin-bottom: 15px; background: #f9fafb; padding: 10px; border-radius: 5px; }
                .meta-row { margin: 3px 0; }
                .meta-label { font-weight: bold; display: inline-block; width: 120px; }
                .summary { display: flex; gap: 15px; margin-bottom: 20px; flex-wrap: wrap; }
                .summary-card { background: #f3f4f6; padding: 10px 15px; border-radius: 5px; min-width: 100px; }
                .summary-card strong { display: block; font-size: 20px; color: #059669; }
                .summary-card span { font-size: 10px; color: #666; }
                table { width: 100%; border-collapse: collapse; margin-top: 15px; }
                th, td { border: 1px solid #ddd; padding: 6px 8px; text-align: left; }
                th { background: #059669; color: white; font-weight: bold; }
                tr:nth-child(even) { background: #f9fafb; }
                .footer { margin-top: 20px; font-size: 10px; color: #666; text-align: center; border-top: 1px solid #ddd; padding-top: 10px; }
                
                /* Print UI */
                .print-controls { 
                    position: fixed; 
                    top: 20px; 
                    right: 20px; 
                    background: white; 
                    padding: 10px; 
                    border-radius: 8px; 
                    box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
                    border: 1px solid #e5e7eb;
                    z-index: 100;
                }
                .btn-print {
                    background: #10b981;
                    color: white;
                    border: none;
                    padding: 8px 16px;
                    border-radius: 6px;
                    cursor: pointer;
                    font-weight: bold;
                    font-size: 12px;
                    display: inline-flex;
                    align-items: center;
                    gap: 6px;
                }
                .btn-print:hover { background: #059669; }
                .btn-back {
                    text-decoration: none;
                    color: #666;
                    font-size: 11px;
                    margin-right: 10px;
                }

                @media print { 
                    body { margin: 0; } 
                    .print-controls { display: none; }
                    
                    /* Force background colors to show when printing */
                    * {
                        -webkit-print-color-adjust: exact !important;
                        print-color-adjust: exact !important;
                        color-adjust: exact !important;
                    }

                    .summary-card { background: #f3f4f6 !important; }
                    .meta { background: #f9fafb !important; }
                    th { background: #059669 !important; color: white !important; }
                    .chart-bar { background: #10b981 !important; }
                }
            </style>
        </head>
        <body>
            <div class="print-controls">
                <a href="javascript:history.back()" class="btn-back">Kembali</a>
                <button onclick="window.print()" class="btn-print">
                    Cetak Laporan / Simpan PDF
                </button>
            </div>

            <h1>LAPORAN BOOKING - LIBRARY BOOKING APP</h1>
            <div class="meta">
                <div class="meta-row"><span class="meta-label">Tanggal Mulai:</span>' . ($filters['start_date'] ?: 'Semua') . '</div>
                <div class="meta-row"><span class="meta-label">Tanggal Akhir:</span>' . ($filters['end_date'] ?: 'Semua') . '</div>
                <div class="meta-row"><span class="meta-label">Filter Status:</span>' . ($filters['status'] ?: 'Semua (kecuali draft/pending)') . '</div>
                <div class="meta-row"><span class="meta-label">Tipe Chart:</span>' . ($chartTypes[$filters['chart_type']] ?? 'Per Hari') . '</div>
                <div class="meta-row"><span class="meta-label">Diekspor:</span>' . date('d/m/Y H:i:s') . '</div>
            </div>
            
            <h3 style="color:#059669; margin-top:20px;">Ringkasan</h3>
            <div class="summary">
                <div class="summary-card"><strong>' . ($summary['total'] ?? 0) . '</strong><span>Total</span></div>
                <div class="summary-card"><strong>' . ($summary['verified'] ?? 0) . '</strong><span>Verified</span></div>
                <div class="summary-card"><strong>' . ($summary['active'] ?? 0) . '</strong><span>Active</span></div>
                <div class="summary-card"><strong>' . ($summary['completed'] ?? 0) . '</strong><span>Completed</span></div>
                <div class="summary-card"><strong>' . ($summary['no_show'] ?? 0) . '</strong><span>No Show</span></div>
                <div class="summary-card"><strong>' . ($summary['cancelled'] ?? 0) . '</strong><span>Cancelled</span></div>
            </div>
            
            ' . $chartHtml . '
            
            <h3 style="color:#059669; margin-top:20px;">Detail Data (' . count($reportRows) . ' data)</h3>
            <table>
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>User</th>
                        <th>Jurusan</th>
                        <th>Ruangan</th>
                        <th>Tujuan</th>
                        <th>Tanggal</th>
                        <th>Waktu</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>';

        foreach ($reportRows as $row) {
            $html .= '<tr>
                <td>' . htmlspecialchars($row['kode_booking'] ?? '-') . '</td>
                <td>' . htmlspecialchars($row['user_name'] ?? '-') . '</td>
                <td>' . htmlspecialchars($row['user_jurusan'] ?? '-') . '</td>
                <td>' . htmlspecialchars($row['room_name'] ?? '-') . '</td>
                <td>' . htmlspecialchars($row['tujuan'] ?? '-') . '</td>
                <td>' . ($row['tanggal'] ?? '-') . '</td>
                <td>' . ($row['waktu_mulai'] ?? '') . ' - ' . ($row['waktu_selesai'] ?? '') . '</td>
                <td>' . htmlspecialchars($row['status'] ?? '-') . '</td>
            </tr>';
        }

        $html .= '</tbody>
            </table>
            <div class="footer">Generated by Library Booking App | ' . date('d/m/Y H:i:s') . '</div>
            
            <script>
                // Auto trigger print after a small delay to ensure rendering
                window.onload = function() {
                    setTimeout(function() {
                        // window.print(); // Di-comment dulu supaya user bisa lihat preview dulu atau klik manual
                    }, 500);
                };
            </script>
        </body>
        </html>';

        header('Content-Type: text/html; charset=utf-8');
        echo $html;
        exit;
    }
}
