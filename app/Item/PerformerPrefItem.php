<?php

namespace App\Item;

use TCG\Voyager\Models\Category;
use App\Models\User;
use App\Models\Task;


class PerformerPrefItem
{
    public $about;
    public $task_count;
    public $cur_cat;
    public $child_categories;
    public $users;
    public $categories;
    public $tasks;
}