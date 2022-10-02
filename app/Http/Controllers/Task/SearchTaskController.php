<?php

namespace App\Http\Controllers\Task;

use App\Models\TaskElastic;
use App\Models\TaskResponse;
use App\Models\Task;
use Elastic\ScoutDriverPlus\Support\Query;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use TCG\Voyager\Models\Category;
use Illuminate\Http\Request;
use TCG\Voyager\Http\Controllers\VoyagerBaseController;
use App\Services\Task\SearchService;
use Carbon\Carbon;
use Jenssegers\Agent\Agent;

class SearchTaskController extends VoyagerBaseController
{
    private SearchService $service;

    public function __construct()
    {
        $this->service = new SearchService();
    }

    /**
     * @param Request $request
     * @return string
     */
    public function taskNames(Request $request): string
    {
        $name = $request->get('name');
        $query = Query::wildcard()
            ->field('name')
            ->value('*' . $name . '*');
        $searchResult = TaskElastic::searchQuery($query)->execute();
        $tasks = $searchResult->models();
        $options = "";
        foreach ($tasks as $task) {
            $options .= "<option value='$task->name' id='$task->category_id'>$task->name</option>";
        }
        return $options;
    }

    /**
     * @param Task $task
     * @param Request $request
     * @return Factory|View|Application
     */
    public function task(Task $task, Request $request): Factory|View|Application
    {
        if (!$task->user_id) {
            abort(404);
        }
        $review = null;
        if ($task->reviews_count === 2) $review = true;

        $user_id = auth()->id();
        if (auth()->check() && $user_id !== $task->user_id) {
            $viewed_tasks = Cache::get('user_viewed_tasks' . $user_id) ?? [];
            if (!in_array($task->id, $viewed_tasks)) {
                $viewed_tasks[] = $task->id;
            }
            Cache::put('user_viewed_tasks' . $user_id, $viewed_tasks);

            $task->views++;
            $task->save();
        }


        $value = Carbon::parse($task->created_at)->locale(getLocale());
        $value->minute < 10 ? $minut = '0' . $value->minute : $minut = $value->minute;
        $day = $value == now()->toDateTimeString() ? "Bugun" : "$value->day-$value->monthName";
        $created = "$day  $value->noZeroHour:$minut";

        $value = Carbon::parse($task->end_date)->locale(getLocale());
        $value->minute < 10 ? $minut = '0' . $value->minute : $minut = $value->minute;
        $end = "$value->day-$value->monthName  $value->noZeroHour:$minut";

        $value = Carbon::parse($task->start_date)->locale(getLocale());
        $value->minute < 10 ? $minut = '0' . $value->minute : $minut = $value->minute;
        $day = $value == now()->toDateTimeString() ? "Bugun" : "$value->day-$value->monthName";
        $start = "$day  $value->noZeroHour:$minut";

        $auth_response = auth()->check();
        $userId = auth()->id();
        $item = $this->service->task_service($auth_response, $userId, $task);
        $filter = $request->get('filter');
        $responses = match ($filter) {
            'rating' => TaskResponse::query()->join('users', 'task_responses.performer_id', '=', 'users.id')
                ->where('task_responses.task_id', '=', $task->id)->orderByDesc('users.review_rating')->get(),
            'date' => $item->responses->orderByDesc('created_at')->get(),
            'reviews' => TaskResponse::query()->join('users', 'task_responses.performer_id', '=', 'users.id')
                ->where('task_responses.task_id', '=', $task->id)->orderByDesc('users.reviews')->get(),
            default => $item->responses->get(),
        };
        return view('task.detailed-tasks',
            [
                'review_description' => $item->review_description, 'task' => $task,
                'created' => $created, 'end' => $end, 'start' => $start, 'review' => $review,
                'complianceType' => $item->complianceType, 'same_tasks' => $item->same_tasks,
                'auth_response' => $item->auth_response, 'selected' => $item->selected,
                'responses' => $responses, 'addresses' => $item->addresses,
                'top_users' => $item->top_users, 'respons_reviews' => $item->respons_reviews
            ]);
    }

    public function compliance_save(Request $request)
    {
        $this->service->comlianse_saveS($request);
        return redirect()->back();
    }

    public function delete_task(Task $task)
    {
        $task->status = Task::STATUS_CANCELLED;
        $task->save();
        return redirect()->back();
    }

    public function changeTask(Task $task)
    {
        taskGuard($task);
        if ($task->responses_count)
            abort(403, "No Permission");
        $addresses = $task->addresses;
        return view('task.changetask', compact('task', 'addresses'));
    }

    public function search_new()
    {
        $agent = new Agent();
        $categories = Category::query()->where('parent_id', null)->select('id', 'name')->get();
        $categories2 = Category::query()->where('parent_id', '<>', null)->select('id', 'parent_id', 'name')->get();
        if ($agent->isMobile()) {
            return view('search_task.mobile_task_search', compact('categories', 'categories2'));
        } else {
            return view('search_task.new_search', compact('categories', 'categories2'));
        }
    }

    public function search_new2(Request $request)
    {

        $data = collect($request->get('data'))->keyBy('name');
        $filter = $data['filter']['value'] ?? null;
        $suggest = $data['suggest']['value'] ?? null;

        $lat = $data['user_lat']['value'] ?? null;
        $lon = $data['user_long']['value'] ?? null;

        $radius = $data["radius"]['value'] ?? null;
        $price = $data["price"]['value'] ?? null;

        $filterByStartDate = $data["sortBySearch"]['value'] ?? false;
        $arr_check = $data->except(['filter', 'suggest', 'user_lat', 'user_long', "radius", "price", 'remjob', 'noresp'])->pluck('name');
        $remjob = $data['remjob']['value'] ?? false;
        $noresp = $data['noresp']['value'] ?? false;

        $tasks = $this->service->search_new_service($arr_check, $filter, $suggest, $price, $remjob, $noresp, $radius, $lat, $lon, $filterByStartDate);

        $html = view("search_task.tasks", ['tasks' => $tasks[0]])->render();
        return response()->json(array('dataForMap' => $tasks[1], 'html' => $html));

    }
}
