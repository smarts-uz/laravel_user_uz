<?php

namespace App\Http\Controllers;

use App\Services\ReportExportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Services\ReportService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    private ReportExportService $exportService;

    public function __construct()
    {
        $this->exportService = new ReportExportService();
    }

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

    /**
     * @param $model
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function report_export($model, Request $request): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $user = auth()->user();
        if (class_exists($model)) {
            $data =  $this->exportService->export($model, $request, $user);
        }
        else{
            Log::error('Model not exists {}');

        }

        return $data;
    }

    public function report(ReportService $service): JsonResponse
    {
        return $service->report();
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
