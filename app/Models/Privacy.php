<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Privacy extends Model
{
    protected $table = "privacies";
    protected $translatable = ['title','text'];
}
