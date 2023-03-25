<?php

namespace App\Http\Controllers\Task;

use App\Models\Task;
use App\Services\Task\{UpdateTaskService, SearchService};
use Illuminate\Contracts\{Foundation\Application, View\Factory, View\View};
use Illuminate\Http\{JsonResponse, RedirectResponse, Request};
use Illuminate\Support\Facades\Session;
use TCG\Voyager\Http\Controllers\VoyagerBaseController;
use Jenssegers\Agent\Agent;

class SearchTaskController extends VoyagerBaseController
{
    private SearchService $service;

    public function __construct()
    {
        $this->service = new SearchService();
    }


    public function taskNames(Request $request): string
    {
        $name = $request->get('name');
        return $this->service->taskNames($name);
    }


    public function task(int $task_id, Request $request): Factory|View|Application
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

    public function compliance_save(Request $request): RedirectResponse
    {
        $data = $request->all();
        $this->service->comlianse_saveS($data);
        return redirect()->back();
    }

    public function task_map(Task $task): mixed
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

    public function changeTask(Task $task): View|Factory|Application
    {
        (new UpdateTaskService)->taskGuard($task);
        if ($task->responses_count) {
            abort(403, "No Permission");
        }
        $addresses = $task->addresses;
        return view('task.changetask', compact('task', 'addresses'));
    }


    public function search_new(): Factory|View|Application
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


    public function search_new2(Request $request): JsonResponse
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
