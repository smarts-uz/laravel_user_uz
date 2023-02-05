<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaskResponseRequest;
use App\Models\Task;
use App\Models\TaskResponse;
use App\Models\User;
use App\Services\Task\ResponseService;
use RealRashid\SweetAlert\Facades\Alert;

class ResponseController extends Controller
{
    private ResponseService $service;

    public function __construct()
    {
        $this->service = new ResponseService();
    }

    public function store(TaskResponseRequest $request, Task $task)
    {
        $data = $request->validated();
        /** @var User $auth_user */
        $auth_user = auth()->user();
        $response = $this->service->store($data, $task, $auth_user);
        if (!$response['success']) {
            Alert::error($response['message']);
        }
        else {
            Alert::success($response['message']);
        }
        return back();
    }

    public function selectPerformer(TaskResponse $response)
    {
        $responses = $this->service->selectPerformer($response);
        return back()->with($responses);
    }
}
