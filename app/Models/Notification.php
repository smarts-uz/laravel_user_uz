<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use TCG\Voyager\Traits\Translatable;

/**
 * @property $id  bildirishnoma idsi
 * @property $user_id bildirishnoma yuborilgan user id
 * @property $task_id task id bo'yicha yuborilgan bildirishnoma
 * @property $name_task bildirishnoma yuborilgan task id
 * @property $type bildirishnoma turi
 * @property $news_id sayt yangilik haqidagi bildirishnomaning yangilik idsi
 * @property $description bildirishnoma tavsifi
 * @property $cat_id bildirishnoma yuborilgan task category id
 * @property $status push bildirishnoma yuborilgan yoki yuborilmagani
 * @property $is_read bildirishnoma o'qilgan yoki o'qilmagani
 * @property $performer_id performer id
 * @property $response bildirishnoma yuborilganda qaytaradigan qiymat
 * @property $created_at bildirishnoma yaratilgan vaqti
 *
 */

class Notification extends Model
{
    use HasFactory;
    use Translatable;

    /**
     * @var mixed|string
     */

    protected array $translatable = ['description'];
    protected $fillable = ['user_id', 'performer_id', 'service_id', 'task_id', 'cat_id', 'description', 'name_task', 'type', 'is_read','news_id'];

    public const TASK_CREATED = 1;
    public const NEWS_NOTIFICATION = 2;
    public const SYSTEM_NOTIFICATION = 3;
    public const GIVE_TASK = 4;
    public const RESPONSE_TO_TASK = 5;
    public const SEND_REVIEW = 6;
    public const SELECT_PERFORMER = 7;
    public const SEND_REVIEW_PERFORMER = 8;
    public const RESPONSE_TO_TASK_FOR_USER = 9;
    public const CANCELLED_TASK = 10;
    public const ADMIN_COMPLETE_TASK = 11;
    public const ADMIN_CANCEL_TASK = 12;
    public const NEW_PASSWORD = 13;
    public const WALLET_BALANCE = 14;
    public const TEST_FIREBASE_NOTIFICATION = 15;
    public const TEST_PUSHER_NOTIFICATION = 16;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function performer()
    {
        return $this->belongsTo(User::class);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function scopeNewTask($query, $user)
    {
        if ((int)$user->role_id === User::ROLE_PERFORMER) {
            return $query->orWhere('type', 1);
        }
        return $query;
    }
}
