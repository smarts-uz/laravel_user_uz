<?php


namespace App\Services;

use JsonException;
use App\Http\Resources\{NotificationResource, PerformerIndexResource, ReviewIndexResource};
use App\Item\{PerformerPrefItem, PerformerServiceItem, PerformerUserItem};
use App\Models\{BlockedUser, Notification, Review, Task, User, UserCategory, UserView, Category};
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;
use League\Flysystem\WhitespacePathNormalizer;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;


class PerformersService
{

    /**
     *
     * Function  service
     * Bu funksiya performerlar ro'yxatini chiqarib beradi
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
        if ((int)setting('admin.PerformerSelfVisible',1) !== 1) {
            $item->users = User::query()
                ->where('role_id', User::ROLE_PERFORMER)
                ->where('name', 'LIKE', "%{$search}%")
                ->WhereNot('id', $authId)
                ->orderByDesc('review_rating')
                ->orderbyRaw('(review_good - review_bad) DESC')->paginate(50);
        } else {
            $item->users = User::query()
                ->where('role_id', User::ROLE_PERFORMER)
                ->where('name', 'LIKE', "%{$search}%")
                ->orderByDesc('review_rating')
                ->orderbyRaw('(review_good - review_bad) DESC')->paginate(50);
        }

        $item->top_users = User::query()
            ->where('review_rating', '!=', 0)
            ->where('role_id', User::ROLE_PERFORMER)->orderbyRaw('(review_good - review_bad) DESC')
            ->limit(Review::TOP_USER)->pluck('id')->toArray();
        return $item;
    }


    /**
     *
     * Function  performer
     * id bo'yicha bitta performer ma'lumotlarini qaytaradi
     * @link https://user.uz/performers/354
     * @param $user
     * @return  PerformerUserItem
     */
    public function performer($user): PerformerUserItem
    {
        $item = new PerformerUserItem();
        $item->top_users = User::query()
            ->where('review_rating', '!=', 0)
            ->where('role_id', User::ROLE_PERFORMER)->orderbyRaw('(review_good - review_bad) DESC')
            ->limit(Review::TOP_USER)->pluck('id')->toArray();
        $item->portfolios = $user->portfolios()->where('image', '!=', null)->get();
        $item->goodReviews = $user->goodReviews()->whereHas('task')->whereHas('user')->latest()->get();
        $item->badReviews = $user->badReviews()->whereHas('task')->whereHas('user')->latest()->get();
        $value = Carbon::parse($user->created_at)->locale((new CustomService)->getLocale());
        $day = $value == now()->toDateTimeString() ? "Bugun" : "$value->day-$value->monthName";
        $item->created = "$day  {$value->year}";
        $performer_category = UserCategory::query()->where('user_id', $user->id)->get()->groupBy(static function ($data){
            return $data->category->parent->id;
        });
        $item->user_category = [];
        foreach ($performer_category as $category_id => $category) {
            $item->user_category[] = [
                'parent' => Category::query()->where('id',$category_id)->get(),
                'category' => $category
            ];
        }
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
        if ((int)setting('admin.PerformerSelfVisible',1) !== 1) {
            $item->users = User::query()
                ->where('role_id', User::ROLE_PERFORMER)
                ->where('name', 'LIKE', "%{$search}%")
                ->WhereNot('id', $authId)
                ->whereIn('id', $item->user_categories)
                ->orderByDesc('review_rating')
                ->orderbyRaw('(review_good - review_bad) DESC')->paginate(50);
        } else {
            $item->users = User::query()
                ->where('role_id', User::ROLE_PERFORMER)
                ->where('name', 'LIKE', "%{$search}%")
                ->whereIn('id', $item->user_categories)
                ->orderByDesc('review_rating')
                ->orderbyRaw('(review_good - review_bad) DESC')->paginate(50);
        }
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
        if ((int)setting('admin.PerformerSelfVisible',1) !== 1) {
            $performers = User::query()
                ->where('role_id', User::ROLE_PERFORMER)
                ->WhereNot('id', $authId);
        }else{
            $performers = User::query()->where('role_id', User::ROLE_PERFORMER);
        }

        if (isset($data['categories'])) {
            $categories = is_array($data['categories']) ? $data['categories'] : json_decode($data['categories']);
            $childCategories = Category::query()->whereIn('parent_id', $categories)->pluck('id')->toArray();
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

        if (isset($data['review'], $data['asc'])) {
            $performers = $performers
                ->orderByDesc('review_rating')
                ->orderByRaw('(review_good - review_bad) DESC');
        }

        if (isset($data['review'], $data['desc'])) {
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
     * biror userga task give qilish (web)
     * @param $task_id
     * @param $user_id
     * @param $session
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws JsonException
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

            NotificationService::sendNotificationRequest($users_id, $notification);
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

    /**
     * biror userga task give qilish (app)
     * @param $task_id
     * @param $performer_id
     * @return JsonResponse
     * @throws JsonException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
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

        NotificationService::sendNotificationRequest($performer_id, $notification);

        NotificationService::pushNotification($performer, [
            'title' => NotificationService::titles($notification->type, $locale),
            'body' => NotificationService::descriptions($notification, $locale)
        ], 'notification', new NotificationResource($notification));

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'data'=> $performer

        ]);
    }


    /**
     * user profilining ko'rishlar soni
     * @param $user
     * @return UserView|bool
     */
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
     * categoriya bo'yicha performerlar rasmlarini qaytaradi
     * @param $categoryId
     * @param $authId
     * @return array
     */
    public function performers_image($categoryId, $authId): array
    {
        $user_cat = UserCategory::query()->where('category_id', $categoryId)->pluck('user_id')->toArray();
        $user_image = User::query()->whereIn('id', $user_cat)->WhereNot('id', $authId)->take(3)->get();
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
     * performer bo'lishdagi email va telefon raqamni saqlaydi
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
        return response()->json([
            'success' => 'true',
            'message' => __('Успешно обновлено'),
            'data'=> $data
        ]);
    }

    /**
     * performer bo'lishdagi userning tug'ilgan kunini saqlaydi
     * @param $user
     * @param $data
     * @return JsonResponse
     */
    public function becomePerformerData($user, $data): JsonResponse
    {
        $data['born_date'] = Carbon::parse($data['born_date'])->format('Y-m-d');
        $user->update($data);

        return response()->json([
            'success' => 'true',
            'message' => __('Успешно обновлено'),
            'data' => $data
        ]);
    }

}
