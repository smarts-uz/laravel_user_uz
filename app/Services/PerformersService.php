<?php


namespace App\Services;

use App\Item\PerformerPrefItem;
use App\Item\PerformerServiceItem;
use App\Item\PerformerUserItem;
use App\Models\Review;
use App\Models\Task;
use App\Models\User;
use App\Models\UserCategory;
use Carbon\Carbon;
use TCG\Voyager\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

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
        $item->user_categories = UserCategory::query()->where('user_id',$user->id)->pluck('category_id')->toArray();
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
    public function perf_ajax($authId,$search,$cf_id)
    {
        $item = new PerformerPrefItem();
        $item->categories = Category::query()->where('parent_id', null)
            ->select('id', 'name', 'slug')->orderBy("order", "asc")->get();
        $item->categories2 = Category::query()->where('parent_id', '<>', null)
            ->select('id', 'parent_id', 'name')->orderBy("order", "asc")->get();
        $item->user_categories = UserCategory::query()->where('category_id',$cf_id)->pluck('user_id')->toArray();
        $item->users = User::query()
            ->where('role_id', User::ROLE_PERFORMER)
            ->where('name', 'LIKE', "%{$search}%")
            ->whereIn('id', $item->user_categories)
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

    /**
     *
     * Function  perf_ajax
     * Mazkur metod performerlar bo'yicha filter qiladi
     * @param $data
     * @return LengthAwarePaginator
     */
    public function performer_filter($data): LengthAwarePaginator
    {
        $performers = User::query()->where('role_id', User::ROLE_PERFORMER);

        if (isset($data['categories'])) {
            $categories = is_array($data['categories'])?$data['categories']:json_decode($data['categories']);
            $childCategories = \App\Models\Category::query()->whereIn('parent_id',$categories)->pluck('id')->toArray();
            $allCategories = array_unique(array_merge($categories, $childCategories));
            $user_categories = UserCategory::query()->whereIn('category_id',$allCategories)->pluck('user_id')->toArray();
            $performers = $performers->whereIn('id', $user_categories);
        }
        if (isset($data['child_categories'])) {
            $categories = is_array($data['child_categories'])?$data['child_categories']:json_decode($data['child_categories']);
            $user_categories = UserCategory::query()->whereIn('category_id',$categories)->pluck('user_id')->toArray();
            $performers = $performers->whereIn('id', $user_categories);
        }

        if (isset($data['online']))
        {
            $date = Carbon::now()->subMinutes(2)->toDateTimeString();
            $performers = $performers->where('last_seen', ">=",$date);
        }

        if(isset($data['review']) && isset($data['desc'])){
            $performers = $performers
                ->orderByDesc('review_rating')
                ->orderByRaw('(review_good - review_bad) DESC');
        }

        if(isset($data['review']) && isset($data['asc'])){
            $performers = $performers
                ->orderBy('review_rating','asc')
                ->orderByRaw('(review_good - review_bad) DESC');
        }

        if (isset($data['alphabet']) && isset($data['desc']))
        {
            $performers = $performers->orderBy('name','desc');
        }

        if (isset($data['alphabet']) && isset($data['asc']))
        {
            $performers = $performers->orderBy('name','asc');
        }

        if (isset($data['search']))
        {
            $search = $data['search'];
            $performers = $performers->where('name','like',"%$search%");
        }

        return $performers->paginate(20);
    }

}
