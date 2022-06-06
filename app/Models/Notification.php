<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use TCG\Voyager\Traits\Translatable;


class Notification extends Model
{
    use HasFactory;
    use Translatable, SoftDeletes;

    protected $translatable = ['description'];
    protected $fillable = ['user_id', 'performer_id', 'service_id', 'task_id', 'cat_id', 'description', 'name_task', 'type', 'is_read'];

    public const TASK_CREATED = 1;
    public const NEWS_NOTIFICATION = 2;
    public const SYSTEM_NOTIFICATION = 3;
    public const GIVE_TASK = 4;
    public const TASK_SELECTED = 5;
    public const SEND_REVIEW = 6;
    public const SELECT_PERFORMER = 7;
    public const SEND_REVIEW_PERFORMER = 8;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeNewTask($query, $user)
    {
        if ($user->role_id == 2) {
            return $query->orWhere('type', 1);
        }
        return $query;
    }
}
