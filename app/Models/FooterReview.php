<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Translatable;

/**
 * @property $id
 * @property $name
 * @property $text
 * @property $image
 * @property $review_type
 * @property $created_at
 */

class FooterReview extends Model
{
    use HasFactory,Translatable;

    protected $table = 'footer_review';

    protected $translatable = ['name','text'];
}
