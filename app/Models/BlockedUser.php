<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlockedUser extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'blocked_users';
    protected $guarded = [];
}
