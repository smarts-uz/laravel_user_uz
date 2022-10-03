<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
}
