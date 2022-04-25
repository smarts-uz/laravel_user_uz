<?php

namespace App\Observers;

use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ReviewObserver
{
    /**
     * Handle the Review "created" event.
     *
     * @param  \App\Models\Review  $review
     * @return void
     */
    public function created(Review $review)
    {
            $user = User::find($review->user_id);
            $review->good_bad ? $user->increment('review_good',1) : $user->increment('review_bad',1);
            $goods = $user->review_good;
            $bads = $user->review_bad;
            $user->review_rating = round($goods * 5 / (($goods+$bads==0) ? 1 : ($goods + $bads)));
            $user->save();
    }

    /**
     * Handle the Review "updated" event.
     *
     * @param  \App\Models\Review  $review
     * @return void
     */
    public function updated(Review $review)
    {
        //
    }

    /**
     * Handle the Review "deleted" event.
     *
     * @param  \App\Models\Review  $review
     * @return void
     */
    public function deleted(Review $review)
    {
        //
    }

    /**
     * Handle the Review "restored" event.
     *
     * @param  \App\Models\Review  $review
     * @return void
     */
    public function restored(Review $review)
    {
        //
    }

    /**
     * Handle the Review "force deleted" event.
     *
     * @param  \App\Models\Review  $review
     * @return void
     */
    public function forceDeleted(Review $review)
    {
        //
    }
}
