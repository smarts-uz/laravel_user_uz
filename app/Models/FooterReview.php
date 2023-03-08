<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Translatable;

/**
 * @property $id
 * @property $name footer review user name
 * @property $text footer review text
 * @property $image footer review user image
 * @property $review_type footer review type, 1 user, 2 performer
 * @property $created_at footer review kiritilgan vaqti
 * @property $site_link review qoldirilgan sayt url
 */

class FooterReview extends Model
{
    use HasFactory,Translatable;

    protected $table = 'footer_review';

    protected $translatable = ['name','text'];
}
