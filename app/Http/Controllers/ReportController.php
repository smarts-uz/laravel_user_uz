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
        $dtHeaders = [
            [
                __('ID') => [
                    'rowspan' => 2,
                    'colspan' => 0,
                ],
                __('Категории') => [
                    'rowspan' => 2,
                    'colspan' => 0,
                ],
                __('Подкатегории') => [
                    'rowspan' => 2,
                    'colspan' => 0,
                ],
                __('Открытые') => [
                    'rowspan' => 0,
                    'colspan' => 2,
                ],
                __('Открытые Ответ') => [
                    'rowspan' => 0,
                    'colspan' => 2,
                ],
                __('В исполнении') => [
                    'rowspan' => 0,
                    'colspan' => 2,
                ],
                __('Закрытые') => [
                    'rowspan' => 0,
                    'colspan' => 2,
                ],
                __('Не завершено') => [
                    'rowspan' => 0,
                    'colspan' => 2,
                ],
                __('Отмененные') => [
                    'rowspan' => 0,
                    'colspan' => 2,
                ],
                __('Всего') => [
                    'rowspan' => 0,
                    'colspan' => 2,
                ],
            ],
            [
                __('Кол-во 1') => [
                    'rowspan' => 0,
                    'colspan' => 0,
                ],
                __('Сумма 1') => [
                    'rowspan' => 0,
                    'colspan' => 0,
                ],
                __('Кол-во 2') => [
                    'rowspan' => 0,
                    'colspan' => 0,
                ],
                __('Сумма 2') => [
                    'rowspan' => 0,
                    'colspan' => 0,
                ],
                __('Кол-во 3') => [
                    'rowspan' => 0,
                    'colspan' => 0,
                ],
                __('Сумма 3') => [
                    'rowspan' => 0,
                    'colspan' => 0,
                ],
                __('Кол-во 4') => [
                    'rowspan' => 0,
                    'colspan' => 0,
                ],
                __('Сумма 4') => [
                    'rowspan' => 0,
                    'colspan' => 0,
                ],
                __('Кол-во 5') => [
                    'rowspan' => 0,
                    'colspan' => 0,
                ],
                __('Сумма 5') => [
                    'rowspan' => 0,
                    'colspan' => 0,
                ],
                __('Кол-во 6') => [
                    'rowspan' => 0,
                    'colspan' => 0,
                ],
                __('Сумма 6') => [
                    'rowspan' => 0,
                    'colspan' => 0,
                ],
                __('Кол-во 7') => [
                    'rowspan' => 0,
                    'colspan' => 0,
                ],
                __('Сумма 7') => [
                    'rowspan' => 0,
                    'colspan' => 0,
                ],
            ],
        ];
        $dtColumns = [
            ['data' => 'id', 'name' => 'id'],
            ['data' => 'name', 'name' => 'name'],
            ['data' => 'link', 'name' => 'link' , 'render' => 'function (data, type, row) {
                            var details = `<a href="/admin/report/${row.id}"><i class="fas fa-arrow-right"></i></a>`;
                            // var details = `<i class="fas fa-arrow-right"></i>`;
                            return details;
                        }'],
            ['data' => 'open_count', 'name' => 'open_count'],
            ['data' => 'open_sum', 'name' => 'open_sum'],
            ['data' => 'response_count', 'name' => 'response_count'],
            ['data' => 'response_sum', 'name' => 'response_sum'],
            ['data' => 'process_count', 'name' => 'process_count'],
            ['data' => 'process_sum', 'name' => 'process_sum'],
            ['data' => 'finished_count', 'name' => 'finished_count'],
            ['data' => 'finished_sum', 'name' => 'finished_sum'],
            ['data' => 'not_complete_count', 'name' => 'not_complete_count'],
            ['data' => 'not_complete_sum', 'name' => 'not_complete_sum'],
            ['data' => 'cancelled_count', 'name' => 'cancelled_count'],
            ['data' => 'cancelled_sum', 'name' => 'cancelled_sum'],
            ['data' => 'total_count', 'name' => 'total_count'],
            ['data' => 'total_sum', 'name' => 'total_sum'],
        ];
        return view('vendor.voyager.report.report',compact('dtHeaders','dtColumns'));
    }

    public function report(): JsonResponse
    {
        return (new ReportService())->report();
    }

    public function index_sub($id)
    {
        Cache::put('child', $id);
        $dtHeaders = [
            [
                __('ID') => [
                    'rowspan' => 2,
                    'colspan' => 0,
                ],
                __('Категории') => [
                    'rowspan' => 2,
                    'colspan' => 0,
                ],
                __('Открытые') => [
                    'rowspan' => 0,
                    'colspan' => 2,
                ],
                __('Открытые Ответ') => [
                    'rowspan' => 0,
                    'colspan' => 2,
                ],
                __('В исполнении') => [
                    'rowspan' => 0,
                    'colspan' => 2,
                ],
                __('Закрытые') => [
                    'rowspan' => 0,
                    'colspan' => 2,
                ],
                __('Не завершено') => [
                    'rowspan' => 0,
                    'colspan' => 2,
                ],
                __('Отмененные') => [
                    'rowspan' => 0,
                    'colspan' => 2,
                ],
                __('Всего') => [
                    'rowspan' => 0,
                    'colspan' => 2,
                ],
            ],
            [
                __('Кол-во 1') => [
                    'rowspan' => 0,
                    'colspan' => 0,
                ],
                __('Сумма 1') => [
                    'rowspan' => 0,
                    'colspan' => 0,
                ],
                __('Кол-во 2') => [
                    'rowspan' => 0,
                    'colspan' => 0,
                ],
                __('Сумма 2') => [
                    'rowspan' => 0,
                    'colspan' => 0,
                ],
                __('Кол-во 3') => [
                    'rowspan' => 0,
                    'colspan' => 0,
                ],
                __('Сумма 3') => [
                    'rowspan' => 0,
                    'colspan' => 0,
                ],
                __('Кол-во 4') => [
                    'rowspan' => 0,
                    'colspan' => 0,
                ],
                __('Сумма 4') => [
                    'rowspan' => 0,
                    'colspan' => 0,
                ],
                __('Кол-во 5') => [
                    'rowspan' => 0,
                    'colspan' => 0,
                ],
                __('Сумма 5') => [
                    'rowspan' => 0,
                    'colspan' => 0,
                ],
                __('Кол-во 6') => [
                    'rowspan' => 0,
                    'colspan' => 0,
                ],
                __('Сумма 6') => [
                    'rowspan' => 0,
                    'colspan' => 0,
                ],
                __('Кол-во 7') => [
                    'rowspan' => 0,
                    'colspan' => 0,
                ],
                __('Сумма 7') => [
                    'rowspan' => 0,
                    'colspan' => 0,
                ],
            ],
        ];
        $dtColumns = [
            ['data' => 'id', 'name' => 'id'],
            ['data' => 'name', 'name' => 'name'],
            ['data' => 'open_count', 'name' => 'open_count'],
            ['data' => 'open_sum', 'name' => 'open_sum'],
            ['data' => 'response_count', 'name' => 'response_count'],
            ['data' => 'response_sum', 'name' => 'response_sum'],
            ['data' => 'process_count', 'name' => 'process_count'],
            ['data' => 'process_sum', 'name' => 'process_sum'],
            ['data' => 'finished_count', 'name' => 'finished_count'],
            ['data' => 'finished_sum', 'name' => 'finished_sum'],
            ['data' => 'not_complete_count', 'name' => 'not_complete_count'],
            ['data' => 'not_complete_sum', 'name' => 'not_complete_sum'],
            ['data' => 'cancelled_count', 'name' => 'cancelled_count'],
            ['data' => 'cancelled_sum', 'name' => 'cancelled_sum'],
            ['data' => 'total_count', 'name' => 'total_count'],
            ['data' => 'total_sum', 'name' => 'total_sum']
        ];
        return view('vendor.voyager.report.childreport', compact('dtHeaders','dtColumns'));
    }

    public function report_sub(): JsonResponse
    {
        $id = Cache::get('child');
        return (new ReportService())->child_report($id);
    }
}
