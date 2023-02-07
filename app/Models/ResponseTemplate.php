<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property $user_id
 * @property $title
 * @property $text
 */
class ResponseTemplate extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'response_template';
    protected $fillable = [
        'user_id',
        'title',
        'text',
    ];
}
