<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use TCG\Voyager\Traits\Translatable;

/**
 * @property $title Kiritilgan yangilik sarlavhasi
 * @property $text  Kiritilgan yangilik texti
 * @property $img  Kiritilgan yangilik rasmi
 * @property $desc  Kiritilgan yangilik matni
 * @property $created_at Yangilik kiritilgan vaqti
 */

class BlogNew extends Model
{
    use HasApiTokens, HasFactory, Notifiable, Translatable;
    protected $table = "blog_new";

    protected $translatable = ['title','text','desc'];

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class,'news_id');
    }

    public static function boot()
    {
        parent::boot();

        self::deleting(static function (BlogNew $blogNew){
            $blogNew->notifications()->delete();
        });
    }
}
