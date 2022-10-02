<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property $id
 * @property $description
 * @property $user_id
 * @property $task_id
 * @property $notificate
 * @property $price
 * @property $performer_id
 * @property $not_free
 * @property $created_at
 * @return array //Value Returned
 */

class TaskResponse extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['user_id', 'task_id', 'description', 'notificate', 'price', 'performer_id','not_free'];


    protected $with = ['user', 'task'];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function performer()
    {
        return $this->belongsTo(User::class);
    }


    public function getCreatedAttribute()
    {
        $value = Carbon::parse($this->created_at)->locale(getLocale());
        $value->minute < 10 ? $minut = '0' . $value->minute : $minut = $value->minute;
        $day = $value == now()->toDateTimeString() ? "Bugun" : "$value->day-$value->monthName";
        return "$day $value->noZeroHour:$minut";
    }


}
