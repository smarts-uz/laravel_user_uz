<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Services\ReportService;
use Illuminate\Support\Facades\Cache;

class ReportController extends Controller
{
    public function request(Request $request): RedirectResponse
    {
        Cache::put('date', $request->get('date'));
        Cache::put('date_1', $request->get('date_1'));
        return redirect()->back();
    }

    public function index()
    {
        return view('vendor.voyager.report.report');
    }

    public function report(): JsonResponse
    {
        return (new ReportService())->report();
    }

    public function index_sub($id)
    {
        Cache::put('child', $id);
        return view('vendor.voyager.report.childreport');
    }

    public function report_sub(): JsonResponse
    {
        $id = Cache::get('child');
        return (new ReportService())->child_report($id);
    }
}
