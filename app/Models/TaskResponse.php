<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskResponse extends Model
{
    use HasFactory;

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
        return "$day  $value->noZeroHour:$minut";
    }


}
