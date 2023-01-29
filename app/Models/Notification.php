<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use TCG\Voyager\Traits\Translatable;

/**
 * @property $id
 * @property $user_id
 * @property $task_id
 * @property $name_task
 * @property $user
 * @property $type
 * @property $created_at
 *
 */
class Notification extends Model
{
    use HasFactory;
    use Translatable;

    protected array $translatable = ['description'];
    protected $fillable = ['user_id', 'performer_id', 'service_id', 'task_id', 'cat_id', 'description', 'name_task', 'type', 'is_read'];

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
        if ($user->role_id == 2) {
            return $query->orWhere('type', 1);
        }
        return $query;
    }
}
