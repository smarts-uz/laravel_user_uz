<?php

namespace App\Item;

use App\Models\User;
use App\Models\Task;

class ProfileCashItem
{
    public $user;
    public $balance;
    public $views;
    public $task_count;
    public $transactions;
    public $top_users;
    public $review_good;
    public $review_bad;
    public $review_rating;
}
