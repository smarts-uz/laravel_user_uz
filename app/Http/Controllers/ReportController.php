<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\Category;
use App\Services\ReportService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function report(Report $report)
    {
    
        $service = new ReportService();
        $item = $service->report($report);
        
        return view('vendor.voyager.report.report', 
        [
            'task' => $item->task,
            'report' => $report,
            'table' => $item->table,
            'columns' => $item->columns,
            'task_parent' => $item->task_parent,
        ]);
    }
}
