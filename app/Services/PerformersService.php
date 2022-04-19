<?php


namespace App\Services;


use App\Events\MyEvent;
use App\Item\PerformerAppItem;
use App\Item\PerformerServiceItem;
use App\Item\PerformerUserItem;
use App\Models\Notification;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use TCG\Voyager\Models\Category;

class PerformersService
{

    /**
     *
     * Function  service
     * @link https://user.uz/performers
     * @param $authId integer Id of Performer
     * @param $user User Object
     * @return  PerformerServiceItem
     */
    public function service($authId, $user)
    {

        $item = new PerformerServiceItem();

        $item->tasks = Task::where('user_id', $authId)->get();
        $item->categories = Category::where('parent_id', null)->select('id', 'name', 'slug')->get();
        $item->categories2 = Category::where('parent_id', '<>', null)->select('id', 'parent_id', 'name')->get();
        $item->users = User::where('role_id', 2)->orderbyDesc('reviews')->paginate(50);



        $item->task_count = $user->performer_tasks_count;
        $item->about = User::where('role_id', 2)->orderBy('reviews', 'desc')->take(20)->get();

        return $item;


    }


    /**
     *
     * Function  performer
     * @link https://user.uz/performers/354
     * @param $user
     * @return  PerformerUserItem
     */
    public function performer($user)
    {
        $item = new PerformerUserItem();

        $item->task_count = $user->performer_tasks_count;
        $item->about = User::where('role_id', 2)->orderBy('reviews', 'desc')->take(20)->get();
        $item->reviews = $user->reviews()->get();
        $item->portfolios = $user->portfolios()->where('image', '!=', null)->get();

        return $item;
    }
}
