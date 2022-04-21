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
        $user = User::where('id',$review->user_id);
        $review->good_bad ? $user->increment('review_good',1) : $user->increment('review_bad',1);
        $goods = $user->review_good;
        $bads = $user->review_bad;
        $user->reviw_rating = round($goods * 5 / (($goods+$bads==0) ? 1 : ($goods + $bads)));
    }
}
