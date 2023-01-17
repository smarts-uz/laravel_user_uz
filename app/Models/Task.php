<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Elastic\ScoutDriverPlus\Searchable;
use Illuminate\Support\Facades\Auth;

/**
 * @return array //Value Returned
 *@property $user
 * @property $performer
 * @property $reviews
 * @property object $category
 * @property $id
 * @property $name
 * @property $reviews_count
 * @property $responses_count
 * @property $status
 * @property $remote
 * @property $budget
 * @property $oplata
 * @property $addresses
 * @property $photos
 * @property $user_id
 * @property $category_id
 * @property $phone
 * @property $views
 * @property $start_date
 * @property $end_date End Date
 * @property $verify_code
 * @property $verify_expiration
 * @property $performer_id
 * @property $created_at
 * @property \Illuminate\Support\Carbon|mixed $deleted_at
 * @property mixed $deleted_by
 */
class Task extends Model
{

    use HasFactory, SoftDeletes;

    public function toSearchableArray()
    {
        return [
            'name' => $this->name,
        ];
    }

    protected $table = 'tasks';

    protected $mapping = [
        'properties' => [
            'id' => [
                "type" => "id"
            ],
            'name' => [
                "type" => "string"
            ],
        ]
    ];

    public const STATUS_OPEN = 1;
    public const STATUS_RESPONSE = 2;
    public const STATUS_IN_PROGRESS = 3;
    public const STATUS_COMPLETE = 4;
    public const STATUS_NOT_COMPLETED = 5;
    public const STATUS_CANCELLED = 6;

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

        static::updating(static function ($model) {
            if (!$model->isDirty('updated_by')) {
                $model->updated_by = auth()->user()->id;
            }
        });

        static::creating(static function ($model) {
            if (!$model->isDirty('created_by')) {
                $model->created_by = auth()->user()->id;
            }
            if (!$model->isDirty('updated_by')) {
                $model->updated_by = auth()->user()->id;
            }
        });

        self::deleting(function (Task $task) {

            $task->responses()->delete();
            $task->custom_field_values()->delete();
            $task->addresses()->delete();
            foreach ($task->reviews as $review) {
                $review->delete();
            }
            $task->compliances()->delete();

            $task->deleted_at = now();
            $task->deleted_by = Auth::user()->id;
            $task->save();
            Notification::query()->where('task_id', $task->id)->delete();
        });

    }

    public function getStatusTextAttribute()
    {
        switch (true){
            case (int)$this->status === self::STATUS_IN_PROGRESS :
                return __('В исполнении');
            case $this->status < self::STATUS_IN_PROGRESS  :
                return __('Открыто');
            case (int)$this->status === self::STATUS_NOT_COMPLETED :
                return __('Не выполнено');
            case (int)$this->status === self::STATUS_CANCELLED :
                return __('Отменен');
            default :
                return __('Закрыто');
        }
    }
}
