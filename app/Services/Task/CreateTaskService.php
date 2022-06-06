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
use Illuminate\Support\Arr;
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
        $data['user_id'] = auth()->id();
        $task = Task::query()->create($data);
        return $this->get_custom($task);
    }

    public function get_custom($task)
    {
        $custom_fields = $this->custom_field_service->getCustomFieldsByRoute($task, CustomField::ROUTE_CUSTOM);
        if (!$task->category->customFieldsInCustom->count()) {
            if ($task->category->parent->remote) {
                return [
                    'route' => 'remote', 'task_id' => $task->id, 'steps' => 5,
                    'custom_fields' => $this->custom_field_service->getCustomFieldsByRoute($task, CustomField::ROUTE_REMOTE)
                ];
            }
            if ($task->category->parent->double_address) {
                return [
                    'route' => 'address', 'address' => 2, 'task_id' => $task->id, 'steps' => 4,
                    'custom_fields' => $this->custom_field_service->getCustomFieldsByRoute($task, CustomField::ROUTE_ADDRESS)
                ];
            }
            return [
                'route' => 'address', 'address' => 1, 'task_id' => $task->id, 'steps' => 4,
                'custom_fields' => $this->custom_field_service->getCustomFieldsByRoute($task, CustomField::ROUTE_ADDRESS)
            ];
        }
        return ['route' => 'custom', 'task_id' => $task->id, 'steps' => 6, 'custom_fields' => $custom_fields];
    }

    public function custom_store($data, $request)
    {
        $task = Task::query()->findOrFail($data['task_id']);
        $this->attachCustomFieldsByRoute($task, CustomField::ROUTE_CUSTOM, $request);
        if ($task->category->parent->remote) {
            return $this->get_remote($task);
        }
        return $this->get_address($task);
    }

    public function get_remote($task)
    {
        return [
            'route' => 'remote', 'task_id' => $task->id, 'steps' => 5,
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
            return ['route' => 'address', 'address' => 2, 'steps' => 4, 'custom_fields' => $custom_fields];
        }
        return ['route' => 'address', 'address' => 1, 'steps' => 4, 'custom_fields' => $custom_fields];
    }

    public function address_store($data)
    {
        $task = Task::query()->findOrFail($data['task_id']);
        $length = min(count($data['points']), setting('site.max_address'));
        for ($i = 0; $i < $length; $i++) {
            $address = [
                'task_id' => $data['task_id'],
                'location' => $data['points'][$i]['location'],
                'latitude' => $data['points'][$i]['latitude'],
                'longitude' => $data['points'][$i]['longitude']
            ];
            if ($i == 0) {
                $address['default'] = 1;
            }
            Address::query()->create($address);
        }

        $task->update([
            //'address' => $data['points'][0]['location'],
            'coordinates' => $data['points'][0]['latitude'] . ',' . $data['points'][0]['longitude']
        ]);
        return $this->get_date($task);

    }

    public function get_date($task)
    {
        return [
            'route' => 'date', 'task_id' => $task->id, 'steps' => 3,
            'custom_fields' => $this->custom_field_service->getCustomFieldsByRoute($task, CustomField::ROUTE_DATE)
        ];
    }

    public function date_store($data)
    {
        $task = Task::query()->findOrFail($data['task_id']);
        unset($data['task_id']);
        $task->update($data);
        return $this->get_budget($task);
    }

    public function get_budget($task)
    {
        return [
            'route' => 'budget', 'task_id' => $task->id, 'steps' => 2, 'price' => Category::findOrFail($task->category_id)->max,
            'custom_fields' => $this->custom_field_service->getCustomFieldsByRoute($task, CustomField::ROUTE_BUDGET)
        ];
    }

    public function budget_store($data)
    {
        $task = Task::query()->findOrFail($data['task_id']);
        $task->budget = $data['amount'];
        $task->oplata = $data['budget_type'];
        $task->save();
        return $this->get_note($task);
    }

    public function get_note($task)
    {
        $custom_fields = $this->custom_field_service->getCustomFieldsByRoute($task, CustomField::ROUTE_NOTE);
        return ['route' => 'note', 'task_id' => $task->id, 'steps' => 1, 'custom_fields' => $custom_fields];
    }

    public function note_store($data)
    {
        $task = Task::query()->findOrFail($data['task_id']);
        unset($data['task_id']);
        $task->update($data);
        return $this->get_contact($task);
    }

    public function image_store($request)
    {
        $validator = Validator::make($request->all(), [
            'task_id' => 'required',
            'images.*' => 'required|image:jpeg,jpg,png,gif|max:10000'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'data' => $validator->errors()
            ]);
        }
        $data = $validator->validated();
        $task = Task::query()->findOrFail($data['task_id']);
        $imgData = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $uploadedImage) {
                $fileName = time() . '_' . $uploadedImage->getClientOriginalName();
                $uploadedImage->move(public_path("storage/uploads/"), $fileName);
                $imgData[] = $fileName;
            }
        }
        $task->photos = json_encode($imgData);
        $task->save();

        return response()->json([
            'success' => true,
            'data' => $task
        ]);
    }

    public function get_contact($task)
    {
        return [
            'route' => 'contact', 'task_id' => $task->id, 'steps' => 0,
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
            LoginController::send_verification('phone', $user, $user->phone_number);
            return $this->get_verify($task, $user);
        }

        $task->status = 1;
        $task->user_id = $user->id;
        $task->phone = $user->phone_number;
        $task->save();

        NotificationService::sendTaskNotification($task, $user->id);

        return [
            'task_id' => $task->id,
            'route' => 'end',
        ];
    }

    public function get_verify($task, $user)
    {
        return ['route' => 'verify', 'task_id' => $task->id, 'user' => $user];
    }

    public function verification($data)
    {
        $task = Task::query()->findOrFail($data['task_id']);
        $user = User::query()->where('phone_number', $data['phone_number'])->first();
        if ($data['sms_otp'] == $user->verify_code) {
            if (strtotime($user->verify_expiration) >= strtotime(Carbon::now())) {
                $user->update(['is_phone_number_verified' => 1]);
                $task->update(['status' => 1, 'user_id' => $user->id, 'phone' => $user->phone_number]);

                // send notification
                NotificationService::sendTaskNotification($task, $user->id);

                return $this->success([
                    'task_id' => $task->id,
                    'route' => 'end',
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
        LoginController::send_verification('phone', $user, $user->phone_number);

        return redirect()->route('task.create.verify', ['task' => $task->id, 'user' => $user->id]);

    }

    public function contact_login(Task $task, UserPhoneRequest $request)
    {
        $request->validated();
        $user = User::query()->where('phone_number', $request->phone_number)->first();
        LoginController::send_verification('phone', $user, $user->phone_number);
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



    /////////////////
    /// custom values store for API
    ///

    protected function attachCustomFieldsByRoute($task, $routeName, $request){
        foreach ($task->category->custom_fields()->where('route',$routeName)->get() as $data) {
            $value = $task->custom_field_values()->where('custom_field_id', $data->id)->first()?? new CustomFieldsValue();
            $value->task_id = $task->id;
            $value->custom_field_id = $data->id;
            $arr = $data->name !== null ? Arr::get($request->all(), $data->name):null;
            $value->value = is_array($arr) ? json_encode($arr) : $arr;
            $value->save();
        }
    }
}
