<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property $id
 * @property $user_id block qilgan user id
 * @property $blocked_user_id block qilingan user id
 * @property $created_at kiritilgan vaqti
 * @property $updated_at o'zgartirilgan vaqti
 * @property $deleted_at o'chirilgan vaqti
 */

class BlockedUser extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'blocked_users';
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class,'blocked_user_id');
    }
}
