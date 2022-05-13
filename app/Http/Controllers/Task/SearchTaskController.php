<?php

namespace App\Http\Controllers\Task;

use App\Services\Task\CreateService;
use App\Services\Task\CustomFieldService;
use App\Models\Task;
use Illuminate\Support\Facades\DB;
use TCG\Voyager\Models\Category;
use App\Models\Review;
use Illuminate\Http\Request;
use TCG\Voyager\Http\Controllers\VoyagerBaseController;
use App\Services\Task\SearchService;
use Carbon\Carbon;


class SearchTaskController extends VoyagerBaseController
{
    private $service;
    private $custom_fields_servie;
    private $create_service;

    public function __construct()
    {
        $this->service = new SearchService();
        $this->custom_fields_servie = new CustomFieldService();
        $this->create_service = new CreateService();
    }

    public function task_search()
    {
        $categories = Category::where('parent_id', null)->select('id', 'name')->get();
        $categories2 = Category::where('parent_id', '<>', null)->select('id', 'parent_id', 'name')->get();
        return view('task.search', compact('categories', 'categories2'));
    }

    public function search(Request $request)
    {
        $s = $request->s;
        return Task::where('name', 'LIKE', "%$s%")->orderBy('name')->paginate(10);
    }

    public function ajax_tasks()
    {
        return $this->service->ajaxReq();
    }

    public function task(Task $task)
    {
        if (!$task->user_id) {
            abort(404);
        }
        $review = null;
        if ($task->reviews_count == 2) $review = true;
        if (auth()->check()) {
            $task->views++;
            $task->save();
        }

        
        $value = Carbon::parse($task->created_at)->locale(getLocale());
        $day = $value == now()->toDateTimeString()? "Bugun": "$value->day-$value->monthName";
        $created = "$day  $value->noZeroHour:$value->minute";

        $value = Carbon::parse($task->end_date)->locale(getLocale());
        $value->minute<10 ? $minut = '0'.$value->minute : $minut = $value->minute;
        $end = "$value->day-$value->monthName  $value->noZeroHour:$minut";

        $value = Carbon::parse($task->start_date)->locale(getLocale());
        $value->minute<10 ? $minut = '0'.$value->minute : $minut = $value->minute;
        $day = $value == now()->toDateTimeString()? "Bugun": "$value->day-$value->monthName";
        $start = "$day  $value->noZeroHour:$minut";

        $auth_response = auth()->check();
        $userId = auth()->id();
        $item = $this->service->task_service($auth_response, $userId, $task);
        return view('task.detailed-tasks',
        ['review_description' => $item->review_description,'task' => $task, 'created' => $created, 'end' => $end, 'start' => $start, 'review' => $review, 'complianceType' => $item->complianceType, 'same_tasks' => $item->same_tasks,
        'auth_response' => $item->auth_response, 'selected' => $item->selected, 'responses' => $item->responses, 'addresses' => $item->addresses, 'top_users'=>$item->top_users, 'respons_reviews'=>$item->respons_reviews]);
    }

    public function comlianse_save(Request $request)
    {
        $this->service->comlianse_saveS($request);
        return redirect()->back();
    }

    public function delete_task(Task $task)
    {
        taskGuard($task);
        abort_if($task->responses()->count() || $task->status != Task::STATUS_OPEN,403, 'No permission');


        $this->create_service->delete($task);
        return redirect('/');
    }

    public function changeTask(Task $task)
    {
        taskGuard($task);
        if ($task->responses_count)
            abort(403,"No Permission");
        $addresses = $task->addresses;
        //        dd($task);
        return view('task.changetask', compact('task', 'addresses'));
    }
    public function search_new(){
            $categories = Category::where('parent_id', null)->select('id', 'name')->get();
            $categories2 = Category::where('parent_id', '<>', null)->select('id', 'parent_id', 'name')->get();
            $tasks = Task::whereIn('status', [1, 2])->orderBy('id', 'desc')->paginate(20);
            return view('search_task.new_search', compact('tasks','categories','categories2'));
    }

public function search_new2(Request $request){

    $data = $request->data;

    $filter = $data[0]['value'];
    $suggest = $data[1]['value'];

// default value is SmartSoware office location
    $lat = $data[2]['value']?:41.364252;
    $lon = $data[3]['value']?:69.281910;

    // default radius is 50km
    $radius = $data[4]['value']?:50;
    $price = $data[5]['value'];


    $arr_check = [];
    $remjob =false;
    $noresp=false;
    $count = count($data);
    
    if(isset($data[6])){
        $remjob = $data[6]['name'] === "remjob";
        $noresp = $data[6]['name'] === "noresp";
        
        for ($k = 6; $k < $count; $k++) {
            if (is_numeric($data[$k]['name']))
                $arr_check[] = $data[$k]['name'];
        }
    }
    
    $tasks = $this->service->search_new_service($arr_check, $filter, $suggest, $price, $remjob, $noresp, $radius,$lat,$lon);


    $html = view("search_task.tasks", compact('tasks'))->render();
    return response()->json(array('success' => true, 'html' => $html));
}
}