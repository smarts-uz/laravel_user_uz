<?php

namespace App\Services\Task;

use App\Item\SearchServiceTaskItem;
use App\Item\SearchNewItem;
use App\Models\Address;
use App\Models\ComplianceType;
use App\Models\Task;
use App\Models\Category;
use App\Models\User;
use App\Models\Compliance;
use App\Models\Review;
use Illuminate\Database\Eloquent\Builder;
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
        $item->about = User::where('role_id', 2)->orderBy('reviews', 'desc')->take(20)->get();
        $item->respons_reviews = Review::all();
        $item->review_description = Review::where('task_id', $task)->first();
        return $item;
    }

    public function search_new_service($arr_check, $filter = '', $suggest = '',$price): SearchNewItem
    {

        $users = User::all()->keyBy('id');
        $categories = Category::all()->keyBy('id');

        $item = new SearchNewItem();
        $tasks=Task::query();

    
if($filter){
    $tasks->where('name', 'LIKE', "%{$filter}%");
}
if($price){
    $tasks->where('budget', '>=', $price*0.8)
    ->where('budget', '<=', $price*1.2);
}
if( $suggest){
    $tasks->whereHas('addresses', function (Builder $query) use($suggest) {
         $query->where('location', 'like', "%{$suggest}%");});
}
if( $arr_check){
    $tasks->whereIn('category_id', $arr_check);
}

        // 

      

            foreach ( $tasks->get()->keyBy('id') as $task) {
                $taskNew = $task;
                $taskNew->user = $users->get($task->user_id);
                $taskNew->category = $categories->get($task->category_id);
                $item->tasks[] = $taskNew;
            }
    

        return $item;
    }
}