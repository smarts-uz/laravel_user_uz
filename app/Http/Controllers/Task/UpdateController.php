<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\UpdateRequest;
use App\Models\ChMessage;
use App\Models\Task;
use App\Services\Task\CreateService;
use App\Services\Task\ReviewService;
use App\Services\Task\UpdateTaskService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use RealRashid\SweetAlert\Facades\Alert;

class UpdateController extends Controller
{
    protected CreateService $service;

    public function __construct()
    {
        $this->service = new CreateService();
    }

    public function change(UpdateRequest $request, Task $task)
    {
        (new UpdateTaskService)->taskGuard($task);
        if ($task->responses_count)
            abort(403, "No Permission");
        if (!(int)$task->remote === 1) {
            $request->validate([
                'location0' => 'required',
                'coordinates0' => 'required',
            ],[
                'location0.required' => __('login.name.required'),
                'coordinates0.required' => __('login.name.required'),
            ]);
        }

        $data = $request->validated();
        $task->addresses()->delete();
        $images = array_merge(json_decode(session()->has('images') ? session('images') : '[]'), json_decode($task->photos)??[]);
        session()->forget('images');
        $data['photos'] = json_encode($images);
        $requestAll = $request->all();
        $data['coordinates'] = $this->service->addAdditionalAddress($task->id, $requestAll);
        unset($data['location0'], $data['coordinates0']);
        $task->update($data);
        $note = $request->validate([
            'description' => 'required|string',
            'oplata' => 'required',
        ],[
            'description.required' => __('login.name.required'),
            'oplata.required' => __('login.name.required'),
            'description.string' => __('login.name.required')
            ]);
        if ($request['docs'] === "on") {
            $note['docs'] = 1;
        } else {
            $note['docs'] = 0;
        }
        $task->update($note);
        $this->service->syncCustomFields($task);
        Alert::success(__('Изменения сохранены'));

        return redirect()->route('searchTask.task', $task->id);
    }

    public function deleteImage(Request $request, Task $task)
    {
        (new UpdateTaskService)->taskGuard($task);
        $image = $request->get('image');
        File::delete(public_path() . '/storage/uploads/' . $image);
        $images = json_decode($task->photos);
        $updatedImages = array_diff($images, [$image]);
        $task->photos = json_encode(array_values($updatedImages));
        $task->save();
        return true;
    }

    public function completed(Task $task)
    {
        $data = [
            'status' => Task::STATUS_COMPLETE
        ];

        ChMessage::query()->where('from_id', $task->user_id)->where('to_id', $task->performer_id)->delete();
        ChMessage::query()->where('to_id', $task->user_id)->where('from_id', $task->performer_id)->delete();

        $task->update($data);
        Alert::success(__('Успешно сохранено'));
        return back();

    }

    public function not_completed(Request $request, Task $task)
    {
        $request->validate(['reason' => 'required']);

        $task->update(['status' => Task::STATUS_NOT_COMPLETED, 'not_completed_reason' => $request->get('reason')]);
        Alert::success(__('Успешно сохранено'));
        return back();
    }

    public function sendReview(Task $task, Request $request)
    {
        (new UpdateTaskService)->taskGuard($task);

        try {
            ReviewService::sendReview($task, $request, true);
        } catch (\Exception $e) {
            DB::rollBack();
        }
        return back();
    }


}
