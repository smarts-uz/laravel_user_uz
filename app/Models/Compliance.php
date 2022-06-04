<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
