<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $table = 'reviews';
    protected $fillable = ['user_id','description','good_bad','reviewer_id','task_id', 'as_performer'];
    protected $with = ['user', 'reviewer','task'];


    public function user()
    {
        return $this->belongsTo(User::class,'reviewer_id');
    }
    public function reviewer()
    {
        return $this->belongsTo(User::class);
    }
    public function task()
    {
        return $this->belongsTo(Task::class);
    }
    public function getCreatedAttribute()
    {
        $value = Carbon::parse($this->created_at)->locale(getLocale());
        $value->minute<10 ? $minut = '0'.$value->minute : $minut = $value->minute;
        $day = $value == now()->toDateTimeString()? "Bugun": "$value->day-$value->monthName";
        return "$day  $value->noZeroHour:$minut";
    }
}
