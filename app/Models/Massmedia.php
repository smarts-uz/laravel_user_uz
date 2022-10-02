<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Translatable;

/**
 * @property $id
 * @property $title
 * @property $description
 * @property $link
 * @property $created_at
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
