<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;

/**
 * Retrieve next step with additional fields
 * objects, relations
 * @property $text qoldirilgan shikoyat matni
 * @property $id shikoyat idsi
 * @property $compliance_type_id shikoyat turining idsi,bu compliance_type tablega bog'langan
 * @property $user_id shikoyat qoldirgan foydalanuvchi idsi
 * @property $task_id shikoyat qoldirilgan vazifa
 */
class Compliance extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'compliance_type_id',
        'user_id',
        'task_id',
        'text'
    ];


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
