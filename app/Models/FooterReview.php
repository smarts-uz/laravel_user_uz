<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Translatable;

class FooterReview extends Model
{
    use HasFactory,Translatable;
    protected $table = 'footer_review';
}
