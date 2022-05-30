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

//        public function show_child(Request $req, $id)
//        {
//            if($req->ajax())
//            {
//                $service = new ReportService();
//                $service->child_report($id);
//            }
//
//            return view('vendor.voyager.report.childreport');
//
//        }
public function index_sub($id)
{
    Cache::put('child',$id);
    return view('vendor.voyager.report.childreport');
}
    public function report_sub()
    {
        $id = Cache::get('child');
        $service = new ReportService();

        return $service->child_report($id);
    }


}
