<?php


namespace App\Services;

use App\Http\Resources\NotificationResource;
use App\Http\Resources\PerformerIndexResource;
use App\Http\Resources\ReviewIndexResource;
use App\Item\PerformerPrefItem;
use App\Item\PerformerServiceItem;
use App\Item\PerformerUserItem;
use App\Models\Notification;
use App\Models\Review;
use App\Models\Task;
use App\Models\User;
use App\Models\UserCategory;
use App\Models\UserView;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;
use TCG\Voyager\Models\Category;

class PerformersService
{

    /**
     *
     * Function  service
     * @link https://user.uz/performers
     * @param $authId
     * @param $search
     * @param string|null $lang
     * @return  PerformerServiceItem
     */
    public function service($authId, $search, ?string $lang = 'uz'): PerformerServiceItem
    {
        $category = Cache::remember('category_' . $lang, now()->addMinute(180), function () use ($lang) {
            return Category::withTranslations($lang)->orderBy("order")->get();
        });

        $item = new PerformerServiceItem();
        $item->tasks = Task::query()->where('user_id', $authId)
            ->whereIn('status', [Task::STATUS_OPEN, Task::STATUS_RESPONSE])->orderBy('created_at', 'DESC')
            ->get();
        $item->categories = collect($category)->where('parent_id', null)->all();
        $item->categories2 = collect($category)->where('parent_id', '!=', null)->all();
        $item->users = User::query()
            ->where('role_id', User::ROLE_PERFORMER)
            ->where('name', 'LIKE', "%{$search}%")
            ->WhereNot('id', $authId)
            ->orderByDesc('review_rating')
            ->orderbyRaw('(review_good - review_bad) DESC')->paginate(50);

        $item->top_users = User::query()
            ->where('review_rating', '!=', 0)
            ->where('role_id', User::ROLE_PERFORMER)->orderbyRaw('(review_good - review_bad) DESC')
            ->limit(Review::TOP_USER)->pluck('id')->toArray();
        return $item;
    }


    /**
     *
     * Function  performer
     * @link https://user.uz/performers/354
     * @param $user
     * @param $authId
     * @return  PerformerUserItem
     */
    public function performer($user, $authId): PerformerUserItem
    {
        $item = new PerformerUserItem();
        $item->top_users = User::query()
            ->where('review_rating', '!=', 0)
            ->where('role_id', User::ROLE_PERFORMER)->orderbyRaw('(review_good - review_bad) DESC')
            ->limit(Review::TOP_USER)->pluck('id')->toArray();
        $item->portfolios = $user->portfolios()->where('image', '!=', null)->get();
        $item->review_good = $user->review_good;
        $item->review_bad = $user->review_bad;
        $item->review_rating = $user->review_rating;
        $item->goodReviews = $user->goodReviews()->whereHas('task')->whereHas('user')->latest()->get();
        $item->badReviews = $user->badReviews()->whereHas('task')->whereHas('user')->latest()->get();
        $item->task_count = Task::query()->where('user_id', $authId)
            ->whereIn('status', [Task::STATUS_OPEN, Task::STATUS_RESPONSE, Task::STATUS_IN_PROGRESS, Task::STATUS_COMPLETE, Task::STATUS_NOT_COMPLETED, Task::STATUS_CANCELLED])->get();
        $user_categories = UserCategory::query()->where('user_id', $user->id)->pluck('category_id')->toArray();
        $item->user_category = Category::query()->whereIn('id', $user_categories)->get();
        $value = Carbon::parse($user->created_at)->locale((new CustomService)->getLocale());
        $day = $value == now()->toDateTimeString() ? "Bugun" : "$value->day-$value->monthName";
        $item->created = "$day  {$value->year}";
        return $item;
    }

    /**
     *
     * Function  perf_ajax
     * Mazkur metod categoriya bo'yicha performerlarni chiqarib beradi
     * @param $authId
     * @param $search
     * @param $cf_id
     * @return PerformerPrefItem
     */
    public function perf_ajax($authId, $search, $cf_id): PerformerPrefItem
    {
        $item = new PerformerPrefItem();
        $item->categories = Category::query()->where('parent_id', null)
            ->select('id', 'name', 'slug')->orderBy("order", "asc")->get();
        $item->categories2 = Category::query()->where('parent_id', '<>', null)
            ->select('id', 'parent_id', 'name')->orderBy("order", "asc")->get();
        $item->user_categories = UserCategory::query()->where('category_id', $cf_id)->pluck('user_id')->toArray();
        $item->users = User::query()
            ->where('role_id', User::ROLE_PERFORMER)
            ->where('name', 'LIKE', "%{$search}%")
            ->WhereNot('id', $authId)
            ->whereIn('id', $item->user_categories)
            ->orderByDesc('review_rating')
            ->orderbyRaw('(review_good - review_bad) DESC')->paginate(50);
        $item->top_users = User::query()
            ->where('review_rating', '!=', 0)
            ->where('role_id', User::ROLE_PERFORMER)->orderbyRaw('(review_good - review_bad) DESC')
            ->limit(Review::TOP_USER)->pluck('id')->toArray();
        $item->tasks = Task::query()->where('user_id', $authId)
            ->whereIn('status', [Task::STATUS_OPEN, Task::STATUS_RESPONSE])->orderBy('created_at', 'DESC')->get();
        return $item;
    }

    /**
     *
     * Function  perf_ajax
     * Mazkur metod performerlar bo'yicha filter qiladi
     * @param $data
     * @param $authId
     * @return AnonymousResourceCollection
     */
    public function performer_filter($data, $authId): AnonymousResourceCollection
    {
        $performers = User::query()
            ->where('role_id', User::ROLE_PERFORMER)
            ->WhereNot('id', $authId);

        if (isset($data['categories'])) {
            $categories = is_array($data['categories']) ? $data['categories'] : json_decode($data['categories']);
            $childCategories = \App\Models\Category::query()->whereIn('parent_id', $categories)->pluck('id')->toArray();
            $allCategories = array_unique(array_merge($categories, $childCategories));
            $user_categories = UserCategory::query()->whereIn('category_id', $allCategories)->pluck('user_id')->toArray();
            $performers = $performers->whereIn('id', $user_categories);
        }
        if (isset($data['child_categories'])) {
            $categories = is_array($data['child_categories']) ? $data['child_categories'] : json_decode($data['child_categories']);
            $user_categories = UserCategory::query()->whereIn('category_id', $categories)->pluck('user_id')->toArray();
            $performers = $performers->whereIn('id', $user_categories);
        }

        if (isset($data['online'])) {
            $date = Carbon::now()->subMinutes(2)->toDateTimeString();
            $performers = $performers->where('last_seen', ">=", $date);
        }

        if (isset($data['review'], $data['desc'])) {
            $performers = $performers
                ->orderByDesc('review_rating')
                ->orderByRaw('(review_good - review_bad) DESC');
        }

        if (isset($data['review'], $data['asc'])) {
            $performers = $performers
                ->orderBy('review_rating')
                ->orderByRaw('(review_good - review_bad) DESC');
        }

        if (isset($data['alphabet'], $data['desc'])) {
            $performers = $performers->orderBy('name', 'desc');
        }

        if (isset($data['alphabet'], $data['asc'])) {
            $performers = $performers->orderBy('name');
        }

        if (isset($data['search'])) {
            $search = $data['search'];
            $performers = $performers->where('name', 'like', "%$search%");
        }

        return PerformerIndexResource::collection($performers->paginate(20));
    }

    /**
     * @param $online
     * @param $per_page
     * @return AnonymousResourceCollection
     */
    public function performers($online, $per_page): AnonymousResourceCollection
    {
        $performers = User::query()
            ->where('role_id', User::ROLE_PERFORMER)
            ->orderByDesc('review_rating')
            ->orderByRaw('(review_good - review_bad) DESC');
        if (isset($online)) {
            $date = Carbon::now()->subMinutes(2)->toDateTimeString();
            $performers = $performers->where('role_id', User::ROLE_PERFORMER)->where('last_seen', ">=", $date);
        }
        return PerformerIndexResource::collection($performers->paginate($per_page));
    }

    /**
     * @param $task_id
     * @param $user_id
     * @param $session
     * @return JsonResponse
     */
    public function task_give_web($task_id, $user_id, $session): JsonResponse
    {
        if ($user_id !== null) {
            $session->put('given_id', $user_id);
        }

        if (isset($task_id)) {
            /** @var Task $task_name */
            $task_name = Task::query()->where('id', $task_id)->first();
            $users_id = $session->pull('given_id');
            /** @var User $performer */
            $performer = User::query()->find($users_id);
            $text_url = route("searchTask.task", $task_id);
            $message = __('Вам предложили новое задание task_name №task_id от заказчика task_user', [
                'task_name' => $text_url, 'task_id' => $task_id, 'task_user' => $task_name->user?->name
            ]);
            $phone_number = (new CustomService)->correctPhoneNumber($performer->phone_number);
            SmsMobileService::sms_packages($phone_number, $message);
            /** @var Notification $notification */
            $notification = Notification::query()->create([
                'user_id' => $task_name->user_id,
                'performer_id' => $users_id,
                'task_id' => $task_id,
                'name_task' => $task_name->name,
                'description' => '123',
                'type' => Notification::GIVE_TASK,
            ]);

            NotificationService::sendNotificationRequest([$users_id], [
                'created_date' => $notification->created_at->format('d M'),
                'title' => NotificationService::titles($notification->type),
                'url' => route('show_notification', [$notification]),
                'description' => NotificationService::descriptions($notification)
            ]);
            $locale = (new CustomService)->cacheLang($performer->id);
            NotificationService::pushNotification($performer, [
                'title' => __('Предложение', [], $locale), 'body' => __('Вам предложили новое задание task_name №task_id от заказчика task_user', [
                    'task_name' => $notification->name_task, 'task_id' => $notification->task_id, 'task_user' => $notification->user?->name
                ], $locale)
            ], 'notification', new NotificationResource($notification));

            return response()->json(['success' => true]);
        }
        return response()->json(['success' => true]);
    }

    public function task_give_app($task_id, $performer_id): JsonResponse
    {
        /** @var Task $task */
        $task = Task::query()->where('id', $task_id)->first();
        /** @var User $performer */
        $performer = User::query()->findOrFail($performer_id);
        $locale = (new CustomService)->cacheLang($performer->id);
        $text_url = route("searchTask.task", $task_id);
        $message = __('Вам предложили новое задание task_name №task_id от заказчика task_user', [
            'task_name' => $text_url, 'task_id' => $task->id, 'task_user' => $task->user?->name
        ], $locale);
        $phone_number = (new CustomService)->correctPhoneNumber($performer->phone_number);
        SmsMobileService::sms_packages($phone_number, $message);
        /** @var Notification $notification */
        $notification = Notification::query()->create([
            'user_id' => $task->user_id,
            'performer_id' => $performer_id,
            'task_id' => $task_id,
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
            'title' => NotificationService::titles($notification->type, $locale),
            'body' => NotificationService::descriptions($notification, $locale)
        ], 'notification', new NotificationResource($notification));

        return response()->json(['success' => true]);
    }


    public function setView($user): UserView|bool
    {
        if (auth()->check()) {
            $user->performer_views()->where('user_id', auth()->user()->id)->first();

            if (!$user->performer_views()->where('user_id', auth()->user()->id)->first()) {
                $view = new UserView();
                $view->user_id = auth()->user()->id;
                $view->performer_id = $user->id;
                $view->save();
                return $view;
            }
        }
        return false;
    }

    /**
     * @param $category_id
     * @return array
     */
    public function performers_image($category_id): array
    {
        $user_cat = UserCategory::query()->where('category_id', $category_id)->pluck('user_id')->toArray();
        $user_image = User::query()->whereIn('id', $user_cat)->take(3)->get();
        $images = [];
        foreach ($user_image as $image) {
            $images[] = asset('storage/' . $image->avatar);
        }
        switch (count($user_image)) {
            case(0):
                $images[0] = asset('images/Rectangle2.png');
                $images[1] = asset('images/Ellipse1.png');
                $images[2] = asset('images/performer4.jpg');
                break;
            case(1):
                $images[1] = asset('images/performer1.jpg');
                $images[2] = asset('images/performer2.jpg');
                break;
            case(2):
                $images[2] = asset('images/Rectangle4.png');
                break;
            default:
        }
        return $images;
    }

    /**
     * @param $from
     * @param $type
     * @param $authId
     * @return mixed
     */
    public function reviews($from, $type, $authId): mixed
    {
        $reviews = Review::query()
            ->whereHas('task')->whereHas('user')
            ->where('user_id', $authId)
            ->fromUserType($from)
            ->type($type)
            ->get();

        return ReviewIndexResource::collection($reviews);
    }

    /**
     * @param $user
     * @param $data
     * @return JsonResponse
     */
    public function becomePerformerEmailPhone($user, $data): JsonResponse
    {
        if ($data['phone_number'] !== $user->phone_number) {
            $user->phone_number = $data['phone_number'];
            $user->is_phone_number_verified = 0;
        }
        if ($data['email'] !== $user->email) {
            $user->email = $data['email'];
            $user->is_email_verified = 0;
        }
        $user->save();
        return response()->json(['success' => 'true', 'message' => __('Успешно обновлено')]);
    }

    /**
     * @param $user
     * @param $data
     * @return JsonResponse
     */
    public function becomePerformerData($user, $data): JsonResponse
    {
        $data['born_date'] = Carbon::parse($data['born_date'])->format('Y-m-d');
        $user->update($data);

        return response()->json(['success' => 'true', 'message' => __('Успешно обновлено')]);
    }

}
