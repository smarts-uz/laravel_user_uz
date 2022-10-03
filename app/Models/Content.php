<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * bu table dinamik qilingandagi ma'lumotlar yuklanadi
 * @property $id kiritilgan kontent idsi
 * @property $key kontentning kalit so'zi
 * @property $page kiritiladigan kontentning sahifasi
 * @property $text_uz kontent matnini o'zbek tilida kiritish
 * @property $text_ru kontent matnini rus tilida kiritish
 * @property $image kontent rasmi
 */

class Content extends Model
{
    use HasFactory;
}
