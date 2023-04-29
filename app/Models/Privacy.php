<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;

/**
 * @property $id privacy id
 * @property $title privacy title
 * @property $text privacy text
 * @property $created_at privacy kiritilgan vaqt
 */

class Privacy extends Model
{
    use SoftDeletes;
    protected $table = "privacies";

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
