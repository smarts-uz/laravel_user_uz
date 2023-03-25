<?php

namespace App\Models;

use App\Services\CustomService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;

/**
 * @return array //Value Returned
 * @property $id task id
 * @property $name vazifa nomi
 * @property $remote vazifa masofadan(1) yoki masofadan emasligi(0)
 * @property $date_type vazifaning vaqtlari turi
 * @property $start_date vazifa boshlash vaqti
 * @property $end_date vazifa tugash vaqti
 * @property $budget vazifa budjeti
 * @property $description vazifa tavsifi
 * @property $status vazifa statusi
 * @property $photos vazifaga kiritilgan rasmlar
 * @property $user_id vazifani yaratgan userning idsi
 * @property $category_id vazifa yaratilgan category id
 * @property $phone vazifa yaratuvchining telefon raqami
 * @property $views vazifani ko'rishlar soni
 * @property $performer_id vazifaning ijrochisi
 * @property $performer_review vazifa ijrochisining sharh qoldirgan(1) yoki qoldirmagani(0)
 * @property $coordinates vazifaning joylashuv koordinatasi
 * @property $docs vazifani bajarish bo'yicha hujjat kerak(1) yoki kerak emasligi(0)
 * @property $oplata vazifaning to'lov turi(naxt yoki karta orqali)
 * @property $go_back 2ta manzilli vazifalarda orqaga ham qaytishni bildiradi(1)
 * @property $responses_count otkliklari soni
 * @property $verify_code vazifani yaratuvchining telefoniga yuborilgan tasdiqlash kodi
 * @property $verify_expiration tasdiqlash kod amal qilish muddati
 * @property $not_completed_reason vazifa bajarilmagani haqidagi tavsif
 * @property $created_at vazifa yaratilgan vaqt
 * @property $deleted_at vazifa o'chirilgan vaqt
 * @property $deleted_by vazifani o'chirgan user idsi
 * @property mixed $addresses
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
        $value = Carbon::parse($this->created_at)->locale((new CustomService)->getlocale());
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
                $model->updated_by = Arr::get(auth()->user(), 'id');
            }
        });

        static::creating(static function ($model) {
            if (!$model->isDirty('created_by')) {
                $model->created_by =  Arr::get(auth()->user(), 'id');
            }
            if (!$model->isDirty('updated_by')) {
                $model->updated_by =  Arr::get(auth()->user(), 'id');
            }
        });

        self::deleting(static function (Task $task) {

            $task->responses()->delete();
            $task->custom_field_values()->delete();
            $task->addresses()->delete();
            foreach ($task->reviews as $review) {
                $review->delete();
            }
            $task->compliances()->delete();

            $task->deleted_at = now();
            $task->deleted_by =  Arr::get(auth()->user(), 'id');
            $task->save();
            Notification::query()->where('task_id', $task->id)->delete();
        });

    }

    public function getStatusTextAttribute()
    {
        return match (true) {
            (int)$this->status === self::STATUS_IN_PROGRESS => __('В исполнении'),
            $this->status < self::STATUS_IN_PROGRESS => __('Открыто'),
            (int)$this->status === self::STATUS_NOT_COMPLETED => __('Не выполнено'),
            (int)$this->status === self::STATUS_CANCELLED => __('Отменен'),
            default => __('Закрыто'),
        };
    }
}
