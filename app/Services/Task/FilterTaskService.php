<?php


namespace App\Services\Task;


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
            $tasks->where('budget', $data['budget'] );
        }
        if (isset($data['is_remote'])) {
            $is_remote = $data['is_remote'];
            if ($is_remote)
                $tasks->where('address',null );
        }

        if (isset($data['without_response'])) {
            $without_response = $data['without_response'];
            if ($without_response)
                $tasks->whereDoesntHave('responses');

        }


        return $tasks->get();
    }

}
