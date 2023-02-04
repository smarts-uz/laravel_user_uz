<?php

namespace App\Http\Controllers\Task;

use App\Models\TaskElastic;
use App\Models\Task;
use Elastic\ScoutDriverPlus\Support\Query;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use TCG\Voyager\Models\Category;
use Illuminate\Http\Request;
use TCG\Voyager\Http\Controllers\VoyagerBaseController;
use App\Services\Task\SearchService;
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

        $userId = auth()->id();
        $auth_response = auth()->check();
        $filter = $request->get('filter');
        $item = $this->service->task_service($auth_response, $userId, $task, $filter);

        return view('task.detailed-tasks',
            [
                'review_description' => $item->review_description,
                'task'               => $task,
                'created'            => $item->created,
                'end'                => $item->end,
                'start'              => $item->start,
                'complianceType'     => $item->complianceType,
                'same_tasks'         => $item->same_tasks,
                'auth_response'      => $item->auth_response,
                'selected'           => $item->selected,
                'responses'          => $item->responses,
                'addresses'          => $item->addresses,
                'top_users'          => $item->top_users,
                'respons_reviews'    => $item->respons_reviews
            ]);
    }

    public function compliance_save(Request $request)
    {
        $this->service->comlianse_saveS($request);
        return redirect()->back();
    }

    public function task_map(Task $task)
    {
        return $task->addresses;
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
        if ($task->responses_count) {
            abort(403, "No Permission");
        }
        $addresses = $task->addresses;
        return view('task.changetask', compact('task', 'addresses'));
    }

    public function search_new()
    {
        $agent = new Agent();
        $categories = Category::query()->where('parent_id', null)->select('id', 'name')->orderBy("order")->get();
        $categories2 = Category::query()->where('parent_id', '<>', null)->select('id', 'parent_id', 'name')->orderBy("order")->get();
        if ($agent->isMobile()) {
            return view('search_task.mobile_task_search', compact('categories', 'categories2'));
        }

        return view('search_task.new_search', compact('categories', 'categories2'));
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
