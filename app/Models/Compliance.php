<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Retrieve next step with additional fields
 * objects, relations
 * @property $text
 * @property $id
 * @property $name
 * @property $compliance_type_id
 * @property $user_id
 * @property $task_id
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
}
