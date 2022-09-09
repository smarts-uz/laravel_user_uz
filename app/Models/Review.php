<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *  @property $user
 *  @property $good_bad
 *
 */
class Review extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'reviews';
    protected $fillable = ['user_id','description','good_bad','reviewer_id','task_id', 'as_performer'];
    protected $with = ['user', 'reviewer','task'];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
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

    public function scopeFromUserType($query, $type)
    {
        if ($type == 'user') {
            return $query->where('as_performer', 0);
        } elseif ($type == 'performer') {
            return $query->where('as_performer', 1);
        }
    }

    public function scopeType($query, $type)
    {
        if ($type == 'good') {
            return $query->where('good_bad', 1);
        } elseif ($type == 'bad') {
            return $query->where('good_bad', 0);
        } elseif ($type == 'all') {
            return $query;
        }
    }

    public static function boot ()
    {
        parent::boot();

        self::deleting(function (Review $review) {
            $user = $review->user;
            if ($review->good_bad == 1 && $user->review_good > 1) {
                $review->user->decrement('review_good');
            } elseif ($user->review_bad > 1) {
                $review->user->decrement('review_bad');
            }
        });
    }
}
