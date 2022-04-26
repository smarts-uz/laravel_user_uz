<?php


namespace App\Services;



use App\Item\PerformerPrefItem;
use App\Item\PerformerServiceItem;
use App\Item\PerformerUserItem;
use App\Models\Task;
use App\Models\User;
use TCG\Voyager\Models\Category;
use Illuminate\Support\Facades\Auth;

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
        $item-> user_online = User::select("*")
        ->whereNotNull('last_seen')
        ->orderBy('last_seen', 'DESC');
        $item->tasks = Task::where('user_id', $authId)->get();
        $item->categories = Category::where('parent_id', null)->select('id', 'name', 'slug')->get();
        $item->categories2 = Category::where('parent_id', '<>', null)->select('id', 'parent_id', 'name')->get();
        $item->users = User::where('role_id', 2)->orderbyDesc('reviews')->paginate(50);
        $item->task_count = $user->performer_tasks_count;
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
        $item->about = User::where('role_id', 2)->orderBy('reviews', 'desc')->take(20)->get();
        $item->reviews = $user->reviews()->get();
        $item->portfolios = $user->portfolios()->where('image', '!=', null)->get();
        $item->task_count = $user->performer_tasks_count;
        return $item;
    }

    public function perf_ajax($user,$cf_id){
        $item = new PerformerPrefItem();
        $item-> about = User::where('role_id', 2)->orderBy('reviews', 'desc')->take(20)->get();
        $item->categories = Category::where('parent_id', null)->select('id', 'name', 'slug')->get();
        $item->categories2 = Category::where('parent_id', '<>', null)->select('id', 'parent_id', 'name')->get();
        $item-> cur_cat = Category::where('id', $cf_id)->get();
        $item-> child_categories = Category::get();
        $item-> users = User::where('role_id', 2)->paginate(50);
        $item-> tasks = Task::where('user_id', Auth::id())->get();
        return $item;
    }
}
