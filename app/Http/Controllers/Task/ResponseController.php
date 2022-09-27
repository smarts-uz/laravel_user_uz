<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskResponse;
use App\Services\Task\ResponseService;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class ResponseController extends Controller
{
    private ResponseService $service;

    public function __construct()
    {
        $this->service = new ResponseService();
    }

    public function store(Request $request, Task $task)
    {
        $response = $this->service->store($request, $task);
        if (!$response['success'])
            Alert::error($response['message']);
        else
            Alert::success($response['message']);
        return back();
    }

    public function selectPerformer(TaskResponse $response)
    {
        $response = $this->service->selectPerformer($response);
        return back()->with($response);
    }
}
