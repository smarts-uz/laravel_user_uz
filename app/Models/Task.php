<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use TCG\Voyager\Traits\Translatable;

/**
 * Retrieve next step with additional fields
 *
 * @property object $category
 * @property $id
 * @property $status
 * @property $budget
 * @property $oplata
 * @property $photos
 * @property $user_id
 * @property $phone
 * @property $verify_code
 * @property $verify_expiration
 * @return array //Value Returned
 */
class Task extends Model
{

    use HasFactory, SoftDeletes;
    //use Translatable;

    const STATUS_NEW = 0;
    const STATUS_OPEN = 1;
    const STATUS_RESPONSE = 2;
    const STATUS_IN_PROGRESS = 3;
    const STATUS_COMPLETE = 4;
    const STATUS_COMPLETE_WITHOUT_REVIEWS = 5;
    const STATUS_NOT_COMPLETE = 6;

    protected $guarded = [];

    protected $withCount = ['responses', 'reviews'];

    public function custom_field_values()
    {
        return $this->hasMany(CustomFieldsValue::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function performer()
    {
        return $this->belongsTo(User::class, 'performer_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function responses()
    {
        return $this->hasMany(TaskResponse::class);
    }

    public function getPriceAttribute()
    {
        return preg_replace('/[^0-9.]+/', '', $this->budget);
    }

    public function getCreatedAttribute()
    {
        $value = Carbon::parse($this->created_at)->locale(getLocale());
        $value->minute < 10 ? $minut = '0' . $value->minute : $minut = $value->minute;
        $day = $value == now()->toDateTimeString() ? "Bugun" : "$value->day-$value->monthName";
        return "$day $value->noZeroHour:$minut";
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }
    public function categories()
    {
        return $this->belongsTo(Category::class);
    }

    public function task_responses()
    {
        return $this->hasMany(TaskResponse::class);
    }

    public function compliances()
    {
        return $this->hasMany(Compliance::class);
    }

    public static function boot ()
    {
        parent::boot();

        self::deleting(function (Task $task) {

            $task->responses()->delete();
            $task->custom_field_values()->delete();
            $task->addresses()->delete();
            $task->reviews()->delete();
            $task->compliances()->delete();

            Notification::query()->where('task_id', $task->id)->delete();
        });
    }
}
