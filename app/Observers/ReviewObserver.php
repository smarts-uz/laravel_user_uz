<?php

namespace App\Observers;

use App\Models\Review;
use App\Models\User;

class ReviewObserver
{
    /**
     * Handle the Review "created" event.
     *
     * @param  Review  $review
     * @return void
     */
    public function created(Review $review)
    {
        /** @var User $user */
        $user = User::query()->find($review->user_id);
        $user->review_rating = round($user->review_good * 5 / (($user->review_good+$user->review_bad === 0) ? 1 : ($user->review_good + $user->review_bad)));
        $user->save();
        /** @var User $reviewer */
        $reviewer = User::query()->find($review->reviewer_id);
        $review->reviewer_name = $reviewer->name;
        $review->save();
    }
}
