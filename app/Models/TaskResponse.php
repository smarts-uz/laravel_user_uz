<?php

namespace App\Models;

use App\Services\CustomService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property $id task response id
 * @property $description task response tavsifi
 * @property $user_id vazifani yaratgan userning idsi
 * @property $task_id otklik qoldirilgan vazifa idsi
 * @property $notificate
 * @property $price otklik narxi
 * @property $performer_id vazifaga otklik qoldirgan ijrochining idsi
 * @property $not_free pulli yoki tekin otklik
 *
 * @property $task
 * @property $user
 * @property $performer
 */

class TaskResponse extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['user_id', 'task_id', 'description', 'notificate', 'price', 'performer_id','not_free'];


    protected $with = ['user', 'task'];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function performer(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    public function getCreatedAttribute()
    {
        $value = Carbon::parse($this->created_at)->locale((new CustomService)->getlocale());
        $value->minute < 10 ? $minut = '0' . $value->minute : $minut = $value->minute;
        $day = $value == now()->toDateTimeString() ? "Bugun" : "$value->day-$value->monthName";
        return "$day $value->noZeroHour:$minut";
    }


    public static function boot ()
    {
        parent::boot();

        self::deleting(function (TaskResponse $response) {
            /** @var Task $task */
            $task = $response->task;
            if ($task !== null){
                if ((int)$task->status === Task::STATUS_IN_PROGRESS) {
                    $task->update(['status' => Task::STATUS_CANCELLED]);
                }
            }
        });
    }
}
