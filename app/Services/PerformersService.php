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
        $item->tasks = Task::where('user_id', $authId)->get();
        $item->categories = Category::where('parent_id', null)->select('id', 'name', 'slug')->get();
        $item->categories2 = Category::where('parent_id', '<>', null)->select('id', 'parent_id', 'name')->get();
        $item->users = User::query()
            ->where('review_rating', '!=', 0)
            ->where('role_id', 2)
            ->orderbyRaw('(review_good - review_bad) DESC')->paginate(50);
        $item->top_users = User::where('role_id', 2)->orderbyRaw('(review_good - review_bad) DESC')->limit(20)->pluck('id')->toArray();
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
        $item->about = User::where('role_id', 2)->orderBy('review_rating', 'desc')->take(20)->get();
        $item->portfolios = $user->portfolios()->where('image', '!=', null)->get();
        $item ->review_good = User::find($user->id)->review_good;
        $item ->review_bad = User::find($user->id)->review_bad;
        $item ->review_rating = User::find($user->id)->review_rating;
        $item ->goodReviews = $user->goodReviews()->whereHas('task')->whereHas('user')->get();
        $item ->badReviews = $user->badReviews()->whereHas('task')->whereHas('user')->get();
        return $item;
    }

    public function perf_ajax($user,$cf_id){
        $item = new PerformerPrefItem();
        $item-> about = User::where('role_id', 2)->orderBy('review_rating', 'desc')->take(20)->get();
        $item->categories = Category::where('parent_id', null)->select('id', 'name', 'slug')->get();
        $item->categories2 = Category::where('parent_id', '<>', null)->select('id', 'parent_id', 'name')->get();
        $item-> cur_cat = Category::where('id', $cf_id)->get();
        $item-> child_categories = Category::get();
        $item->users = User::where('role_id', 2)->orderbyDesc('review_rating')->paginate(50);
        $item-> tasks = Task::where('user_id', Auth::id())->get();
        $item ->review_good = User::select('review_good')->get();
        $item ->review_bad = User::select('review_bad')->get();
        $item ->review_rating = User::select('review_rating')->get();
        return $item;
    }
}
