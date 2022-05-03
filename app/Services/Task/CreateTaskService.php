<?php

namespace App\Services\Task;

use App\Http\Controllers\LoginController;
use App\Http\Requests\UserPhoneRequest;
use App\Http\Requests\UserRequest;
use App\Models\Address;
use App\Models\Category;
use App\Models\CustomField;
use App\Models\CustomFieldsValue;
use App\Models\Task;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\Response;
use Carbon\Carbon;
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

    public function name_store($data)
    {
        $task = Task::query()->create($data);
        return $this->get_custom($task);
    }

    public function get_custom($task)
    {
        $custom_fields = $this->custom_field_service->getCustomFieldsByRoute($task, CustomField::ROUTE_CUSTOM);
        if (!$task->category->customFieldsInCustom->count()) {
            if ($task->category->parent->remote) {
                return [
                    'route' => 'remote', 'task_id' => $task->id,
                    'custom_fields' => $this->custom_field_service->getCustomFieldsByRoute($task, CustomField::ROUTE_REMOTE)
                ];
            }
            if ($task->category->parent->double_address) {
                return [
                    'route' => 'address', 'address' => 2, 'task_id' => $task->id,
                    'custom_fields' => $this->custom_field_service->getCustomFieldsByRoute($task, CustomField::ROUTE_ADDRESS)
                ];
            }
            return [
                'route' => 'address', 'address' => 1, 'task_id' => $task->id,
                'custom_fields' => $this->custom_field_service->getCustomFieldsByRoute($task, CustomField::ROUTE_ADDRESS)
            ];
        }
        return ['route' => 'custom', 'task_id' => $task->id, 'custom_fields' => $custom_fields];
    }

    public function custom_store($data)
    {
        $task = Task::query()->findOrFail($data['task_id']);
        $this->service->attachCustomFieldsByRoute($task, CustomField::ROUTE_CUSTOM);
        if ($task->category->parent->remote) {
            return $this->get_remote($task);
        }
        return $this->get_address($task);
    }

    public function get_remote($task)
    {
        return [
            'route' => 'remote', 'task_id' => $task->id,
            'custom_fields' => $this->custom_field_service->getCustomFieldsByRoute($task, CustomField::ROUTE_REMOTE)
        ];
    }

    public function remote_store($data)
    {
        $task = Task::query()->findOrFail($data['task_id']);
        switch ($data['radio']) {
            case CustomField::ROUTE_ADDRESS:
                return $this->get_address($task);
                break;
            case CustomField::ROUTE_REMOTE:
                return $this->get_date($task);
            default:
                return [''];
        }
    }

    public function get_address($task)
    {
        $custom_fields = $this->custom_field_service->getCustomFieldsByRoute($task, CustomField::ROUTE_ADDRESS);
        if ($task->category->parent->double_address) {
            return ['route' => 'address', 'address' => 2, 'custom_fields' => $custom_fields];
        }
        return ['route' => 'address', 'address' => 1, 'custom_fields' => $custom_fields];
    }


    public function address_store($data)
    {
        $task = Task::query()->findOrFail($data['task_id']);
        foreach ($data['points'] as $point) {
            Address::query()->create([
                'task_id' => $data['task_id'],
                'location' => $point['location'],
                'latitude' => $point['latitude'],
                'longitude' => $point['longitude']
            ]);
        }
        $task->update(['coordinates' => $data['points'][0]['latitude'] . ',' . $data['points'][0]['longitude']]);
        $this->service->attachCustomFieldsByRoute($task, CustomField::ROUTE_ADDRESS);
        return $this->get_date($task);

    }

    public function get_date($task)
    {
        return [
            'route' => 'date', 'task_id' => $task->id,
            'custom_fields' => $this->custom_field_service->getCustomFieldsByRoute($task, CustomField::ROUTE_DATE)
        ];
    }

    public function date_store($data)
    {
        $task = Task::query()->findOrFail($data['task_id']);
        unset($data['task_id']);
        $task->update($data);
        $this->service->attachCustomFieldsByRoute($task, CustomField::ROUTE_DATE);
        return $this->get_budget($task);
    }

    public function get_budget($task)
    {
        return [
            'route' => 'budget', 'task_id' => $task->id, 'price' => Category::findOrFail($task->category_id)->max,
            'custom_fields' => $this->custom_field_service->getCustomFieldsByRoute($task, CustomField::ROUTE_BUDGET)
        ];
    }

    public function budget_store($data)
    {
        $task = Task::query()->findOrFail($data['task_id']);
        $task->budget = $data['amount'];
        $task->save();
        $this->service->attachCustomFieldsByRoute($task, CustomField::ROUTE_BUDGET);
        return $this->get_note($task);
    }

    public function get_note($task)
    {
        $custom_fields = $this->custom_field_service->getCustomFieldsByRoute($task, CustomField::ROUTE_NOTE);
        return ['route' => 'note', 'task_id' => $task->id, 'custom_fields' => $custom_fields];
    }

    public function note_store($data)
    {
        if ($data['docs'] == "on") {
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
            'route' => 'contact', 'task_id' => $task->id,
            'custom_fields' => $this->custom_field_service->getCustomFieldsByRoute($task, CustomField::ROUTE_CONTACTS)
        ];
    }

    public function contact_store($data)
    {
        $user = auth()->user();
        $task = Task::query()->findOrFail($data['task_id']);
        unset($data['task_id']);
        if (!$user->is_phone_number_verified || $user->phone_number != $data['phone_number']) {
            $data['is_phone_number_verified'] = 0;
            $user->update($data);
            LoginController::send_verification('phone', $user);
            return $this->get_verify($task, $user);
        }

        $task->status = 1;
        $task->user_id = $user->id;
        $task->phone = $user->phone_number;
        $task->save();

        NotificationService::sendTaskNotification($task, $user->id);

        return [
            'task_id' => $task->id,
            'message' => 'Task successfully created'
        ];
    }

    public function get_verify($task, $user)
    {
        return ['route' => 'verify', 'task_id' => $task->id, 'user' => $user, $user->verify_code];
    }

    public function verification($data)
    {
        $task = Task::query()->findOrFail($data['task_id']);
        $user = User::query()->findOrFail($data['user_id']);
        if ($data['sms_otp'] == $user->verify_code) {
            if (strtotime($user->verify_expiration) >= strtotime(Carbon::now())) {
                $user->update(['is_phone_number_verified' => 1]);
                $task->update(['status' => 1, 'user_id' => $user->id, 'phone' => $user->phone_number]);

                // send notification
                NotificationService::sendTaskNotification($task, $user->id);

                return $this->success([
                    'task_id' => $task->id
                ], 'Successfully verified');
            } else {
                return $this->fail([
                    'sms_otp' => ['expired_message']
                ], 'Validation errors');
            }
        } else {
            return $this->fail([
                'sms_otp' => ['incorrect_message']
            ], 'Validation errors');
        }
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
