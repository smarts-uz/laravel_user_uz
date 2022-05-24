<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\Category;
use App\Services\ReportService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Item\ReportItem;

class ReportController extends Controller
{
    public function request(Request $request)
    {
        Cache::put('date',$request->date);
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

    public function new_report($report)
    {
        $well = new ReportService();
        $item = $well->new_report($report);

        $item = $categories_array = \App\Models\Category::where('parent_id', $report->id)->pluck('id')->toarray();
        $item = $category_count = \App\Models\Task::whereIn('category_id', $categories_array)->count();
        $item = $categories_array1 = \App\Models\Category::where('parent_id', $report->id)->pluck('id')->toarray();
        $item = $category_count1 = \App\Models\Task::whereIn('category_id', $categories_array1)->pluck('budget')->toArray();
        $item = $budgets = str_replace(array('до', 'сум', 'от'), '', $category_count1);
        $item = $all_budget = array_sum($budgets);

        return view('vendor.voyager.report.report', $item);
    }
}
