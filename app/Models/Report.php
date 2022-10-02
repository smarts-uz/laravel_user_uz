<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property $id
 * @property $parent_name
 * @property $count
 * @property $amount
 * @property $created_at
 */

class Report extends Model
{
    use HasFactory;
}
