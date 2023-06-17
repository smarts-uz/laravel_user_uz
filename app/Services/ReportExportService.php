<?php


namespace App\Services;


use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Facades\Excel;

class ReportExportService
{
    /**
     * @param $model
     * @param object $request
     * @param object $user
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export($model, object $request, object $user): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $title = $model::title();
        return Excel::download(new $model(Cache::get('date'),Cache::get('date_1')), "$title.xlsx");
    }
}
