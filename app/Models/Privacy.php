<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Translatable;

class Privacy extends Model
{
    use Translatable;
    protected $table = "privacies";
    protected $translatable = ['title','text'];

}
