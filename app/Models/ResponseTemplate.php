<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property $user_id response template user id
 * @property $title response template title
 * @property $text response template title
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
