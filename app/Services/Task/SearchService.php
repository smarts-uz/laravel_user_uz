<?php

namespace App\Services\Task;

use App\Item\SearchServiceTaskItem;
use App\Item\SearchNewItem;
use App\Models\ComplianceType;
use App\Models\Task;
use App\Models\Category;
use App\Models\User;
use App\Models\Compliance;
use App\Models\Review;
use Illuminate\Support\Facades\DB;

class SearchService
{
    public function ajaxReq()
    {
        $tasks = Task::whereIn('status', [1, 2])
            ->orderBy('id', 'desc')
            ->join('users', 'tasks.user_id', '=', 'users.id')
            ->join('categories', 'tasks.category_id', '=', 'categories.id')
            ->select('tasks.id', 'tasks.name', 'tasks.address', 'tasks.date_type', 'tasks.start_date', 'tasks.end_date', 'tasks.budget', 'tasks.category_id', 'tasks.status', 'tasks.oplata', 'tasks.coordinates', 'users.name as user_name', 'users.id as userid', 'categories.name as category_name', 'categories.ico as icon')
            ->get()->load(['responses', 'addresses']);
        return $tasks->all();
    }

    public function comlianse_saveS($request)
    {
        $comp = new Compliance();
        $comp->compliance_type_id = $request->input('c_type');
        $comp->text = $request->input('c_text');
        $comp->user_id = $request->input('userId');
        $comp->task_id = $request->input('taskId');
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
        $item->top_users = User::query()
        ->where('review_rating', '!=', 0)
        ->where('role_id', 2)->orderbyRaw('(review_good - review_bad) DESC')
        ->limit(20)->pluck('id')->toArray();
        $item->respons_reviews = Review::all();
        $item->review_description = Review::where('task_id', $task)->first();
        return $item;
    }

    public function search_new_service($arr_check, $filter = '', $suggest = ''): SearchNewItem
    {

        $users = User::all()->keyBy('id');
        $categories = Category::all()->keyBy('id');

        $item = new SearchNewItem();
        $tasks = DB::table('tasks')->whereIn('status', [1, 2])->whereIn('category_id', $arr_check)->get()->keyBy('id');

        foreach ($tasks as $task) {
            $taskNew = $task;
            $taskNew->user_id = $users->get($task->user_id)->name;
            $taskNew->category_id = $categories->get($task->category_id)->name;
            $item->tasks[] = $taskNew;
        }

        return $item;
    }
}
