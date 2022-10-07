<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use TCG\Voyager\Traits\Translatable;

/**
 * @property $title Kiritilgan yangilik sarlavhasi
 * @property $text  Kiritilgan yangilik matni
 * @property $img  Kiritilgan yangilik rasmi
 * @property $created_at Yangilik kiritilgan vaqti
 */

class BlogNew extends Model
{
    use HasApiTokens, HasFactory, Notifiable, Translatable;
    protected $table = "blog_new";

    protected $translatable = ['title','text','desc'];
}
