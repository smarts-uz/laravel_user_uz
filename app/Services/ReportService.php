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
        $item->table = $report->getTable();
        $item-> columns = DB::select( 'SHOW FULL COLUMNS FROM reports' );
        $item->task_parent = Category::where('parent_id', null)->get();
        $item-> task = Task::where('category_id', $item->task_parent)->count();
        return $item;
    }

}
