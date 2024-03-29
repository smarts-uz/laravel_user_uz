<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
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
    use HasFactory,Translatable, SoftDeletes;

    protected $table = 'footer_review';

    protected $translatable = ['name','text'];

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
