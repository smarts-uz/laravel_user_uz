<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Translatable;

/**
 * @property $id
 * @property $title massmedia sarlavhasi
 * @property $description massmedia tavsifi
 * @property $link  massmedia url
 * @property $created_at massmedia kiritilgan vaqt
 */

class Massmedia extends Model
{
    use HasFactory;
    use Translatable;
    protected $translatable = [
        'title',
        'description',
    ];
    protected $fillable = [
        'title',
        'description',
    ];
}
