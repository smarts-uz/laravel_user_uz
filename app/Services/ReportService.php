<?php


namespace App\Services;


use App\Models\Task;
use App\Models\Report;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use App\Item\ReportItem;

class ReportService
{

    public function report( $report){
        $item = new ReportItem();
        $item-> table = $report->getTable();
        $item-> columns = DB::select( 'SHOW FULL COLUMNS FROM reports' );
        $item-> task_parent = Category::where('parent_id', null)->get();
        $item-> task = Task::where('category_id', $item->task_parent)->count();
        return $item;
    }

    public function new_report($report) {

         $item = new ReportItem();
         
        //  $item = $categories_array = \App\Models\Category::where('parent_id', $parent->id)->pluck('id')->toarray();
        //  $item = $category_count = \App\Models\Task::whereIn('category_id', $categories_array)->count();
        //  $item = $categories_array1 = \App\Models\Category::where('parent_id', $parent->id)->pluck('id')->toarray();
        //  $item = $category_count1 = \App\Models\Task::whereIn('category_id', $categories_array1)->pluck('budget')->toArray();
        //  $item = $budgets = str_replace(array('до', 'сум', 'от'), '', $category_count1);
        //  $item = $all_budget = array_sum($budgets);

         return $item;

    }

}
