<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Translatable;

/**
 * @property $id region id
 * @property $name region nomi
 * @property $created_at region yaratilgan vaqti
 */

class Region extends Model
{
    use HasFactory;
    use Translatable;
    protected $translatable = ['name'];
}
