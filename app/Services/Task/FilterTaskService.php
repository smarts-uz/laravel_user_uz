<?php


namespace App\Services\Task;


use App\Models\Address;
use App\Models\Task;

class FilterTaskService
{


    public function filter($data)
    {
        $tasks = Task::query()->where('status', '=',Task::STATUS_OPEN);

        if (isset($data['categories'])) {
            $categories = $data['categories'];
            $tasks->whereIn('category_id', $categories);
        }
        if (isset($data['budget'])) {
            $tasks->where('budget', ">=", $data['budget'] );
        }
        if (isset($data['is_remote'])) {
            $is_remote = $data['is_remote'];
            if ($is_remote)
                $tasks->whereDoesntHave('address');
        }
        if (isset($data['without_response'])) {
            $without_response = $data['without_response'];
            if ($without_response)
                $tasks->whereDoesntHave('responses');

        }
        $tasks_items =  [];
        if (isset($data['lat']) && isset($data['long']) && isset($data['difference']))
        {
            foreach ($tasks->get() as $task) {
                foreach ($task->addresses as $address) {
                    $k = $this->distance($data['lat'], $data['long'],$address->latitude, $address->longitude);
                    if ($k < $data['difference'])
                    {
                        $tasks_items[] = $task;
                    }
                }
            }
        }else{
            $tasks_items = $tasks->get();
        }
        $tasks = $tasks_items;


        return $tasks;
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
