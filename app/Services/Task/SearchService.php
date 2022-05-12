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
use Illuminate\Support\Arr;
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

    public function search_new_service($arr_check, $filter = '', $suggest = '', $price=null, $remjob, $noresp, $radius): array
    {

        $users = User::all()->keyBy('id');
        $categories = Category::all()->keyBy('id');
        $adresses = Address::all()->keyBy('id');

        $tasks = Task::query()
            ->when($filter !== '', function ($query) use ($filter) {
                $query->where('name', 'like', "%{$filter}%");
            })
             ->when($suggest!=='', function ($query) use ($suggest) {
                $query->whereHas('addresses', function ($query) use($suggest) {
                    $query->where('location', 'like', "%{$suggest}%");});
            })
            ->when($price!==null, function ($query) use ($price) {
              $query->where('budget', '>=', $price*0.8)
                ->where('budget', '<=', $price*1.2);
            })
            ->when($arr_check, function ($query) use ($arr_check) {
                $query->whereIn('category_id', $arr_check);
            })
            ->when($remjob, function ($query) {
                $query->whereNull('address');
            })
            ->get()
            ->keyBy('id');

        $return = [];

        foreach ($tasks as $task) {
            $item = new SearchNewItem();
           $item=$task;
           if ($users->contains($task->user_id)) {
               $item->user_name = $users->get($task->user_id)->name;
            }
            
            $allAdresses = $adresses->where('task_id', $task->id);
            $mainAdress = Arr::first($allAdresses);
            $item->address_main = Arr::get($mainAdress, 'location');

            if ($categories->contains($task->category_id)) {
                $item->category_icon = $categories->get($task->category_id)->ico;
                $item->category_name = $categories->get($task->category_id)->name;
            }
            $return[] = $item;
        }

        return $return;
    }
}