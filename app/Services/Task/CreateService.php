<?php

namespace App\Services\Task;

use App\Http\Resources\NotificationResource;
use App\Item\CreateNameItem;
use App\Models\Address;
use App\Models\Category;
use App\Models\CustomFieldsValue;
use App\Models\Notification;
use App\Models\Task;
use App\Models\User;
use App\Services\CustomService;
use App\Services\NotificationService;
use App\Services\SmsMobileService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class CreateService
{

    /**
     *
     * Function  name
     * @param $category_id
     * @param string|null $lang
     * @return  CreateNameItem
     */
    public function name($category_id, ?string $lang = 'uz'): CreateNameItem
    {
        $category = Cache::remember('category_' . $lang, now()->addMinute(180), function () use($lang) {
            return Category::withTranslations($lang)->orderBy("order")->get();
        });

        $item = new CreateNameItem();
        $item->current_category = Category::query()->findOrFail($category_id);
        $item->categories = collect($category)->where('parent_id', null)->all();
        $item->child_categories = collect($category)->where('parent_id', '!=', null)->all();
        return $item;
    }
    /**
     *
     * Function  syncCustomFields
     * Mazkur metod Task obyektiga unga tegishli bo'lgan custom fieldslarni o'chirib beradi
     * @param Task $task Task Object
     *
     */
    public function syncCustomFields(Task $task): void
    {
        $task->custom_field_values()->delete();
        $this->attachCustomFields($task);

    }


    /**
     *
     * Function  attachCustomFields
     * Mazkur metod Task obyektiga, unga tegishli bo'lgan custom fieldslarni qo'shib beradi
     * @param Task $task Task Object
     *
     */

    public function attachCustomFields(Task $task): void
    {
        foreach ($task->category->custom_fields as $data) {
            $value = new CustomFieldsValue();
            $value->task_id = $task->id;
            $value->custom_field_id = $data->id;
            $arr = $data->name !== null ? (Arr::get(request()->all(), str_replace(' ', '_', $data->name)) ?? [null] ): null;
            $value->value = is_array($arr) ? json_encode($arr) : $arr;
            $value->save();
        }
    }
    /**
     *
     * Function  delete
     * Mazkur metod Taskni o'chirib tashlaydi
     * @param  $task
     *
     */
    public function delete($task): void
    {
        $task->status = Task::STATUS_CANCELLED;
        $task->save();
        /** @var Notification $notification */
        $notification = Notification::query()->create([
            'user_id' => $task->user_id,
            'description' => $task->desciption ?? 'task description',
            'task_id' => $task->id,
            "cat_id" => $task->category_id,
            "name_task" => $task->name,
            "type" => Notification::CANCELLED_TASK
        ]);

        NotificationService::sendNotificationRequest([$task->user_id], [
            'created_date' => $notification->created_at->format('d M'),
            'title' => NotificationService::titles($notification->type),
            'url' => route('show_notification', [$notification]),
            'description' => NotificationService::descriptions($notification)
        ]);

        $locale = (new CustomService)->cacheLang($task->user_id);
        NotificationService::pushNotification($task->user, [
            'title' => __('3адание отменено', [], $locale),
            'body' => __('Ваше задание task_name №task_id было отменено', [
                'task_name' => $task->name, 'task_id' => $task->id,
            ], $locale)
        ], 'notification', new NotificationResource($notification));
    }

    /**
     *
     * Function  storeName
     * @param string $name
     * @param $category_id
     * @return  int
     */
    public function storeName(string $name, $category_id): int
    {
        $data = ['name' => $name, 'category_id' => $category_id];
        $task = Task::query()->create($data);
        return (int)$task->id;
    }

    /**
     *
     * Function  attachCustomFieldsByRoute
     * @param int $task_id
     * @param string $routeName
     * @param $request
     * @return  Builder|Builder|Collection|Model|null
     */
    public function attachCustomFieldsByRoute(int $task_id, string $routeName, $request)
    {
        $task = Task::with('category.custom_fields')->find($task_id);
        foreach ($task->category->custom_fields()->where('route', $routeName)->get() as $data) {
            $value = $task->custom_field_values()->where('custom_field_id', $data->id)->first() ?? new CustomFieldsValue();
            $value->task_id = $task->id;
            $value->custom_field_id = $data->id;
            $arr = $data->name !== null ? (Arr::get($request, str_replace(' ', '_', $data->name)) ?? [null]): [];
            $value->value = is_array($arr) ? json_encode($arr) : $arr;
            $value->save();
        }
        return $task;
    }

    /**
     *
     * Function  addAdditionalAddress
     * @param int $task_id
     * @param $requestAll
     * @return  mixed
     */
    public function addAdditionalAddress(int $task_id, $requestAll): mixed
    {
        $data_inner = [];
        $dataMain = Arr::get($requestAll, 'coordinates0', '');

        for ($i = 0; $i < setting('site.max_address') ?? 10; $i++) {

            $location = Arr::get($requestAll, 'location' . $i, '');
            $coordinates = Arr::get($requestAll, 'coordinates' . $i, '');

            if ($coordinates) {
                if ($i === 0) {
                    $data_inner['default'] = 1;
                }
                $data_inner['location'] = $location;
                $data_inner['longitude'] = explode(',', $coordinates)[1];
                $data_inner['latitude'] = explode(',', $coordinates)[0];
                $data_inner['task_id'] = $task_id;
                Address::query()->create($data_inner);
            }
        }
        return $dataMain;
    }

    /**
     *
     * Function  perform_notification
     * Mazkur metod Task yaratganda notification va sms yuborish
     * @param $task $user
     * @param $user
     * @param $performer_id
     */
    public function perform_notification($task, $user, $performer_id): void
    {
        /** @var User $performer */
        $performer = User::query()->findOrFail($performer_id);
        $locale = (new CustomService)->cacheLang($performer_id);
        $text_url = route("searchTask.task", $task->id);
        $message = __('Вам предложили новое задание task_name №task_id от заказчика task_user', [
            'task_name' => $text_url, 'task_id' => $task->id, 'task_user' => $user->name
        ], $locale);
        $phone_number = (new CustomService)->correctPhoneNumber($performer->phone_number);
        SmsMobileService::sms_packages($phone_number, $message);

        /** @var Notification $notification */
        $notification = Notification::query()->create([
            'user_id' => $task->user_id,
            'performer_id' => $performer_id,
            'task_id' => $task->id,
            'name_task' => $task->name,
            'description' => '123',
            'type' => Notification::GIVE_TASK,
        ]);

        NotificationService::sendNotificationRequest([$performer_id], [
            'created_date' => $notification->created_at->format('d M'),
            'title' => NotificationService::titles($notification->type),
            'url' => route('show_notification', [$notification]),
            'description' => NotificationService::descriptions($notification)
        ]);

        NotificationService::pushNotification($performer, [
            'title' => __('Предложение', [], $locale), 'body' => __('Вам предложили новое задание task_name №task_id от заказчика task_user', [
                'task_name' => $notification->name_task, 'task_id' => $notification->task_id, 'task_user' => $notification->user?->name
            ], $locale)
        ], 'notification', new NotificationResource($notification));

        session()->forget('performer_id_for_task');

    }

}
