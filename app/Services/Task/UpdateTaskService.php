<?php

namespace App\Services\Task;

use App\Http\Resources\PerformerResponseResource;
use App\Http\Resources\TaskAddressResource;
use App\Http\Resources\UserInTaskResource;
use App\Models\Address;
use App\Models\Category;
use App\Models\ChMessage;
use App\Models\CustomField;
use App\Models\CustomFieldsValue;
use App\Models\Task;
use App\Models\TaskResponse;
use App\Models\User;
use App\Services\CustomService;
use App\Services\Response;
use App\Services\VerificationService;
use Carbon\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use JetBrains\PhpStorm\ArrayShape;
use RealRashid\SweetAlert\Facades\Alert;

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
        $task = TAsk::select('id','phone', 'verify_code', 'verify_expiration', 'status', 'user_id')->find($task_id);
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
        if (is_array($cacheValues) && array_key_exists('custom_fields', $cacheValues)) {
            // Save task custom fields
            $task->custom_field_values()->delete();
            foreach ($cacheValues['custom_fields'] as $customField) {
                $customField['task_id'] = $task->id;
                CustomFieldsValue::create($customField);
            }
        }
        $addressesCount = 0;
        if (is_array($cacheValues) && array_key_exists('addresses', $cacheValues)) {
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
        $array = $this->service->addAdditionalAddress(request());
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

    public function __invoke(int $task_id, $data)
    {
        $task = Task::select('user_id', 'performer_id')->find($task_id);
        $this->taskGuard($task);
        $data = $this->getAddress($data);
        $task->update($data);
        $this->service->syncCustomFields($task_id);
        Alert::success('Success');
        return response()->json(['message' => 'Success']);
    }

    public function completed($task_api, $api = false)
    {
        $task = Task::with('category')->find($task_api);
        $this->taskGuardApi($task);
        $data = [
            'status' => Task::STATUS_COMPLETE
        ];
        ChMessage::where('from_id', $task->user_id)->where('to_id', $task->performer_id)
        ->orWhere('from_id', $task->performer_id)->delete();

        $task->update($data);

        if($api) {
            $photos = array_map(function ($val) {
                return asset('storage/uploads/' . $val);
            },
                json_decode(!empty($task->photos)) ?? []
            );
            $user_response = TaskResponse::where('task_id', $task->id)
                ->where('performer_id', \auth()->guard('api')->id())
                ->first();
            $performer_response = TaskResponse::where('task_id', $task->id)
                ->where('performer_id', $task->performer_id)
                ->first();
            $response = ['data' => [
                'id' => $task->id,
                'name' => $task->name,
                'address' => TaskAddressResource::collection($task->addresses),
                'date_type' => $task->date_type,
                'start_date' => $task->start_date,
                'end_date' => $task->end_date,
                'budget' => $task->budget,
                'description' => $task->description,
                'phone' => $task->phone,
                'performer_id' => $task->performer_id,
                'performer' => new PerformerResponseResource($performer_response),
                'other'=> $task->category->name === "Что-то другое" || $task->category->name === "Boshqa narsa",
                'parent_category_name'=>$task->category->parent->getTranslatedAttribute('name', app()->getLocale(), 'ru'),
                'category_name' => $task->category->getTranslatedAttribute('name', app()->getLocale(), 'ru'),
                'category_id' => $task->category_id,
                'current_user_response' => (bool)$user_response,
                'responses_count' => $task->responses()->count(),
                'user' => new UserInTaskResource($task->user),
                'views' => $task->views,
                'status' => $task->status,
                'oplata' => $task->oplata,
                'docs' => $task->docs,
                'created_at' => $task->created,
                'custom_fields' => $this->custom_field_service->getCustomFieldsByRoute($task->id, 'custom')['custom_fields'],
                'photos' => $photos,
                'performer_review' => $task->performer_review,
                'response_price' => setting('admin.pullik_otklik'),
                'free_response' => setting('admin.bepul_otklik')
            ]];
            return response()->json([
                'success' => true,
                'message' => __('Успешно сохранено'),
                'task' => new $response
            ]);
        }

        Alert::success(__('Успешно сохранено'));
        return back();
    }

    public function not_completed($task_id, $data, $api = false): JsonResponse|RedirectResponse
    {
        $task = Task::select('user_id', 'performer_id')->find($task_id);
        $this->taskGuardApi($task);

        ChMessage::where('from_id', $task->user_id)->where('to_id', $task->performer_id)
            ->orWhere('from_id', $task->performer_id)->delete();

        $task->update(['status' => Task::STATUS_NOT_COMPLETED, 'not_completed_reason' => $data]);
        if($api){
            return response()->json([
                'success' => true,
                'message' => __('Успешно сохранено')
            ]);
        }
        Alert::success(__('Успешно сохранено'));
        return back();
    }

    public function change($task_id, $request)
    {
        $task = Task::find($task_id);
        $this->taskGuard($task);
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
        if ($request['docs'] === "on") {
            $data['docs'] = 1;
        } else {
            $data['docs'] = 0;
        }
        $task->update($data);
        $this->service->syncCustomFields($task->id);
        Alert::success(__('Изменения сохранены'));
    }

    public function deleteImage2($task_id, $image): void
    {
        $task = Task::select('photos', 'performer_id', 'user_id')->find($task_id);
        $this->taskGuard($task);
        File::delete(public_path() . '/storage/uploads/' . $image);
        $images = json_decode($task->photos);
        $updatedImages = array_diff($images, [$image]);
        $task->photos = json_encode(array_values($updatedImages));
        $task->save();
    }

}
