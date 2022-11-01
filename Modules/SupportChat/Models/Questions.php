<?php

namespace Modules\SupportChat\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Translatable;

class Questions extends Model
{
    use HasFactory,Translatable;
    protected $translatable = ['text'];
    protected $table = 'questions';
}
