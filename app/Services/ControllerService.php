<?php


namespace App\Services;

use App\Item\ControllerItem;
use App\Item\CategoryItem;
use App\Item\MyTaskItem;
use App\Models\Review;
use App\Models\Task;
use App\Models\TaskResponse;
use Illuminate\Support\Facades\Cache;
use App\Models\Category;
use App\Item\UserInfoItem;

class ControllerService
{
    public const MAX_HOMEPAGE_TASK = 20;

    /**
     *Category::query()->where('parent_id', null)->orderBy("order")->get();
     *Category::query()->where('parent_id','!=',null)->orderBy("order")->get();
     * Function  home
     * @link https://user.uz/
     * @param string|null $lang
     * @return  ControllerItem
     */
    public function home(string $lang = 'uz')
    {
        $category = Cache::remember('category_' . $lang, now()->addMinute(180), function () use($lang) {
            return Category::withTranslations($lang)->orderBy("order")->get();
        });

        $item = new ControllerItem();
        $item->categories = collect($category)->where('parent_id', null)->all();
        $item->tasks = Task::query()->where('status', Task::STATUS_OPEN)->orWhere('status', Task::STATUS_RESPONSE)->orderBy('id', 'desc')->take(self::MAX_HOMEPAGE_TASK)->get();
        $item->child_categories = collect($category)->where('parent_id', '!=', null)->all();
        return $item;

    }

    /**
     *
     * Function  category
     * Mazkur metod barcha kategoriyalarni chiqarib beradi
     * @param $id
     * @param string|null $lang
     * @return  CategoryItem
     */
    public function category($id, ?string $lang = 'uz'): CategoryItem
    {
        $category = Cache::remember('category_' . $lang, now()->addMinute(180), function () use($lang) {
            return Category::withTranslations($lang)->orderBy("order")->get();
        });

        $item = new CategoryItem();
        $item->categories = collect($category)->where('parent_id', null)->all();
        $item->choosed_category = Category::find($id);
        $item->child_categories =  collect($category)->where('parent_id',$id)->all();
        return $item;
    }

    /**
     *
     * Function  my_tasks
     * Mazkur metod my_task sahifasini ochib beradi
     * @param $user
     * @param string|null $lang
     * @return  MyTaskItem
     */
    public function my_tasks($user, ?string $lang = 'uz'): MyTaskItem
    {
        $category = Cache::remember('category_' . $lang, now()->addMinute(180), function () use($lang) {
            return Category::withTranslations($lang)->orderBy("order")->get();
        });
        $statuses = [
            Task::STATUS_OPEN,
            Task::STATUS_RESPONSE,
            Task::STATUS_IN_PROGRESS,
            Task::STATUS_COMPLETE,
            Task::STATUS_NOT_COMPLETED,
            Task::STATUS_CANCELLED
        ];
        $item = new MyTaskItem();
        $item->tasks = Task::query()->where('user_id', $user->id)->whereIn('status', $statuses)->latest()->get();
        $item->perform_tasks = Task::query()->where('performer_id', $user->id)->whereIn('status', $statuses)->latest()->get();
        $item->categories = collect($category)->where('parent_id', null)->all();
        $item->categories2 = collect($category)->where('parent_id', '!=', null)->all();
        return $item;
    }

    /**
     *
     * Function  user_info
     * Mazkur metod adminga user haqidagi ma'lumotlarni ochib beradi
     * @param Object
     * @return  UserInfoItem
     */
    public function user_info($user): UserInfoItem
    {
        $statuses = [
            Task::STATUS_OPEN,
            Task::STATUS_RESPONSE,
            Task::STATUS_IN_PROGRESS,
            Task::STATUS_COMPLETE,
            Task::STATUS_NOT_COMPLETED,
            Task::STATUS_CANCELLED
        ];
        $item = new UserInfoItem();
        $item->tasks = Task::query()->where('user_id', $user)->whereIn('status', $statuses)->latest()->get();
        $item->performer_tasks = Task::query()->where('performer_id', $user)->whereIn('status', $statuses)->latest()->get();
        $item->user_reviews = Review::query()->where('reviewer_id', $user)->latest()->get();
        $item->performer_reviews = Review::query()->where('user_id', $user)->latest()->get();
        $item->task_responses = TaskResponse::query()->where('performer_id', $user)->latest()->get();
        return $item;
    }
}
