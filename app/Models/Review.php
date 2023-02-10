<?php

namespace App\Models;

use App\Services\CustomService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *  @property $user
 *  @property $good_bad
 *  @property $user_id
 *  @property $reviewer_id
 *  @property $reviewer_name
 *  @property $created_at
 *
 */
class Review extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'reviews';
    protected $fillable = ['user_id','description','good_bad','reviewer_id','task_id', 'as_performer'];
    protected $with = ['user', 'reviewer','task'];

    const TOP_USER = 20;

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
        $value = Carbon::parse($this->created_at)->locale((new CustomService)->getlocale());
        $value->minute<10 ? $minute = '0'.$value->minute : $minute = $value->minute;
        $day = $value == now()->toDateTimeString() ? "Bugun": "$value->day-$value->monthName";
        return "$day  $value->noZeroHour:$minute";
    }

    public function scopeFromUserType($query, $type)
    {
        switch ($type){
            case 'user' :
                return $query->where('as_performer', 0);
            case 'performer' :
                return $query->where('as_performer', 1);
        }
    }

    public function scopeType($query, $type)
    {
        switch ($type){
            case 'good' :
                return $query->where('good_bad', 1);
            case 'bad' :
                return $query->where('good_bad', 0);
            case  'all' :
                return $query;
        }
    }

    public static function boot ()
    {
        parent::boot();
        self::deleting(function (Review $review) {
            $user = $review->user;
            if ($user) {
                switch (true){
                    case (int)$review->good_bad === 1 && $user->review_good > 0 :
                        $user->decrement('review_good');
                        $user->decrement('reviews');
                        break;
                    case $user->review_bad > 0 :
                        $user->decrement('review_bad');
                        $user->decrement('reviews');
                        break;
                }
            }
        });
    }
}
