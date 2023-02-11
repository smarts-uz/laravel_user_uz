<?php

namespace App\Services\Task;

use App\Models\Address;
use App\Models\Category;
use App\Models\CustomField;
use App\Models\CustomFieldsValue;
use App\Models\Task;
use App\Models\User;
use App\Services\CustomService;
use App\Services\Response;
use App\Services\VerificationService;
use Carbon\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use JetBrains\PhpStorm\ArrayShape;

class UpdateTaskService
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

    public function updateName($task, $data): array
    {
        (new CustomService)->updateCache('task_update_' . $task->id, 'name', $data['name']);
        (new CustomService)->updateCache('task_update_' . $task->id, 'category_id', $data['category_id']);

        return $this->get_custom($task);
    }

    #[ArrayShape([])]
    public function get_custom($task): array
    {
        $result = $this->custom_field_service->getCustomFieldsByRoute($task->id, CustomField::ROUTE_CUSTOM);
        $custom_fields = $result['custom_fields'];
        if (!$task->category->customFieldsInCustom->count()) {
            if ($task->category->parent->remote) {
                return $this->get_remote($task);
            }
            return $this->get_address($task);
        }
        return ['route' => 'custom', 'task_id' => $task->id, 'steps' => 6, 'custom_fields' => $custom_fields];
    }

    public function updateCustom($task, $request): array
    {
        $customFields = [];
        foreach ($task->category->custom_fields()->where('route', CustomField::ROUTE_CUSTOM)->get() as $customField) {
            $value['custom_field_id'] = $customField->id;
            $requestValue = $customField->name !== null ? (Arr::get($request->all(), $customField->name) ?? [null]) : [];
            $value['value'] = is_array($requestValue) ? json_encode($requestValue) : $requestValue;
            $customFields[] = $value;
        }
        (new CustomService)->updateCache('task_update_' . $task->id, 'custom_fields', $customFields);

        if ($task->category->parent->remote) {
            return $this->get_remote($task);
        }
        return $this->get_address($task);
    }


    #[ArrayShape([])]
    public function get_remote($task): array
    {
        return [
            'route' => 'remote', 'task_id' => $task->id, 'steps' => 5,
            'custom_fields' => []
        ];
    }

    public function updateRemote($task, $data): array
    {
        return match ($data['radio']) {
            CustomField::ROUTE_ADDRESS => $this->get_address($task),
            CustomField::ROUTE_REMOTE => $this->get_date($task),
            default => ['success' => false, 'message' => 'Incorrect value']
        };
    }


    #[ArrayShape([])]
    public function get_address($task): array
    {
        if ($task->category->parent->double_address) {
            return ['route' => 'address', 'address' => 2, 'steps' => 4, 'custom_fields' => []];
        }
        return ['route' => 'address', 'address' => 1, 'steps' => 4, 'custom_fields' => []];
    }

    public function updateAddress($task, $data): array
    {
        $length = min(count($data['points']), setting('site.max_address'));
        $addresses = [];
        for ($i = 0; $i < $length; $i++) {
            $address = [
                'task_id' => $task->id,
                'location' => $data['points'][$i]['location'],
                'latitude' => $data['points'][$i]['latitude'],
                'longitude' => $data['points'][$i]['longitude']
            ];
            if ($i == 0) {
                $address['default'] = 1;
            }
            $addresses[] = $address;
        }

        (new CustomService)->updateCache('task_update_' . $task->id, 'addresses', $addresses);

        return $this->get_date($task);

    }


    #[ArrayShape([])]
    public function get_date($task): array
    {
        return [
            'route' => 'date', 'task_id' => $task->id, 'steps' => 3,
            'custom_fields' => $this->custom_field_service->getCustomFieldsByRoute($task->id, CustomField::ROUTE_DATE)['custom_fields']
        ];
    }

    public function updateDate($task, $data): array
    {
        unset($data['task_id']);
        (new CustomService)->updateCache('task_update_' . $task->id, 'date', $data);
        return $this->get_budget($task);
    }


    #[ArrayShape([])]
    public function get_budget($task): array
    {
        return [
            'route' => 'budget', 'task_id' => $task->id, 'steps' => 2, 'price' => Category::query()->findOrFail($task->category_id)->max,
            'custom_fields' => $this->custom_field_service->getCustomFieldsByRoute($task->id, CustomField::ROUTE_BUDGET)['custom_fields']
        ];
    }

    public function updateBudget($task, $data): array
    {
        (new CustomService)->updateCache('task_update_' . $task->id, 'budget', $data['amount']);
        (new CustomService)->updateCache('task_update_' . $task->id, 'oplata', $data['budget_type']);
        return $this->get_note($task);
    }


    #[ArrayShape([])]
    public function get_note($task): array
    {
        $result = $this->custom_field_service->getCustomFieldsByRoute($task->id, CustomField::ROUTE_NOTE);
        $custom_fields = $result['custom_fields'];

        return ['route' => 'note', 'task_id' => $task->id, 'steps' => 1, 'custom_fields' => $custom_fields];
    }

    public function updateNote($task, $data): array
    {
        unset($data['task_id']);
        (new CustomService)->updateCache('task_update_' . $task->id, 'note', $data);
        return $this->get_contact($task);
    }

    #[ArrayShape([])]
    public function get_contact($task): array
    {
        return [
            'route' => 'contact', 'task_id' => $task->id, 'steps' => 0,
            'phone' => $task->phone ? (new CustomService)->correctPhoneNumber($task->phone) : null,
            'custom_fields' => $this->custom_field_service->getCustomFieldsByRoute($task->id, CustomField::ROUTE_CONTACTS)['custom_fields']
        ];
    }

    public function updateContact($task_id, $data): array
    {
        /** @var User $user */
        $user = auth()->user();
        unset($data['task_id']);
        $task = TAsk::select('phone', 'verify_code', 'verify_expiration', 'status', 'user_id')->find($task_id);
        $correct = (new CustomService)->correctPhoneNumber($data['phone_number']);
        $correctUser = (new CustomService)->correctPhoneNumber($user->phone_number);
        switch (true) {
            case (!$user->is_phone_number_verified && $user->phone_number !== $correct):
                // in this case user phone number will be changed to new phone number
                $data['is_phone_number_verified'] = 0;
                $data['phone_number'] = $correct;
                $user->update($data);
                VerificationService::send_verification('phone', $user, $correctUser);
                return $this->get_verify($task_id, $user);
            case (!$user->is_phone_number_verified && $user->phone_number === $correct):
                VerificationService::send_verification('phone', $user, $correctUser);
                return $this->get_verify($task_id, $user);
            case ($user->phone_number !== $correctUser && $task->phone !== $correct):
                VerificationService::send_verification_for_task_phone($task, $correct);
                return $this->get_verify($task_id, $user);
            case ($user->is_phone_number_verified && $task->phone === $correct):
                // in this case task's phone number already verified in create task process, that's why it doesn't need verification
                $task->status = Task::STATUS_OPEN;
                $task->user_id = $user->id;
                $task->phone = $correct;
                $task->save();
                break;
            default:
                $task->status = Task::STATUS_OPEN;
                $task->user_id = $user->id;
                $task->phone = $correctUser;
                $task->save();
                break;
        }


        $this->updateTask($task, $user);

        return [
            'task_id' => $task->id,
            'route' => 'end',
        ];
    }


    // Update Image
    public function updateImage($task, $request): JsonResponse
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
        $imgData = $task->photos ? json_decode($task->photos) : [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $uploadedImage) {
                $fileName = time() . '_' . $uploadedImage->getClientOriginalName();
                $uploadedImage->move(public_path("storage/uploads/"), $fileName);
                $imgData[] = $fileName;
            }
        }
        $data['photos'] = $imgData;
        $task->update($data);
        $task->save();

        return response()->json([
            'success' => true,
            'data' => $task
        ]);
    }

    // Delete Image
    public function deleteImage($request, $task): JsonResponse
    {
        $image = $request->get('image');
        File::delete(public_path() . '/storage/uploads/' . $image);
        $images = json_decode($task->photos);
        $updatedImages = array_diff($images, [$image]);
        $task->photos = json_encode(array_values($updatedImages));
        $task->save();
        return response()->json([
            'success' => true,
            'message' => 'Successfully deleted'
        ]);
    }


    #[ArrayShape([])]
    public function get_verify($task_id, $user): array
    {
        return ['route' => 'verify', 'task_id' => $task_id, 'user' => $user];
    }

    public function verification($task_id, $data): JsonResponse
    {
        $correct = (new CustomService)->correctPhoneNumber($data['phone_number']);
        /** @var User $user */
        $user = User::where('phone_number', $correct)->first();
        if (!$user) {
            /** @var Task $task */
            $task = Task::where('phone', $correct)->first();
        }
        if ($data['sms_otp'] === $user->verify_code) {
            if (strtotime($user->verify_expiration) >= strtotime(Carbon::now())) {
                $user->update(['is_phone_number_verified' => 1]);

                $this->updateTask($task, $user);

                return $this->success([
                    'task_id' => $task_id,
                    'route' => 'end',
                ], __('Ваш телефон успешно подтвержден'));
            } else {
                return $this->fail([
                    'sms_otp' => ['expired_message']
                ],  __('Срок действия номера истек'));
            }
        }
        else {
            return $this->fail([
                'sms_otp' => ['incorrect_message']
            ],  __('Неправильный код!'));
        }
    }



    private function updateTask($task, $user): void
    {
        $cacheValues = cache()->get('task_update_' . $task->id);
        if (array_key_exists('custom_fields', $cacheValues)) {
            // Save task custom fields
            $task->custom_field_values()->delete();
            foreach ($cacheValues['custom_fields'] as $customField) {
                $customField['task_id'] = $task->id;
                CustomFieldsValue::create($customField);
            }
        }
        $addressesCount = 0;
        if (array_key_exists('addresses', $cacheValues)) {
            // Save task addresses
            $addressesCount = count($cacheValues['addresses']);
            $task->addresses()->delete();
            foreach ($cacheValues['addresses'] as $address) {
                Address::create($address);
            }
        }

        $task->update([
            'name' => $cacheValues['name'],
            'category_id' => $cacheValues['category_id'],
            'start_date' => $cacheValues['date']['start_date'] ?? null,
            'end_date' => $cacheValues['date']['end_date'] ?? null,
            'date_type' => $cacheValues['date']['date_type'],
            'budget' => $cacheValues['budget'],
            'oplata' => $cacheValues['oplata'],
            'description' => $cacheValues['note']['description'],
            'docs' => $cacheValues['note']['docs'],
            'status' => Task::STATUS_OPEN,
            'user_id' => $user->id,
            'phone' => $user->phone_number,
            'coordinates' => $addressesCount > 0 ? $cacheValues['addresses'][0]['latitude'] . ',' . $cacheValues['addresses'][0]['longitude'] : ''
        ]);

        cache()->forget('task_update_' . $task->id);
    }

    public function getAddress($data)
    {
        $array = (new CreateService())->addAdditionalAddress(request());
        $data['address'] = $array['address'];
        $data['address_add'] = $array['address_add'];
        $data['coordinates'] = $data['coordinates0'];
        unset($data['coordinates0'], $data['location0']);
        return $data;
    }

    public function taskGuardApi($task): void
    {
        if ((int)$task->user_id !== auth()->id() && (int)$task->performer_id !== auth()->id()) {
            throw new HttpResponseException(response()->json([
                'success' => false, 'message' => "No Permission"
            ], 403));
        }
    }

    public function taskGuard($task): void
    {
        if ((int)$task->user_id !== (int)auth()->id() && (int)$task->performer_id !== (int)auth()->id()) {
            abort(403, "No Permission");
        }
    }
}

