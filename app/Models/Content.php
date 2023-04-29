<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Rennokki\QueryCache\Traits\QueryCacheable;

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
    use HasFactory, SoftDeletes;

    use QueryCacheable;

    public $cacheFor = 3600;

    public $cacheTags = ['content'];

    public static function boot():void
    {
        parent::boot();

        // updating updated_by when model is updated
        static::updating(static function ($model) {
            if (!$model->isDirty('updated_by')) {
                $model->updated_by = Arr::get(auth()->user(), 'id');
            }
        });

        self::deleting(static function ($model) {
            $model->deleted_at = now();
            $model->deleted_by =  Arr::get(auth()->user(), 'id');
            $model->save();
        });

    }
}
