<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compliance extends Model
{
    use HasFactory;

    protected $fillable = [
        'compliance_type_id',
        'user_id',
        'task_id',
        'text'
    ];
}
