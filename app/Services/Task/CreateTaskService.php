<?php

namespace App\Services\Task;

use App\Models\Address;
use App\Models\Category;
use App\Models\CustomField;
use App\Models\CustomFieldsValue;
use App\Models\Task;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\Response;
use App\Services\VerificationService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use JetBrains\PhpStorm\ArrayShape;

class CreateTaskService
{
    use Response;

    /**
     * @var CreateService
     */
    private CreateService $service;
    /**
     * @var CustomFieldService
     */
    private CustomFieldService $custom_field_service;

    public function __construct()
    {
        $this->service = new CreateService();
        $this->custom_field_service = new CustomFieldService();
    }

    /**
     * Create and save the task name
     *
     * @param array $data // Validated request data from mobile
     * @return array $result //Value Returned (use void if doesn't return)
     */
    public function name_store(array $data): array
    {
        $data['user_id'] = auth()->id();
        $task = Task::query()->create($data);
        /** @var User $user */
        $user = auth()->user();
        $user->active_task = $task->id;
        $user->save();
        return $this->get_custom($task);
    }

    /**
     * Retrieve next step with additional fields - e.g - remote, address or custom
     *
     * @param object $task // Task model object
     * @return array //Value Returned
     */
    public function get_custom(object $task): array
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

    /**
     * Save custom field values by request
     *
     * @param $data // Validated request data from mobile
     * @param $request // All request params
     * @return array //Value Returned
     */
    public function custom_store($data, $request): array
    {
        /** @var Task $task */
        $task = Task::query()->findOrFail($data['task_id']);
        $this->attachCustomFieldsByRoute($task, CustomField::ROUTE_CUSTOM, $request);
        $category = $task->category;
        /** @var User $user */
        $user = auth()->user();
        $user->active_step = 6;
        $user->save();
        if ($category->parent->remote) {
            return $this->get_remote($task);
        }
        return $this->get_address($task);
    }

    /**
     * Retrieve next step with additional fields
     *
     * @param object $task // Task model object
     * @return array //Value Returned
     */
    #[ArrayShape(['route' => "string", 'task_id' => "int", 'steps' => "int", 'custom_fields' => "array"])]
    public function get_remote(object $task): array
    {
        return [
            'route' => 'remote', 'task_id' => $task->id, 'steps' => 5,
            'custom_fields' => $this->custom_field_service->getCustomFieldsByRoute($task, CustomField::ROUTE_REMOTE)
        ];
    }

    /**
     * Save remote s values by request
     *
     * @param $data // Validated request data from mobile
     * @return array //Value Returned
     */
    public function remote_store($data): array
    {
        /** @var Task $task */
        $task = Task::query()->findOrFail($data['task_id']);
        /** @var User $user */
        $user = auth()->user();
        $user->active_step = 5;
        $user->save();
        switch ($data['radio'] ){
            case CustomField::ROUTE_ADDRESS :
                return $this->get_address($task);
            case CustomField::ROUTE_REMOTE :
                $task->remote = 1;
                $task->save();
                return $this->get_date($task);
            default :
                return [];
        }

    }

    /**
     * Retrieve next step with additional fields
     *
     * @param object $task // Task model object
     * @return array //Value Returned
     */
    #[ArrayShape(['route' => "string", 'address' => "int", 'steps' => "int", 'custom_fields' => "array"])]
    public function get_address(object $task): array
    {
        $custom_fields = $this->custom_field_service->getCustomFieldsByRoute($task, CustomField::ROUTE_ADDRESS);
        if ($task->category->parent->double_address) {
            return ['route' => 'address', 'address' => 2, 'steps' => 4, 'custom_fields' => $custom_fields];
        }
        return ['route' => 'address', 'address' => 1, 'steps' => 4, 'custom_fields' => $custom_fields];
    }

    /**
     * Save remote s values by request
     *
     * @param $data // Validated request data from mobile
     * @return array //Value Returned
     */
    #[ArrayShape([])]
    public function address_store($data): array
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
            if ($i === 0) {
                $address['default'] = 1;
            }
            Address::query()->create($address);
        }
        /** @var User $user */
        $user = auth()->user();
        $user->active_step = 4;
        $user->save();
        $task->update([
            'coordinates' => $data['points'][0]['latitude'] . ',' . $data['points'][0]['longitude']
        ]);
        return $this->get_date($task);

    }

    /**
     * Retrieve next step with additional fields
     *
     * @param $task // Task model object
     * @return array //Value Returned
     */
    #[ArrayShape(['route' => "string", 'task_id' => "", 'steps' => "int", 'custom_fields' => "array"])]
    public function get_date($task): array
    {
        return [
            'route' => 'date', 'task_id' => $task->id, 'steps' => 3,
            'custom_fields' => $this->custom_field_service->getCustomFieldsByRoute($task, CustomField::ROUTE_DATE)
        ];
    }

    /**
     * Save remote s values by request
     *
     * @param $data // Validated request data from mobile
     * @return array //Value Returned
     */
    #[ArrayShape([])]
    public function date_store($data): array
    {
        $task = Task::query()->findOrFail($data['task_id']);
        unset($data['task_id']);
        $task->update($data);
        /** @var User $user */
        $user = auth()->user();
        $user->active_step = 3;
        $user->save();
        return $this->get_budget($task);
    }

    /**
     * Retrieve next step with additional fields
     *
     * @param $task // Task model object
     * @return array //Value Returned
     */
    #[ArrayShape([])]
    public function get_budget($task): array
    {
        return [
            'route' => 'budget', 'task_id' => $task->id, 'steps' => 2, 'price' => Category::query()->findOrFail($task->category_id)->max,
            'custom_fields' => $this->custom_field_service->getCustomFieldsByRoute($task, CustomField::ROUTE_BUDGET)
        ];
    }

    /**
     * Save remote s values by request
     *
     * @param $data // Validated request data from mobile
     * @return array //Value Returned
     */
    #[ArrayShape([])]
    public function budget_store($data): array
    {
        /** @var Task $task */
        $task = Task::query()->findOrFail($data['task_id']);
        $task->budget = $data['amount'];
        $task->oplata = $data['budget_type'];
        $task->save();
        /** @var User $user */
        $user = auth()->user();
        $user->active_step = 2;
        $user->save();
        return $this->get_note($task);
    }

    /**
     * Retrieve next step with additional fields
     *
     * @param $task // Task model object
     * @return array //Value Returned
     */
    #[ArrayShape([])]
    public function get_note($task): array
    {
        $custom_fields = $this->custom_field_service->getCustomFieldsByRoute($task, CustomField::ROUTE_NOTE);
        return ['route' => 'note', 'task_id' => $task->id, 'steps' => 1, 'custom_fields' => $custom_fields];
    }

    /**
     * Save remote s values by request
     *
     * @param $data // Validated request data from mobile
     * @return array //Value Returned
     */
    #[ArrayShape([])]
    public function note_store($data): array
    {
        $task = Task::query()->findOrFail($data['task_id']);
        unset($data['task_id']);
        $task->update($data);
        /** @var User $user */
        $user = auth()->user();
        $user->active_step = 1;
        $user->save();
        return $this->get_contact($task);
    }

    /**
     * Save remote s values by request
     *
     * @param $request
     * @return JsonResponse //Value Returned
     * @throws ValidationException
     */
    #[ArrayShape([])]
    public function image_store($request): JsonResponse
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
        /** @var Task $task */
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

    /**
     * Retrieve next step with additional fields
     *
     * @param $task // Task model object
     * @return array //Value Returned
     */
    #[ArrayShape([])]
    public function get_contact($task): array
    {
        return [
            'route' => 'contact', 'task_id' => $task->id, 'steps' => 0,
            'custom_fields' => $this->custom_field_service->getCustomFieldsByRoute($task, CustomField::ROUTE_CONTACTS)
        ];
    }

    /**
     * Save remote s values by request
     *
     * @param $data // Validated request data from mobile
     * @return array //Value Returned
     */
    #[ArrayShape([])]
    public function contact_store($data): array
    {
        /** @var User $user */
        $user = auth()->user();
        /** @var Task $task */
        $task = Task::query()->findOrFail($data['task_id']);
        unset($data['task_id']);
        switch (true){
            case (!$user->is_phone_number_verified && $user->phone_number !== $data['phone_number']):
                $data['is_phone_number_verified'] = 0;
                $data['phone_number'] = correctPhoneNumber($data['phone_number']);
                $user->update($data);
                VerificationService::send_verification('phone', $user, correctPhoneNumber($user->phone_number));
                return $this->get_verify($task, $user);
            case ($user->phone_number !== $data['phone_number']) :
                VerificationService::send_verification_for_task_phone($task, correctPhoneNumber($data['phone_number']));
                return $this->get_verify($task, $user);
            case (!$user->is_phone_number_verified) :
                VerificationService::send_verification('phone', $user, correctPhoneNumber($user->phone_number));
                return $this->get_verify($task, $user);
        }

        $task->status = 1;
        $task->user_id = $user->id;
        $task->phone = $user->phone_number;
        $task->save();
        $user->active_step = null;
        $user->active_task = null;
        $user->save();
        NotificationService::sendTaskNotification($task, $user->id);

        return [
            'task_id' => $task->id,
            'route' => 'end',
        ];
    }

    #[ArrayShape(['route' => "string", 'task_id' => "", 'user' => ""])]
    public function get_verify($task, $user): array
    {
        return ['route' => 'verify', 'task_id' => $task->id, 'user' => $user];
    }

    public function verification($data): JsonResponse
    {
        /** @var Task $task */
        $task = Task::query()->findOrFail($data['task_id']);
        /** @var User $user */
        $user = auth()->user();

        switch (true){
            case !$user->is_phone_number_verified && $data['sms_otp'] === $user->verify_code :
                if (strtotime($user->verify_expiration) >= strtotime(Carbon::now())) {
                    $user->update(['is_phone_number_verified' => 1,'active_step'=>null,'active_task'=>null]);
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
            case $data['sms_otp'] === $task->verify_code :
                if (strtotime($task->verify_expiration) >= strtotime(Carbon::now())) {
                    $task->update(['status' => 1, 'user_id' => $user->id]);
                    $user->active_step = null;
                    $user->active_task = null;
                    $user->save();
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
            default :
                return $this->fail([
                    'sms_otp' => ['incorrect_message']
                ], 'Validation errors');
        }
    }


    /**
     * Custom values store for API
     * @param $task
     * @param $routeName
     * @param $request
     */
    protected function attachCustomFieldsByRoute($task, $routeName, $request): void
    {
        foreach ($task->category->custom_fields()->where('route',$routeName)->get() as $data) {
            $value = $task->custom_field_values()->where('custom_field_id', $data->id)->first() ?? new CustomFieldsValue();
            $value->task_id = $task->id;
            $value->custom_field_id = $data->id;
            $arr = $data->name !== null ? Arr::get($request->all(), $data->name):null;
            $value->value = is_array($arr) ? json_encode($arr) : $arr;
            $value->save();
        }
    }
}
