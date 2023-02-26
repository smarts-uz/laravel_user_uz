<?php

namespace App\Services\Task;

use App\Models\Address;
use App\Models\Category;
use App\Models\CustomField;
use App\Models\CustomFieldsValue;
use App\Models\Task;
use App\Models\User;
use App\Services\CustomService;
use App\Services\NotificationService;
use App\Services\Response;
use App\Services\VerificationService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use JetBrains\PhpStorm\ArrayShape;

class CreateTaskService
{
    public const Create_Name = 0;
    public const Create_Custom = 1;
    public const Create_Remote = 2;
    public const Create_Address = 3;
    public const Create_Date = 4;
    public const Create_Budget = 5;
    public const Create_Note = 6;
    public const Create_Contact = 7;

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
     *
     * Function  name_store
     * @param string $name
     * @param int $category_id
     * @param $user
     * @param int $user_id
     * @return  array
     */
    public function name_store(string $name, int $category_id, $user, int $user_id): array
    {
        $data = ["name" => $name, "category_id" => $category_id];
        $data['user_id'] = $user_id;
        $task = Task::create($data);
        $user->active_task = $task->id;
        $user->active_step = self::Create_Name;
        $user->save();
        return $this->get_custom($task->id);
    }

    /**
     *
     * Function  get_custom
     * @param int $task_id
     * @return  array
     */
    public function get_custom(int $task_id): array
    {
        $result = $this->custom_field_service->getCustomFieldsByRoute($task_id, CustomField::ROUTE_CUSTOM);
        $task = $result['task'];
        $custom_fields = $result['custom_fields'];
        if (!$task->category->customFieldsInCustom->count()) {
            if (!is_null($task->category->parent) && $task->category->parent->remote) {
                return [
                    'route' => 'remote', 'task_id' => $task->id, 'steps' => self::Create_Remote,
                    'custom_fields' => $custom_fields
                ];
            }
            if (!is_null($task->category->parent) && $task->category->parent->double_address) {
                return [
                    'route' => 'address', 'address' => 2, 'task_id' => $task_id, 'steps' => self::Create_Address,
                    'custom_fields' => $this->custom_field_service->getCustomFieldsByRoute($task_id, CustomField::ROUTE_ADDRESS)['custom_fields']
                ];
            }
            return [
                'route' => 'address', 'address' => 1, 'task_id' => $task_id, 'steps' => self::Create_Address,
                'custom_fields' => $this->custom_field_service->getCustomFieldsByRoute($task_id, CustomField::ROUTE_ADDRESS)['custom_fields']
            ];
        }
        return ['route' => 'custom', 'task_id' => $task_id, 'steps' => self::Create_Custom, 'custom_fields' => $custom_fields];
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
        $task = $this->attachCustomFieldsByRoute($data['task_id'], CustomField::ROUTE_CUSTOM, $request->all());
        $category = $task->category;
        /** @var User $user */
        $user = auth()->user();
        $user->active_step = self::Create_Custom;
        $user->save();
        if ($category->parent->remote) {
            return $this->get_remote($task->id);
        }
        return $this->get_address($task->id);
    }

    /**
     * Retrieve next step with additional fields
     *
     * @param object $task // Task model object
     * @return array //Value Returned
     */
    #[ArrayShape(['route' => "string", 'task_id' => "int", 'steps' => "int", 'custom_fields' => "array"])]
    public function get_remote(int $task_id): array
    {
        return [
            'route' => 'remote', 'task_id' => $task_id, 'steps' => self::Create_Remote,
            'custom_fields' => $this->custom_field_service->getCustomFieldsByRoute($task_id, CustomField::ROUTE_REMOTE)['custom_fields']
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
        $user->active_step = self::Create_Remote;
        $user->save();
        switch ($data['radio']) {
            case CustomField::ROUTE_ADDRESS :
                return $this->get_address($task->id);
            case CustomField::ROUTE_REMOTE :
                $task->remote = 1;
                $task->save();
                return $this->get_date($task->id);
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
    public function get_address(int $task_id): array
    {
        $result = $this->custom_field_service->getCustomFieldsByRoute($task_id, CustomField::ROUTE_ADDRESS);
        $task = $result['task'];
        $custom_fields = $result['custom_fields'];
        if ($task->category->parent->double_address) {
            return ['route' => 'address', 'address' => 2, 'steps' => self::Create_Address, 'custom_fields' => $custom_fields];
        }
        return ['route' => 'address', 'address' => 1, 'steps' => self::Create_Address, 'custom_fields' => $custom_fields];
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
        $user->active_step = self::Create_Address;
        $user->save();
        $task->update([
            'coordinates' => $data['points'][0]['latitude'] . ',' . $data['points'][0]['longitude']
        ]);
        return $this->get_date($task->id);

    }

    /**
     * Retrieve next step with additional fields
     *
     * @param $task // Task model object
     * @return array //Value Returned
     */
    #[ArrayShape(['route' => "string", 'task_id' => "", 'steps' => "int", 'custom_fields' => "array"])]
    public function get_date(int $task_id): array
    {
        return [
            'route' => 'date', 'task_id' => $task_id, 'steps' => self::Create_Date,
            'custom_fields' => $this->custom_field_service->getCustomFieldsByRoute($task_id, CustomField::ROUTE_DATE)['custom_fields']
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
        $user->active_step = self::Create_Date;
        $user->save();
        return $this->get_budget($task->id);
    }

    /**
     * Retrieve next step with additional fields
     *
     * @param $task // Task model object
     * @return array //Value Returned
     */
    #[ArrayShape([])]
    public function get_budget(int $task_id): array
    {
        $result = $this->custom_field_service->getCustomFieldsByRoute($task_id, CustomField::ROUTE_BUDGET);
        $task = $result['task'];
        $custom_fields = $result['custom_fields'];
        return [
            'route' => 'budget', 'task_id' => $task_id, 'steps' => self::Create_Budget, 'price' => Category::query()->findOrFail($task->category_id)->max,
            'custom_fields' => $custom_fields
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
        $user->active_step = self::Create_Budget;
        $user->save();
        return $this->get_note($task->id);
    }

    /**
     * Retrieve next step with additional fields
     *
     * @param $task // Task model object
     * @return array //Value Returned
     */
    #[ArrayShape([])]
    public function get_note(int $task_id): array
    {
        $custom_fields = $this->custom_field_service->getCustomFieldsByRoute($task_id, CustomField::ROUTE_NOTE)['custom_fields'];
        return ['route' => 'note', 'task_id' => $task_id, 'steps' => self::Create_Note, 'custom_fields' => $custom_fields];
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
        $user->active_step = self::Create_Note;
        $user->save();
        return $this->get_contact($task->id);
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
    public function get_contact(int $task_id): array
    {
        return [
            'route' => 'contact', 'task_id' => $task_id, 'steps' => self::Create_Contact,
            'custom_fields' => $this->custom_field_service->getCustomFieldsByRoute($task_id, CustomField::ROUTE_CONTACTS)['custom_fields']
        ];
    }

    /**
     * Save remote s values by request
     *
     * @param $data // Validated request data from mobile
     * @return array //Value Returned
     * @throws \Exception
     */
    #[ArrayShape([])]
    public function contact_store($data, $user_id = 0): array
    {
        $user = User::find($user_id);
        $task = Task::findOrFail($data['task_id']);
        unset($data['task_id']);
        $correctPhoneNumber = (!empty($user)) ? (new CustomService)->correctPhoneNumber($user->phone_number) : '';
        switch (true) {
            case (!$user->is_phone_number_verified && $user->phone_number !== $data['phone_number']):
                $data['is_phone_number_verified'] = 0;
                $data['phone_number'] = (new CustomService)->correctPhoneNumber($data['phone_number']);
                $user->update($data);
                VerificationService::send_verification('phone', $user, $correctPhoneNumber);
                return $this->get_verify($task->id, $user);
            case ($user->phone_number !== $data['phone_number']) :
                VerificationService::send_verification_for_task_phone($task, $data['phone_number']);
                return $this->get_verify($task->id, $user);
            case (!$user->is_phone_number_verified) :
                VerificationService::send_verification('phone', $user, $correctPhoneNumber);
                return $this->get_verify($task->id, $user);
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
    public function get_verify(int $task_id, $user): array
    {
        return ['route' => 'verify', 'task_id' => $task_id, 'user' => $user];
    }

    public function verification($data): JsonResponse
    {
        /** @var Task $task */
        $task = Task::findOrFail($data['task_id']);
        /** @var User $user */
        $user = auth()->user();

        switch (true) {
            case !$user->is_phone_number_verified && $data['sms_otp'] === $user->verify_code :
                if (strtotime($user->verify_expiration) >= strtotime(Carbon::now())) {
                    $user->update(['is_phone_number_verified' => 1, 'active_step' => null, 'active_task' => null]);
                    $task->update(['status' => 1, 'user_id' => $user->id, 'phone' => $user->phone_number]);
                    // send notification
                    NotificationService::sendTaskNotification($task, $user->id);

                    return $this->success([
                        'task_id' => $task->id,
                        'route' => 'end',
                    ], 'Successfully verified');
                }
                return $this->fail([
                    'sms_otp' => ['expired_message']
                ], 'Validation errors');

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
                }
                return $this->fail([
                    'sms_otp' => ['expired_message']
                ], 'Validation errors');

            default :
                return $this->fail([
                    'sms_otp' => ['incorrect_message']
                ], 'Validation errors');
        }
    }

    /**
     *
     * Function  attachCustomFieldsByRoute
     * @param int $task_id
     * @param string $routeName
     * @param $request
     * @return  Builder|Collection|Model|null
     */
    protected function attachCustomFieldsByRoute(int $task_id, string $routeName, $request)
    {
        $task = Task::with('category.custom_fields.custom_field_values')->find($task_id);
        $custom_fields = collect($task->category->custom_fields)->where('route', $routeName)->all();
        foreach ($custom_fields as $data) {
            $value = $task->custom_field_values()->where('custom_field_id', $data->id)->first() ?? new CustomFieldsValue();
            $value->task_id = $task_id;
            $value->custom_field_id = $data->id;
            $arr = $data->name !== null ? Arr::get($request, $data->name) : null;
            $value->value = is_array($arr) ? json_encode($arr) : $arr;
            $value->save();
        }
        return $task;
    }
}
