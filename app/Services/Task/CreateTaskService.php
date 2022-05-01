<?php

namespace App\Services\Task;

use App\Http\Controllers\LoginController;
use App\Http\Requests\TaskDateRequest;
use App\Http\Requests\UserPhoneRequest;
use App\Http\Requests\UserRequest;
use App\Http\Resources\CustomFiledResource;
use App\Models\Category;
use App\Models\CustomField;
use App\Models\CustomFieldsValue;
use App\Models\Task;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;

class CreateTaskService
{
    use Response;

    /**
     * @var CreateService
     */
    private $service;
    /**
     * @var CustomFieldService
     */
    private $custom_field_service;

    public function __construct()
    {
        $this->service = new CreateService();
        $this->custom_field_service = new CustomFieldService();
    }

    public function name_store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'category_id' => 'required'
        ]);
        $task = Task::query()->create($data);

        return $this->get_custom($task);
    }

    public function get_custom($task)
    {
        $custom_fields = $this->custom_field_service->getCustomFieldsByRoute($task, CustomField::ROUTE_CUSTOM);
        if (!$task->category->customFieldsInCustom->count()) {
            if ($task->category->parent->remote) {
                return ['route' => 'remote'];
            }
            if ($task->category->parent->double_address) {
                return ['route' => 'address', 'address' => 2];
            }
            return ['route' => 'address', 'address' => 1];
        }
        return ['route' => 'custom', 'custom_fields' => $custom_fields];
    }

    public function custom_store(Request $request)
    {
        $task = Task::query()->findOrFail($request->get('task_id'));
        $this->service->attachCustomFieldsByRoute($task, CustomField::ROUTE_CUSTOM);

        if ($task->category->parent->remote) {
            return $this->get_remote($task);
        }

        return redirect()->route('task.create.address', $task->id);
    }

    public function get_remote($task)
    {
        return [
            'route' => 'remote',
            'custom_fields' => $this->custom_field_service->getCustomFieldsByRoute($task, CustomField::ROUTE_REMOTE)
        ];
    }

    public function remote_store(Request $request)
    {
        $data = $request->validate(['task_id' => 'required', 'radio' => 'required']);
        $task = Task::query()->findOrFail($data['task_id']);
        switch ($data['radio']) {
            case CustomField::ROUTE_ADDRESS:
                return $this->get_address($task);
                break;
            case CustomField::ROUTE_REMOTE:
                return $this->get_date($task);
        }

        return back();
    }

    public function get_address($task)
    {
        $custom_fields = $this->custom_field_service->getCustomFieldsByRoute($task, CustomField::ROUTE_ADDRESS);
        if ($task->category->parent->double_address) {
            return ['route' => 'address', 'address' => 2, 'custom_fields' => $custom_fields];
        }
        return ['route' => 'address', 'address' => 1, 'custom_fields' => $custom_fields];
    }


    public function address_store(Request $request)
    {
        $task = Task::query()->findOrFail($request->get('task_id'));
        $task->update($this->service->addAdditionalAddress($task, $request));
        $this->service->attachCustomFieldsByRoute($task, CustomField::ROUTE_ADDRESS);
        return $this->get_date($task);

    }

    public function get_date($task)
    {
        return [
            'route' => 'date',
            'custom_fields' => $this->custom_field_service->getCustomFieldsByRoute($task, CustomField::ROUTE_DATE)
        ];
    }

    public function date_store($request)
    {
        $request->validate(['date_type' => 'required', 'task_id' => 'required']);
        $request_tasks = app('App\Http\Requests\TaskDateRequest');
        $request_tasks->setValidator(Validator::make($request->all(), $request_tasks->rules(), $request_tasks->messages()));
        $data = $request_tasks->validated();

        $task = Task::query()->findOrFail($data['task_id']);
        $task->update($data);
        $this->service->attachCustomFieldsByRoute($task, CustomField::ROUTE_DATE);

        return $this->get_budget($task);
    }

    public function get_budget($task)
    {
        $category = Category::findOrFail($task->category_id);
        $custom_fields = $this->custom_field_service->getCustomFieldsByRoute($task, CustomField::ROUTE_BUDGET);

        return ['route' => 'budget', 'custom_fields' => $custom_fields];
//        return view('create.budget', compact('task', 'category', 'custom_fields'));
    }

    public function budget_store(Request $request)
    {
        $data = $request->validate(['task_id' => 'required', 'amount1']);
        $task = Task::query()->findOrFail($data['task_id']);
        $task->budget = preg_replace('/[^0-9.]+/', '', $request->amount1);
        $task->save();
        $this->service->attachCustomFieldsByRoute($task, CustomField::ROUTE_BUDGET);
        return $this->get_note($task);

    }

    public function get_note($task)
    {
        $custom_fields = $this->custom_field_service->getCustomFieldsByRoute($task, CustomField::ROUTE_NOTE);
        return ['route' => 'note', 'custom_fields' => $custom_fields];
//        return view('create.notes', compact('task', 'custom_fields'));
    }

    public function images_store(Task $task, Request $request)
    {

        $imgData = json_decode($task->photos);

        if ($request->hasFile('images')) {

            $files = $request->file('images');
            $name = Storage::put('public/uploads', $files);
            $name = str_replace('public/', '', $name);
            $imgData[] = $name;

        }

        $task->photos = json_encode($imgData);
        $task->save();
    }

    public function uploadImage(Task $task, Request $request)
    {
        $folder_task = Task::orderBy('created_at', 'desc')->first();
        if ($request->file()) {
            $fileName = time() . '_' . $request->file->getClientOriginalName();
            $filePath = $request->file('file')
                ->move(public_path("storage/Uploads/{$folder_task->name}"), $fileName);

            $fileModelname = time() . '_' . $request->file->getClientOriginalName();
            $fileModelfile_path = '/storage/' . $filePath;
            return response()->json([
                "success" => true,
                "message" => "File successfully uploaded",
                "file" => $fileName
            ]);
        }
//        $this->note_store();
    }

    public function note_store(Request $request)
    {
        $data = $request->validate([
            'task_id' => 'required',
            'description' => 'required|string',
            'oplata' => 'required',
        ]);
        if ($request['docs'] == "on") {
            $data['docs'] = 1;
        } else {
            $data['docs'] = 0;
        }
        $task = Task::query()->findOrFail($data['task_id']);
        $task->update($data);
        return $this->get_contact($task);
    }


    public function get_contact($task)
    {
        return [
            'route' => 'contact',
            'custom_fields' => $this->custom_field_service->getCustomFieldsByRoute($task, CustomField::ROUTE_CONTACTS)
        ];
    }


    public function contact_store(Request $request)
    {
        $user = auth()->user();
        $data = $request->validate([
            'phone_number' => 'required|integer|min:9|unique:users,phone_number,' . $user->id,
            'task_id' => 'required',
        ]);
        $task = Task::query()->findOrFail($data['task_id']);
        if (!$user->is_phone_number_verified || $user->phone_number != $data['phone_number']) {
            $data['is_phone_number_verified'] = 0;
            $user->update($data);
            LoginController::send_verification('phone', $user);
            return $this->verify($task, $user);
        }

        $task->status = 1;
        $task->user_id = $user->id;
        $task->phone = $user->phone_number;
        $task->save();

        NotificationService::sendTaskNotification($task, $user->id);

        return redirect()->route('searchTask.task', $task->id);
    }

    public function contact_register(Task $task, UserRequest $request)
    {
        $data = $request->validated();

        $data['password'] = Hash::make('login123');

        $user = User::create($data);
        LoginController::send_verification('phone', $user);

        return redirect()->route('task.create.verify', ['task' => $task->id, 'user' => $user->id]);

    }

    public function contact_login(Task $task, UserPhoneRequest $request)
    {
        $request->validated();
        $user = User::query()->where('phone_number', $request->phone_number)->first();
        LoginController::send_verification('phone', $user);
        return redirect()->route('task.create.verify', ['task' => $task->id, 'user' => $user->id])->with(['not-show', 'true']);

    }

    public function verify($task, $user)
    {
        return view('create.verify', compact('task', 'user'));
    }

    public function deletetask(Task $task)
    {
        $task->delete();
        CustomFieldsValue::where('task_id', $task)->delete();
    }

    public function deleteAllImages(Task $task)
    {
        taskGuard($task);
        $task->photos = null;
        $task->save();
        Alert::success('success');
        return back();
    }

    public function date_validator($request)
    {
        $request->validate(['date_type' => 'required']);
        $rules = [];
        switch ($request->get('date_type')) {
            case 1:
                $rules = [
                    'start_date' => 'required|date',
                    'date_type' => 'required'
                ];
                break;
            case 2:
                $rules = [
                    'end_date' => 'required|date',
                    'date_type' => 'required'
                ];
                break;
            case 3:
                $rules = [
                    'start_date' => 'required|date',
                    'end_date' => 'required|date',
                    'date_type' => 'required'

                ];
                break;
        }
        $rules['task_id'] = 'required';
        return Validator::make($request->all(), $rules, [
            "start_date.required" => __('dateTime.start_date.required'),
            "start_date.date" => __('dateTime.start_date.date'),
            "end_date.required" => __('dateTime.end_date.required'),
            "end_date.date" => __('dateTime.end_date.date'),
        ]);
    }
}
