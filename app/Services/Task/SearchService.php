<?php

namespace App\Services\Task;

use App\Item\SearchServiceTaskItem;
use App\Models\Address;
use App\Models\ComplianceType;
use App\Models\FavoriteTask;
use App\Models\Task;
use App\Models\Category;
use App\Models\TaskElastic;
use App\Models\User;
use App\Models\Compliance;
use App\Models\Review;
use App\Services\CustomService;
use App\Services\TelegramService;
use Elastic\ScoutDriverPlus\Support\Query;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use JetBrains\PhpStorm\ArrayShape;

class SearchService
{
    public const MAX_SEARCH_TASK = 20;
    public const REMOTE_TASK = 1;

    public function search_new(?string $lang = 'uz'): array
    {
        $categories = Cache::remember('category_' . $lang, now()->addMinute(180), function () use ($lang) {
            return Category::withTranslations('uz')->orderBy("order")->get();
        });
        //forget('name')forget
        $allCategories['categories'] = collect($categories)->where('parent_id', null)->all();

        $allCategories['categories2'] = collect($categories)->where('parent_id', '!=', null)->all();

        return $allCategories;
    }

    /**
     * Function  comlianse_saveS
     * Mazkur metod taskka qoldirilgan shikoyatlarni tablega yozib beradi va telegramga yuboradi
     * @param $data
     * @return bool
     */
    public function compliance_saveS($data): bool
    {
        $comp = Compliance::query()->create($data);
        $telegramService = new TelegramService();
        $data['id'] = $comp->id;
        $data['complaint'] = $comp->text;
        $data['user_name'] = User::query()->find($comp->user_id)->name;
        $data['task_name'] = Task::query()->find($comp->task_id)->name;
        $telegramService->sendMessage($data);
        return true;
    }

    /**
     * Function  task_service
     * Mazkur metod yaratilgan barcha tasklarni korsatib berad va kerakli ma'lumotlarni yuboradi
     * @param  $auth_response , $userId, $task Object
     * @param $userId
     * @param $task_id
     * @param $filter
     * @return array
     */
    #[ArrayShape(['item' => "\App\Item\SearchServiceTaskItem", 'task' => "mixed"])]
    public function task_service($auth_response, $userId, $task_id, $filter): array
    {
        $task = Task::with('category')->find($task_id);
        if (auth()->check() && $userId !== $task->user_id) {
            $viewed_tasks = Cache::get('user_viewed_tasks' . $userId) ?? [];
            if (!in_array($task->id, $viewed_tasks)) {
                $viewed_tasks[] = $task->id;
            }
            Cache::put('user_viewed_tasks' . $userId, $viewed_tasks);

            $task->views++;
            $task->save();
        }
        $item = new SearchServiceTaskItem();
        $item->complianceType = ComplianceType::all();
        $item->selected = $task->responses()->where('performer_id', $task->performer_id)->first();
        $item->responses = $item->selected ? $task->responses()->where('id', '!=', $item->selected->id) : $task->responses();
        $item->auth_response = $auth_response ? $task->responses()->where('performer_id', $userId)->with('user')->first() : null;
        $item->same_tasks = $task->category->tasks()->where('id', '!=', $task->id)->where('status', [Task::STATUS_OPEN, Task::STATUS_RESPONSE])->orderBy('created_at', 'desc')->get();
        $item->addresses = $task->addresses;
        $item->top_users = User::query()
            ->where('review_rating', '!=', 0)
            ->where('role_id', User::ROLE_PERFORMER)->orderbyRaw('(review_good - review_bad) DESC')
            ->limit(Review::TOP_USER)->pluck('id')->toArray();
        $item->respons_reviews = Review::query()->where('task_id',$task->id)->get();
        $item->responses = match ($filter) {
            'rating' => [],
            'date' => $item->responses->orderByDesc('created_at')->get(),
            'reviews' => [],
            default => $item->responses->get(),
        };
        $value = Carbon::parse($task->created_at)->locale((new CustomService)->getlocale());
        $value->minute < 10 ? $minut = '0' . $value->minute : $minut = $value->minute;
        $day = $value == now()->toDateTimeString() ? "Bugun" : "$value->day-$value->monthName";
        $item->created = "$day  $value->noZeroHour:$minut";

        $value = Carbon::parse($task->end_date)->locale((new CustomService)->getlocale());
        $value->minute < 10 ? $minut = '0' . $value->minute : $minut = $value->minute;
        $item->end = "$value->day-$value->monthName  $value->noZeroHour:$minut";

        $value = Carbon::parse($task->start_date)->locale((new CustomService)->getlocale());
        $value->minute < 10 ? $minut = '0' . $value->minute : $minut = $value->minute;
        $day = $value == now()->toDateTimeString() ? "Bugun" : "$value->day-$value->monthName";
        $item->start = "$day  $value->noZeroHour:$minut";
        return ['item' => $item, 'task' => $task];
    }

    /**
     * Function  search_new_service
     * Mazkur metod search task bladega ma'lumotlarni chiqarib beradi
     * @param  $arr_check , $filter, $suggest, $price, $remjob, $noresp, $radius,$lat,$lon,$filterByStartDate Object
     */
    public function search_new_service($arr_check, $filter, $price, $remjob, $noresp, $radius, $lat, $lon, $filterByStartDate): array
    {

        $users = User::all()->keyBy('id');
        $adresses = Address::all()->keyBy('id');
        $categories = Cache::remember('categoriesAll_', now()->addMinute(180), function () {
            return Category::all()->keyBy('id');
        });


        $adressesQuery = "
        SELECT task_id FROM ( SELECT task_id,
        6371 * acos(cos(radians($lat))
		        * cos(radians(`latitude`))
		        * cos(radians(`longitude`) - radians($lon))
		        + sin(radians($lat))
		        * sin(radians(`latitude`))) as distance FROM `addresses` where `default` = 1 ) addresses WHERE distance <=$radius";
        $results = [];

        if (!$remjob && $lat && $lon && $radius) {
            $results = DB::select(DB::raw($adressesQuery));
        }

        $relatedAdress = [];
        foreach ($results as $result) {
            $relatedAdress[] = $result->task_id;
        }

        $tasks = Task::query()
            ->whereIn('status', [Task::STATUS_OPEN, Task::STATUS_RESPONSE])
            ->when(count($relatedAdress), function ($query) use ($relatedAdress) {
                $query->whereIn('id', $relatedAdress);
            })
            ->when($filter, function ($query) use ($filter) {
                $query->where('name', 'like', "%{$filter}%");
            })
            ->when($price, function ($query) use ($price) {
                $query->whereBetween('budget', array(intval($price * 0.8), intval($price * 1.2)));
            })
            ->when($arr_check, function ($query) use ($arr_check) {
                $query->whereIn('category_id', $arr_check);
            })
            ->when($remjob, function ($query) {
                $query->where('remote', self::REMOTE_TASK);
            })
            ->when($noresp, function ($query) {
                $query->whereIn('status', [Task::STATUS_OPEN]);
            })
            ->when($filterByStartDate, function ($query) {
                $query->orderBy('start_date', 'desc');
            })
            ->when(!$filterByStartDate, function ($query) {
                $query->latest();
            })
            ->paginate(self::MAX_SEARCH_TASK);


        $tasks->transform(function ($task) use ($users, $adresses, $categories) {
            if ($users->contains($task->user_id)) {
                $task->user_name = $users->get($task->user_id)->name;
            }
            $allAdresses = $adresses->where('task_id', $task->id);
            $mainAdress = Arr::first($allAdresses);
            $task->address_main = Arr::get($mainAdress, 'location');
            $task->latitude = Arr::get($mainAdress, 'latitude');
            $task->longitude = Arr::get($mainAdress, 'longitude');

            if ($categories->contains($task->category_id)) {
                $task->category_icon = $categories->get($task->category_id)->ico;
                $task->category_name = $categories->get($task->category_id)->getTranslatedAttribute('name');
            }

            if ($task->start_date) {
                $value = Carbon::parse($task->start_date)->locale((new CustomService)->getlocale());
                $value->minute < 10 ? $minut = '0' . $value->minute : $minut = $value->minute;
                $task->sd_parse = "$value->day-$value->monthName  $value->noZeroHour:$minut";
            }
            if ($task->end_date) {
                $value = Carbon::parse($task->end_date)->locale((new CustomService)->getlocale());
                $value->minute < 10 ? $minut = '0' . $value->minute : $minut = $value->minute;
                $task->ed_parse = "$value->day-$value->monthName  $value->noZeroHour:$minut";
            }

            return $task;
        });
        $dataForMap = $tasks->map(function ($task) {
            return collect($task)
                ->only(['id', 'name', 'address_main', 'sd_parse', 'ed_parse', 'budget', 'latitude', 'longitude'])
                ->toArray();
        });

        return [$tasks, $dataForMap];
    }

    /**
     * task status change cancelled
     * @param $taskId
     * @param $authId
     * @return JsonResponse
     */
    public function cancelTask($taskId, $authId): JsonResponse
    {
        $task = Task::find($taskId);
        if ($task->user_id !== $authId){
            return response()->json([
                'success' => false,
                "message" => __("Отсутствует разрешение")
            ], 403);
        }
        $task->status = Task::STATUS_CANCELLED;
        $task->save();
        return response()->json([
            'success' => true,
            'message' => __('Успешно отменено'),
            'data' => $task
        ]);
    }

    /**
     * Task delete
     * @param $taskId
     * @param $userId
     * @return JsonResponse
     */
    public function delete_task($taskId, $userId): JsonResponse
    {
        $task = Task::find($taskId);
        $user = User::find($userId);
        if ($task->user_id !== $userId){
            return response()->json([
                'success' => false,
                "message" => __("Отсутствует разрешение")
            ], 403);
        }
        $task->delete();

        $user->active_step = null;
        $user->active_task = null;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => __('Успешно удалено')
        ]);
    }

    /**
     * user active_task task id save
     * @param $taskId
     * @param $user
     * @return JsonResponse
     */
    public function task_cancel($taskId, $user): JsonResponse
    {
        $task = Task::find($taskId);
        if ($task->user_id !== $user->id){
            return response()->json([
                'success' => false,
                "message" => __("Отсутствует разрешение")
            ], 403);
        }

        $user->active_task = $taskId;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => __('Успешно сохранено')
        ]);
    }

    /**
     * homepageda elastic searchda task nomlarini qaytaradi
     * @param $name
     * @return string
     */
    public function taskNames($name): string
    {
        $query = Query::wildcard()
            ->field('name')
            ->value('*' . $name . '*');
        $searchResult = TaskElastic::searchQuery($query)->execute();
        $tasks = $searchResult->models();
        $options = "";
        foreach ($tasks as $task) {
            $options .= "<option value='$task->name' id='$task->category_id'>$task->name</option>";
        }
        return $options;
    }

    /**
     * Favorite task create
     * @param $task_id
     * @param $userId
     * @return JsonResponse
     */
    public function favorite_task_create($task_id, $userId): JsonResponse
    {
        $task_exists = Task::query()->where('id',$task_id)->exists();
        if($task_exists){
            FavoriteTask::query()->updateOrCreate([
                'task_id' => $task_id,
                'user_id' => $userId
            ]);

            return response()->json([
                'success' => true,
                'message' => __('Успешно сохранено')
            ]);
        }

        return response()->json([
            'success' => false,
            "message" => __("Нет такой задачи")
        ], 403);
    }

    /**
     * Favorite task delete
     * @param $taskId
     * @param $userId
     * @return JsonResponse
     */
    public function favorite_task_delete($taskId, $userId): JsonResponse
    {
        $task_exists = Task::query()->where('id',$taskId)->exists();

        if($task_exists){
            $task_favorite = FavoriteTask::query()->where('task_id',$taskId)->where('user_id',$userId);
            $task_favorite->delete();
            return response()->json([
                'success' => true,
                'message' => __('Успешно удалено')
            ]);
        }

        return response()->json([
            'success' => false,
            "message" => __("Нет такой задачи")
        ], 403);
    }

    /**
     * favorite task all
     * @param $userId
     * @return array
     */
    public function favorite_task_all($userId): array
    {
        $favorite_tasks = FavoriteTask::query()->where('user_id',$userId)->get();
        $data = [];
        foreach ($favorite_tasks as $favorite_task) {
            $data[] = [
                'id' => $favorite_task->id,
                'user_id' => $favorite_task->user_id,
                'task' => (new FilterTaskService)->taskSingle($favorite_task->task)
            ];
        }
        return $data;
    }
}
