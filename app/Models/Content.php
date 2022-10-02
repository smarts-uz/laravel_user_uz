<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property $id
 * @property $key
 * @property $page
 * @property $text_uz
 * @property $text_ru
 * @property $image
 */

class Content extends Model
{
    use HasFactory;
}
