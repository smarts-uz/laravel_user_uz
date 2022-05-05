<?php

namespace App\Services\Task;

use App\Item\SearchServiceTaskItem;
use App\Models\ComplianceType;
use App\Models\Task;
use App\Models\Category;
use App\Models\User;
use App\Models\Compliance;
use App\Models\Review;
class SearchService
{
    public function ajaxReq(){
        $tasks = Task::whereIn('status', [1, 2])
            ->orderBy('id', 'desc')
            ->join('users', 'tasks.user_id', '=', 'users.id')
            ->join('categories', 'tasks.category_id', '=', 'categories.id')
            ->select('tasks.id', 'tasks.name', 'tasks.address','tasks.date_type', 'tasks.start_date', 'tasks.end_date', 'tasks.budget', 'tasks.category_id', 'tasks.status', 'tasks.oplata', 'tasks.coordinates', 'users.name as user_name', 'users.id as userid', 'categories.name as category_name', 'categories.ico as icon')
            ->get()->load(['responses','addresses']);
        return $tasks->all();
    }

    public function comlianse_saveS($request){
        $comp = new Compliance();
        $comp->compliance_type_id=$request->input('c_type');
        $comp->text=$request->input('c_text');
        $comp->user_id=$request->input('userId');
        $comp->task_id=$request->input('taskId');
        $comp->save();
    }

    public function task_service($auth_response, $userId, $task): SearchServiceTaskItem
    {
        $item = new SearchServiceTaskItem();
        $item->complianceType = ComplianceType::all();
        $item->selected = $task->responses()->where('performer_id', $task->performer_id)->first();
        $item->responses = $item->selected ? $task->responses()->where('id', '!=', $item->selected->id)->get() : $task->responses;
        $item->auth_response = $auth_response ? $task->responses()->where('performer_id', $userId)->with('user')->first() : null;
        $item->same_tasks = $task->category->tasks()->where('id', '!=', $task->id)->where('status', Task::STATUS_OPEN)->orderBy('created_at', 'desc')->get();
        $item->addresses = $task->addresses;
        $item->about = User::where('role_id', 2)->orderBy('reviews', 'desc')->take(20)->get();
        $item->respons_reviews = Review::all();
        return $item;
    }

}
