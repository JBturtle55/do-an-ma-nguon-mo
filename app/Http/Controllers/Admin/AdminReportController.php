<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class AdminReportController extends Controller
{
    public function __construct(protected ReportService $reportService) {}

    public function index(Request $request): View
    {
        $from = Carbon::parse($request->input('from', now()->startOfMonth()));
        $to   = Carbon::parse($request->input('to', now()->endOfMonth()));

        $summary     = $this->reportService->bookingSummary($from, $to);
        $utilization = $this->reportService->roomUtilization($from, $to);
        $topUsers    = $this->reportService->topUsers($from, $to);
        $equipUsage  = $this->reportService->equipmentUsage($from, $to);

        return view('admin.reports.index', compact(
            'summary', 'utilization', 'topUsers', 'equipUsage', 'from', 'to'
        ));
    }

    public function export(Request $request): Response
    {
        $from = Carbon::parse($request->input('from', now()->startOfMonth()));
        $to   = Carbon::parse($request->input('to', now()->endOfMonth()));
        $type = $request->input('type', 'utilization');

        $csv      = $this->reportService->exportCsv($type, $from, $to);
        $filename = "report_{$type}_{$from->format('Ymd')}-{$to->format('Ymd')}.csv";

        return response($csv, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
