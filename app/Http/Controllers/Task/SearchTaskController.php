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

    public function my_tasks()
    {
        $user = auth()->user();
        $tasks = $user->tasks();
        $perform_tasks = Task::where('performer_id', $user->id())->get();
        $all_tasks = Task::where('user_id', $user->id)->where('performer_id', $user->id)->get();
        $categories = Category::get();
        return view('/task/mytasks', compact('tasks', 'categories', 'perform_tasks', 'all_tasks'));
    }

    public function task(Task $task)
    {
        if (!$task->user_id) {
            abort(404);
        }
        $complianceType = ComplianceType::all();

        $review = null;
        if ($task->reviews_count == 2) $review == true;
        if (auth()->check()) {
            $task->views++;
            $task->save();
        }
        $selected = $task->responses()->where('performer_id', $task->performer_id)->first();
        $responses = $selected ? $task->responses()->where('id', '!=', $selected->id)->get() : $task->responses;
        $auth_response = auth()->check() ? $task->responses()->where('performer_id', auth()->user()->id)->with('user')->first() : null;
        $same_tasks = $task->category->tasks()->where('id', '!=', $task->id)->where('status', Task::STATUS_OPEN)->take(10)->get();
        $addresses = $task->addresses;

        return view('task.detailed-tasks', compact('task', 'review', 'complianceType', 'same_tasks', 'auth_response', 'selected', 'responses', 'addresses'));
    }

    public function comlianse_save(Request $request)
    {
        $comp = new SearchService();
        $comp->comlianse_saveS($request);
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
            abort(403);
        $addresses = $task->addresses;
        //        dd($task);
        return view('task.changetask', compact('task', 'addresses'));
    }

}
