<?php

namespace App\Services\Task;

use App\Item\SearchServiceTaskItem;
use App\Models\Address;
use App\Models\ComplianceType;
use App\Models\Task;
use App\Models\Category;
use App\Models\User;
use App\Models\Compliance;
use App\Models\Review;
use App\Services\TelegramService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SearchService
{
    public const MAX_SEARCH_TASK = 20;
    public const REMOTE_TASK = 1;
    /**
     *
     * Function  comlianse_saveS
     * Mazkur metod taskka qoldirilgan shikoyatlarni tablega yozib beradi va telegramga yuboradi
     * @param Object
     * @return bool
     */
    public function comlianse_saveS($request)
    {
        $comp = new Compliance();
        $comp->compliance_type_id = $request->input('c_type');
        $comp->text = $request->input('c_text');
        $comp->user_id = $request->input('userId');
        $comp->task_id = $request->input('taskId');
        $comp->save();
        $telegramService = new TelegramService();
        $data['id'] = $comp->id;
        $data['complaint'] = $comp->text;
        $data['user_name'] = User::query()->find($comp->user_id)->name;
        $data['task_name'] = Task::query()->find($comp->task_id)->name;
        $telegramService->sendMessage($data);
        return true;
    }

    /**
     *
     * Function  task_service
     * Mazkur metod yaratilgan barcha tasklarni korsatib berad va kerakli ma'lumotlarni yuboradi
     * @param  $auth_response , $userId, $task Object
     * @param $userId
     * @param $task
     * @return  SearchServiceTaskItem
     */
    public function task_service($auth_response, $userId, $task): SearchServiceTaskItem
    {
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
        return $item;
    }

    /**
     *
     * Function  search_new_service
     * Mazkur metod search task bladega ma'lumotlarni chiqarib beradi
     * @param  $arr_check , $filter, $suggest, $price, $remjob, $noresp, $radius,$lat,$lon,$filterByStartDate Object
     */
    public function search_new_service($arr_check, $filter, $suggest, $price, $remjob, $noresp, $radius, $lat, $lon, $filterByStartDate)
    {

        $users = User::all()->keyBy('id');
        $categories = Category::all()->keyBy('id');
        $adresses = Address::all()->keyBy('id');
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
                $value = Carbon::parse($task->start_date)->locale(getLocale());
                $value->minute < 10 ? $minut = '0' . $value->minute : $minut = $value->minute;
                $task->sd_parse = "$value->day-$value->monthName  $value->noZeroHour:$minut";
            }
            if ($task->end_date) {
                $value = Carbon::parse($task->end_date)->locale(getLocale());
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
}
