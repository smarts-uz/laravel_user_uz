<?php


namespace App\Services\Task;

use App\Models\Category;
use App\Models\Task;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

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
     * @return LengthAwarePaginator
     */
    public function filter($data): LengthAwarePaginator
    {
        $tasks = Task::query()->where('status', '=',Task::STATUS_OPEN);

        $tasks_items =  [];
        if (isset($data['lat']) && isset($data['long']) && isset($data['difference']) && !isset($data['is_remote']))
        {
            foreach ($tasks->get() as $task) {
                $address = $task->addresses->first();
                if (isset($address->latitude) && isset($address->longitude)) {
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
            $tasks->whereIn('category_id', $categories)->pluck('id')->toArray();
        }
        if (isset($data['child_categories'])) {
            $categories = is_array($data['child_categories'])?$data['child_categories']:json_decode($data['child_categories']);
            $tasks->whereIn('category_id', $categories)->pluck('id')->toArray();
        }
        if (isset($data['budget'])) {
            $tasks->where('budget', ">=", (int) $data['budget'] )->pluck('id')->toArray();
        }
        if (isset($data['is_remote']) && !(isset($data['lat']) && !isset($data['long']) && !isset($data['difference']))) {
            $is_remote = $data['is_remote'];
            if ($is_remote == 1)
                $tasks->where('remote', true);
        }
        if (isset($data['without_response'])) {
            $without_response = $data['without_response'];
            if ($without_response == 1)
                $tasks->whereDoesntHave('responses');
        }

        if (isset($data['s']))
        {
            $s = $data['s'];
            $tasks->where('name','like',"%$s%")
                ->orWhere('description', 'like',"%$s%")
                ->orWhere('budget', 'like',"%$s%");
        }

        return $tasks->orderByDesc('created_at')->paginate(10);
    }


    public function distance($lat1, $lon1, $lat2, $lon2) {
        $radlat1 =  pi() * $lat1/180;
        $radlat2 =  pi() * $lat2/180;
        $theta = $lon1-$lon2;
        $radtheta =  pi() * $theta/180;
        $dist = sin($radlat1) * sin($radlat2) +  cos($radlat1) * cos($radlat2) * cos($radtheta);
        $dist = acos($dist);
        $dist = $dist * 180/ pi();
        $dist = $dist * 60 * 1.1515;
        return $dist = $dist * 1.609344;
    }

}
