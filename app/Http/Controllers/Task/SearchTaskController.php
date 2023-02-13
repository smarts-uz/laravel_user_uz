<?php

namespace App\Http\Controllers\Task;

use App\Models\TaskElastic;
use App\Models\Task;
use App\Services\Task\UpdateTaskService;
use Elastic\ScoutDriverPlus\Support\Query;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
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
     * @param $task
     * @param Request $request
     */
    public function task(int $task_id, Request $request)
    {
        $task = Task::select('user_id')->find($task_id);
        $empty = empty($task);
        if ((!$empty && !$task->user_id) || $empty) {
            abort(404);
        }

        $userId = auth()->id();
        $auth_response = auth()->check();
        $filter = $request->get('filter');
        $result = $this->service->task_service($auth_response, $userId, $task_id, $filter);
        $item = $result['item'];
        $tasks = $result['task'];
        return view('task.detailed-tasks',
            [
                'review_description' => $item->review_description,
                'task'               => $tasks,
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

    /**
     *
     * Function  compliance_save
     * @param Request $request
     * @return  RedirectResponse
     */
    public function compliance_save(Request $request)
    {
        $data = $request->all();
        $this->service->comlianse_saveS($data);
        return redirect()->back();
    }

    /**
     *
     * Function  task_map
     * @param Task $task
     * @return  mixed
     */
    public function task_map(Task $task)
    {
        return $task->addresses;
    }

    /**
     *
     * Function  delete_task
     * @param Task $task
     * @return  RedirectResponse
     */
    public function delete_task(Task $task): RedirectResponse
    {
        $task->status = Task::STATUS_CANCELLED;
        $task->save();
        return redirect()->back();
    }

    /**
     *
     * Function  changeTask
     * @param Task $task
     * @return  Application|Factory|View
     */
    public function changeTask(Task $task)
    {
        (new UpdateTaskService)->taskGuard($task);
        if ($task->responses_count) {
            abort(403, "No Permission");
        }
        $addresses = $task->addresses;
        return view('task.changetask', compact('task', 'addresses'));
    }

    /**
     *
     * Function  search_new
     * @return  Application|Factory|View
     */
    public function search_new()
    {
        $lang = Session::get('lang');
        $allCategories = $this->service->search_new($lang);
        $agent = new Agent();
        $categories = $allCategories['categories'];
        $categories2 = $allCategories['categories2'];

        if ($agent->isMobile()) {
            return view('search_task.mobile_task_search', compact('categories', 'categories2'));
        }

        return view('search_task.new_search', compact('categories', 'categories2'));
    }

    /**
     *
     * Function  search_new2
     * @param Request $request
     * @return  JsonResponse
     */
    public function search_new2(Request $request): \Illuminate\Http\JsonResponse
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
