<?php


namespace App\Services;

use App\Item\PerformerPrefItem;
use App\Item\PerformerServiceItem;
use App\Item\PerformerUserItem;
use App\Models\Review;
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
     * @param $authId
     * @return  PerformerServiceItem
     */
    public function service($authId,$search)
    {
        $item = new PerformerServiceItem();
        $item->tasks = Task::query()->where('user_id', $authId)
            ->whereIn('status', [Task::STATUS_OPEN, Task::STATUS_RESPONSE])->orderBy('created_at', 'DESC')
            ->get();
        $item->categories = Category::query()->where('parent_id', null)
            ->select('id', 'name', 'slug')->orderBy("order", "asc")->get();
        $item->categories2 = Category::query()->where('parent_id', '<>', null)
            ->select('id', 'parent_id', 'name')->orderBy("order", "asc")->get();
        $item->users = User::query()
            ->where('role_id', User::ROLE_PERFORMER)
            ->where('name', 'LIKE', "%{$search}%")
            ->orderByDesc('review_rating')
            ->orderbyRaw('(review_good - review_bad) DESC')->paginate(50);
        $item->top_users = User::query()
            ->where('review_rating', '!=', 0)
            ->where('role_id', User::ROLE_PERFORMER)->orderbyRaw('(review_good - review_bad) DESC')
            ->limit(Review::TOP_USER)->pluck('id')->toArray();
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
        $item->top_users = User::query()
            ->where('review_rating', '!=', 0)
            ->where('role_id', User::ROLE_PERFORMER)->orderbyRaw('(review_good - review_bad) DESC')
            ->limit(Review::TOP_USER)->pluck('id')->toArray();
        $item->portfolios = $user->portfolios()->where('image', '!=', null)->get();
        $item->review_good = $user->review_good;
        $item->review_bad = $user->review_bad;
        $item->review_rating = $user->review_rating;
        $item->goodReviews = $user->goodReviews()->whereHas('task')->whereHas('user')->latest()->get();
        $item->badReviews = $user->badReviews()->whereHas('task')->whereHas('user')->latest()->get();
        $item->task_count = Task::query()->where('user_id', Auth::id())
            ->whereIn('status', [Task::STATUS_OPEN, Task::STATUS_RESPONSE, Task::STATUS_IN_PROGRESS, Task::STATUS_COMPLETE, Task::STATUS_NOT_COMPLETED, Task::STATUS_CANCELLED])->get();
        return $item;
    }

    /**
     *
     * Function  perf_ajax
     * Mazkur metod categoriya bo'yicha performerlarni chiqarib beradi
     * @param $cf_id
     * @param $authId
     * @return PerformerPrefItem
     */
    public function perf_ajax($cf_id, $authId,$search)
    {
        $item = new PerformerPrefItem();
        $item->categories = Category::query()->where('parent_id', null)
            ->select('id', 'name', 'slug')->orderBy("order", "asc")->get();
        $item->categories2 = Category::query()->where('parent_id', '<>', null)
            ->select('id', 'parent_id', 'name')->orderBy("order", "asc")->get();
        $item->cur_cat = Category::query()->where('id', $cf_id)->get();
        $item->child_categories = Category::all();
        $item->users = User::query()
            ->where('role_id', User::ROLE_PERFORMER)
            ->where('name', 'LIKE', "%{$search}%")
            ->orderByDesc('review_rating')
            ->orderbyRaw('(review_good - review_bad) DESC')->paginate(50);
        $item->top_users = User::query()
            ->where('review_rating', '!=', 0)
            ->where('role_id', User::ROLE_PERFORMER)->orderbyRaw('(review_good - review_bad) DESC')
            ->limit(Review::TOP_USER)->pluck('id')->toArray();
        $item->tasks = Task::query()->where('user_id', $authId)
            ->whereIn('status', [Task::STATUS_OPEN, Task::STATUS_RESPONSE])->orderBy('created_at', 'DESC')->get();
        return $item;
    }
}
