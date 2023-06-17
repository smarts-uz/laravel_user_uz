<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use TCG\Voyager\Traits\Translatable;

/**
 * @property $id shikoyat turi idsi
 * @property $name shikoyat turining nomi
 * @property $created_at shikoyat turi kiritligan vaqti
 */

class ComplianceType extends Model
{
    use HasFactory;
    use Translatable, SoftDeletes;

    protected $translatable = ['name'];

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
