<?php

namespace App\Observers;

use App\Models\Review;
use App\Models\User;

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
        $goods = $user->review_good;
        $bads = $user->review_bad;
        $user->review_rating = round($goods * 5 / (($goods+$bads==0) ? 1 : ($goods + $bads)));
        $user->save();
        $reviewer = User::query()->find($review->reviewer_id);
        $review->reviewer_name = $reviewer->name;
        $review->save();
        if(PHP_SAPI === 'cli')
            dd($user->review_good,$user->review_bad,$user->review_rating);
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
        $user = User::find($review->user_id);
        if (!$review->as_performer) {
            if($review->good_bad == 1) {
                $user->decrement('review_good');
            } else {
                $user->decrement('review_bad');
            }
        }
        $goods = $user->review_good;
        $bads = $user->review_bad;
        $user->review_rating = round($goods * 5 / (($goods+$bads==0) ? 1 : ($goods + $bads)));
        $user->save();
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
