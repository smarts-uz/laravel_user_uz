<?php


namespace App\Services\Task;

use App\Models\Category;
use App\Models\Task;
use Illuminate\Support\Facades\Cache;

class FilterTaskService
{

    /**
     * Filter tasks with the parameters shown below
     * - latitude, longitude, difference // filter with location
     * - categories // filter with task categories
     * - child_categories filter with task child categories
     * - budget // filter with budget amount
     * - is_remote // filter with task can be work remotely
     * - without_response Filter with task does not have responses
     * - search // filter task by name, description, phone, budget by LIKE operator
     *
     *
     * @param $data
     * @return array
     */
    public function filter($data): array
    {
        $tasks = Task::query()->whereIn('status',[Task::STATUS_OPEN,Task::STATUS_RESPONSE]);

        $tasks_items =  [];
        if (isset($data['lat'], $data['long'], $data['difference']) && !isset($data['is_remote']))
        {
            foreach ($tasks->get() as $task) {
                $address = $task->addresses->first();
                if (isset($address->latitude, $address->longitude)) {
                    $k = $this->distance($data['lat'], $data['long'], $address->latitude, $address->longitude);
                    if ($k < $data['difference']) {
                        $tasks_items[] = $task->id;
                    }
                }
            }

        }else{
            $tasks_items = $tasks->pluck('id')->toArray();
        }
        $tasks->whereIn('id', $tasks_items);
        if (isset($data['categories'])) {
            $categories = is_array($data['categories'])?$data['categories']:json_decode($data['categories']);
            $childCategories = Category::query()->whereIn('parent_id',$categories)->pluck('id')->toArray();
            $allCategories = array_unique(array_merge($categories, $childCategories));
            $tasks->whereIn('category_id', $allCategories)->pluck('id')->toArray();
        }
        if (isset($data['child_categories'])) {
            $categories = is_array($data['child_categories'])?$data['child_categories']:json_decode($data['child_categories']);
            $tasks->whereIn('category_id', $categories)->pluck('id')->toArray();
        }
        if (isset($data['budget'])) {
            $tasks->where('budget', ">=", (int) $data['budget'] )->pluck('id')->toArray();
        }

        if (isset($data['is_remote'])) {
            $is_remote = $data['is_remote'];
            if ((string)$is_remote === 'true') {
                $tasks->where('remote', true);
            }
        }
        if (isset($data['without_response'])) {
            $without_response = $data['without_response'];
            if ((string)$without_response === 'true') {
                $tasks->where('status',Task::STATUS_OPEN);
            }
        }

        if (isset($data['s']))
        {
            $s = $data['s'];
            $tasks->where('name','like',"%$s%");
        }

        $datas = [];
        foreach ($tasks->orderByDesc('created_at')->get() as $task) {
            $datas[] = $this->taskSingle($task);
        }

        return $datas;
    }

    /**
     * task filter uchun radius qiymatni qaytaradi
     * @param $lat1
     * @param $lon1
     * @param $lat2
     * @param $lon2
     * @return float
     */
    public function distance($lat1, $lon1, $lat2, $lon2): float
    {
        $radlat1 =  pi() * $lat1/180;
        $radlat2 =  pi() * $lat2/180;
        $theta = $lon1-$lon2;
        $radtheta =  pi() * $theta/180;
        $dist = sin($radlat1) * sin($radlat2) +  cos($radlat1) * cos($radlat2) * cos($radtheta);
        $dist = acos($dist);
        $dist = $dist * 180/ pi();
        $dist *= 60 * 1.1515;
        return $dist * 1.609344;
    }

    public function taskSingle($task): array
    {
        return !empty($task) ? [
            'id' => $task->id,
            'name'=> $task->name,
            'addresses' => (new TaskService)->taskAddress($task->addresses),
            'start_date' => $task->start_date,
            'end_date' => $task->end_date,
            'budget' => $task->budget,
            'remote' => $task->remote,
            'oplata' => $task->oplata,
            'status' => $task->status,
            'category_icon' => asset('storage/'.$task->category->ico),
            'viewed' => in_array($task->id, Cache::get('user_viewed_tasks' . auth()->guard('api')->id()) ?? [], true) ?? false
        ]: [];
    }

    /**
     * Navbar blade uchun categoriyalarni qaaytaradi
     * @return array
     */
    public static function categories(): array
    {
        $datas = Cache::remember('category_', now()->addMinute(180), function () {
            return Category::withTranslations()->orderBy("order")->get();
        });

        $child_categories = [];
        $parent_categories = [];

        foreach ($datas as $data) {
            if ($data->parent_id === null) {
                $parent_categories[] = $data;
            } else {
                $child_categories[] = $data;
            }

        }

        foreach ($parent_categories as $parent_category) {

            foreach ($child_categories as $child_category) {
                if ((int)$parent_category->id === (int)$child_category->parent_id) {
                    $categories[$parent_category->id][] = $child_category;
                }
            }

        }
        return $categories;
    }

}
