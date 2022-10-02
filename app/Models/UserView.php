<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property $id
 * @property $user_id
 * @property $performer_id
 * @property $created_at
 */

class UserView extends Model {

    protected $table = 'user_views';
    protected $fillable = ['user_id','count'];
}
