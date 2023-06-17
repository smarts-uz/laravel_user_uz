<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property $id user notif id
 * @property $user_id user id
 * @property $notification_id bildirishnoma idsi
 * @property $response push bildirishnoma yuborilganda qaytgan javob
 */

class UserNotification extends Model
{
    use HasFactory;
    protected $table = 'user_notification';
    protected $fillable = [
        'user_id',
        'notification_id',
        'response'
    ];
}
