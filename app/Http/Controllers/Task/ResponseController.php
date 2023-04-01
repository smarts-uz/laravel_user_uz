<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaskResponseRequest;
use App\Models\{Task, TaskResponse, User};
use App\Services\Task\ResponseService;
use Illuminate\Http\RedirectResponse;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use RealRashid\SweetAlert\Facades\Alert;

class ResponseController extends Controller
{
    private ResponseService $service;

    public function __construct()
    {
        $this->service = new ResponseService();
    }

    public function store(TaskResponseRequest $request, Task $task): RedirectResponse
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

    /**
     * @param TaskResponse $response
     * @return RedirectResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function selectPerformer(TaskResponse $response): RedirectResponse
    {
        $responses = $this->service->selectPerformer($response);
        return back()->with($responses);
    }
}
