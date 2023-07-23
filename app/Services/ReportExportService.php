<?php


namespace App\Services;


use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportExportService
{
    /**
     * @param $model
     * @param object $request
     * @param object $user
     * @return BinaryFileResponse
     */
    public function export($model, object $request, object $user): BinaryFileResponse
    {
        $title = $model::title();
        return Excel::download(new $model(Cache::get('date'),Cache::get('date_1')), "$title.xlsx");
    }
}
