<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property $id
 * @property $social_name
 * @property $social_link
 * @property $user_id
 * @property $created_at
 * @return array //Value Returned
 */


class Social extends Model
{
    use HasFactory;
    public function User(){
        return $this->belongsTo(User::class);
    }
}
