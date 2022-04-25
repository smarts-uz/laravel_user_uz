<?php

namespace App\Http\Controllers\Task;

use App\Http\Requests\Task\UpdateRequest;
use App\Http\Resources\ResponseResource;
use App\Models\Compliance;
use App\Models\ComplianceType;
use App\Models\CustomField;
use App\Models\CustomFieldsValue;
use App\Models\WalletBalance;
use App\Services\Task\CreateService;
use App\Services\Task\CustomFieldService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use App\Models\User;
use App\Models\Task;
use App\Models\Notification;
use App\Models\TaskResponse;
use App\Models\Response;
use TCG\Voyager\Models\Category;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use TCG\Voyager\Http\Controllers\VoyagerBaseController;
use App\Services\Task\SearchService;


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
        $auth_response = auth()->check();
        $userId = auth()->id();
        $item = $this->service->task_service($auth_response, $userId, $task);

        return view('task.detailed-tasks',
        ['task' => $task, 'review' => $review, 'complianceType' => $item->complianceType, 'same_tasks' => $item->same_tasks,
        'auth_response' => $item->auth_response, 'selected' => $item->selected, 'responses' => $item->responses, 'addresses' => $item->addresses]);
    }

    public function comlianse_save(Request $request)
    {
        $this->service->comlianse_saveS($request);
        return redirect()->back();
    }

    public function delete_task(Task $task)
    {
        taskGuard($task);
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

}
