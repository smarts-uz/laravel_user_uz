<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ReportService;
use Illuminate\Support\Facades\Cache;

class ReportController extends Controller
{
    public function request(Request $request)
    {
        Cache::put('date', $request->get('date'));
        Cache::put('date_1', $request->get('date_1'));
        return redirect()->back();
    }

    public function index()
    {
        return view('vendor.voyager.report.report');
    }

    public function report()
    {

        $service = new ReportService();

        return $service->report();
    }

    public function index_sub($id)
    {
        Cache::put('child', $id);
        return view('vendor.voyager.report.childreport');
    }

    public function report_sub()
    {
        $id = Cache::get('child');
        $service = new ReportService();

        return $service->child_report($id);
    }
}
