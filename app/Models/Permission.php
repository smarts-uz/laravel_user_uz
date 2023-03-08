<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property $id
 * @property $key permission key
 * @property $table_name table nomi
 * @property $created_at permission yaratilgan vaqti
 */


class Permission extends Model
{
    use HasFactory;
}
