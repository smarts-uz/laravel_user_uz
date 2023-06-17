<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;
use TCG\Voyager\Traits\Translatable;

/**
 * @property $title Kiritilgan yangilik sarlavhasi
 * @property $text  Kiritilgan yangilik texti
 * @property $img  Kiritilgan yangilik rasmi
 * @property $desc  Kiritilgan yangilik matni
 * @property $created_at Yangilik kiritilgan vaqti
 * @property $deleted_at yangilikni chirgan vaqti
 * @property $updated_by yangilikni tahrirlagan foydalanuvchi idsi
 * @property $deleted_by yangilikni o'chirgan foydalanuvchi idsi
 */

class BlogNew extends Model
{
    use HasApiTokens, HasFactory, Notifiable, Translatable, SoftDeletes;
    protected $table = "blog_new";

    protected $translatable = ['title','text','desc'];

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class,'news_id');
    }

    public static function boot()
    {
        parent::boot();

        // updating updated_by when model is updated
        static::updating(static function ($model) {
            if (!$model->isDirty('updated_by')) {
                $model->updated_by = Arr::get(auth()->user(), 'id');
            }
        });

        self::deleting(static function ($model) {
            $model->notifications()->delete();
            $model->deleted_at = now();
            $model->deleted_by =  Arr::get(auth()->user(), 'id');
            $model->save();
        });

    }
}
