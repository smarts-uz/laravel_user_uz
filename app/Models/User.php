<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Arr;
use Laravel\Passport\HasApiTokens;

/**
 * @return array //Value Returned
 * @property $is_phone_number_verified user raqamini tasdiqlash, 1 bo'lsa tasdiqlangan, null bo'lsa tasdiqlanmagan
 * @property $is_email_verified user emailini tasdiqlash, 1 bo'lsa tasdiqlangan, null bo'lsa tasdiqlanmagan
 * @property $born_date user tug'ilgan kunini kiritadi
 * @property $verify_expiration tasdiqlash kodi yaroqlilik muddati
 * @property $verify_code tasdiqlash kodi
 * @property $phone_number userning telefon raqami
 * @property $phone_number_old userning avvalgi telefon raqami
 * @property $id user id
 * @property $is_active profil faol yoki faolmasligini bildiradi, 1 bo'lsa faol yoki aksincha.
 * @property $review_rating sayt bo'yicha o'rtacha bahosi
 * @property $review_good saytda olgan ijobiy izohlari
 * @property $review_bad saytda olgan salbiy izohlari
 * @property $youtube_link userning youtobedan joylagan linki
 * @property $firebase_token  firebase token
 * @property $sms_notification  sms orqali keladigan bildirishnomalar yoqilgan(1) yoki yoqilmaganligini(null yoki 0) bildiradi
 * @property $email_notification email orqali keladigan bildirishnomalar yoqilgan(1) yoki yoqilmaganligini(null yoki 0) bildiradi
 * @property $news_notification yangiliklar haqidagi bildirishnomalar yoqilgan(1) yoki yoqilmaganligini(null yoki 0) bildiradi
 * @property $system_notification tizim haqidagi bildirishnomalar yoqilgan(1) yoki yoqilmaganligini(null yoki 0) bildiradi
 * @property $email userning emaili
 * @property $name userning nomi
 * @property $last_name userning familyasi
 * @property $location userning joylashuvi
 * @property $gender userning jinsi
 * @property $dark_mode chat orqa foni
 * @property $password userning passwordi
 * @property $avatar userning profilidagi rasmi
 * @property $last_seen userning oxirgi aktiv vaqti
 * @property $description userning profilida o'zi haqida qoldirgan izohi
 * @property $district yashash joyi
 * @property $role_id userning role_idsi, (1-admin, 2-performer, 5-user)
 * @property $google_id google akkountdan kirgandagi id
 * @property $facebook_id facebook akkountdan kirgandagi id
 * @property $apple_id apple akkountdan kirgandagi id
 * @property $api_token api token
 * @property $remember_token remember token
 * @property $reviews userga qoldirilgan izohlari soni
 * @property $tokens
 * @property $tasks
 * @property $taskResponses
 * @property $reviewsObj
 * @property $updated_password_at admin tomonidan password o'zgartirilgan vaqti
 * @property $updated_password_by qaysi iddagi admin o'zgartirgani passwordni
 * @property $active_task user yaratayotgan vazifa idsi
 * @property $active_step user yaratayotgan vazifa qaysi bosqichdaligi
 * @property $work_experience ish tajribasi
 * @property $deleted_by qaysi user o'chirgani
 * @property $version app versiya
 * @property $notification_off push bildirishnomani o'chirib qo'yish
 * @property $notification_to push bildirishnomani qachongacha o'chirish
 * @property $notification_from push bildirishnomani qachondan o'chirish
 * @property $deleted_at user profili o'chirilgan vaqti
 * @property $sessions user sessiyalari
 * @property $settings user adminkadagi tanlagan tili
 * @property $post_id
 * @property $discussion_post_id
 * @property $reply_message
 */
class User extends \TCG\Voyager\Models\User
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;


    public const ROLE_ADMIN = 1;
    public const ROLE_PERFORMER = 2;
    public const ROLE_USER = 5;
    public const ROLE_MODERATOR = 6;

    protected $table = 'users';

    protected $guarded = [];

    protected $appends = ['has_password'];

    protected $withCount = ['views', 'tasks', 'performer_views', 'performer_tasks'];

    protected $hidden = [
        'password',
        'remember_token',
    ];
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getHasPasswordAttribute()
    {
        return ! empty($this->attributes['password']);
    }

    public function scopeUpdateViews($query, $id)
    {
        return $query->whereId($id)->increment('views', 1);
    }

    public function reviewsObj(): HasMany
    {
        return $this->hasMany(Review::class, 'reviewer_id', 'id');
    }

    public function goodReviews()
    {
        return $this->hasMany(Review::class, 'user_id', 'id')->where('good_bad', 1)->whereHas('task');
    }

    public function badReviews()
    {
        return $this->hasMany(Review::class, 'user_id', 'id')->where('good_bad', 0)->whereHas('task');
    }

    public function views(): HasMany
    {
        return $this->hasMany(UserView::class, 'user_id');
    }

    public function performer_views(): HasMany
    {
        return $this->hasMany(UserView::class, 'performer_id');
    }

    public function performer_tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'performer_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'transactionable_id')->orderBy('created_at', "DESC");
    }

    public function alerts(): HasMany
    {
        return $this->hasMany(Notification::class);
    }


    public function closedResponses(): HasMany
    {
        return $this->hasMany(Task::class, 'performer_id')->where('status', Task::STATUS_COMPLETE);
    }

    public function taskResponses(): HasMany
    {
        return $this->hasMany(TaskResponse::class, 'performer_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class)->whereIn('status',[Task::STATUS_OPEN, Task::STATUS_RESPONSE, Task::STATUS_IN_PROGRESS, Task::STATUS_COMPLETE, Task::STATUS_NOT_COMPLETED, Task::STATUS_CANCELLED]);
    }

    public function walletBalance()
    {
        return $this->hasOne(WalletBalance::class);
    }

    public function getLastSeenAtAttribute(): string
    {
        return Carbon::parse($this->attributes['last_seen'])->locale(app()->getLocale() . '-' . app()->getLocale())->diffForHumans();
    }

    public function portfolios(): HasMany
    {
        return $this->hasMany(Portfolio::class);
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(Session::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChMessage::class, '');
    }

    public function getBalanceAttribute()
    {
        return $this->walletBalance->balance ?? 0;
    }

    public function isActive(): bool
    {
        return (int)$this->is_active === 1;
    }

    public function compliances(): HasMany
    {
        return $this->hasMany(Compliance::class);
    }

    public function blockedUser(): HasMany
    {
        return $this->hasMany(BlockedUser::class,'blocked_user_id');
    }

    public function userCategory(): HasMany
    {
        return $this->hasMany(UserCategory::class);
    }

    public static function boot ()
    {
        parent::boot();

        self::deleting(function (User $user) {

            foreach ($user->tasks as $task) {
                $task->delete();
            }
            foreach ($user->reviewsObj as $review) {
                $review->delete();
            }
            foreach ($user->taskResponses as $response) {
                $response->delete();
            }

            $user->portfolios()->delete();
            $user->compliances()->delete();
            $user->blockedUser()->delete();
            $user->userCategory()->delete();

            ChMessage::query()->where('from_id', $user->id)->orWhere('to_id', $user->id)->delete();
            Notification::query()->where('user_id', $user->id)->orWhere('performer_id', $user->id)->delete();

            $user->walletBalance()->delete();
            $user->email = '_' . $user->email . '_' . $user->id;
            $user->phone_number = '_' . $user->phone_number . '_' . $user->id;
            $user->google_id = '_' . $user->google_id . '_' . $user->id;
            $user->firebase_token = '_' . $user->firebase_token . '_' . $user->id;
            $user->facebook_id = '_' . $user->facebook_id . '_' . $user->id;
            $user->apple_id = '_' . $user->apple_id . '_' . $user->id;

            $user->deleted_at = now();
            $user->deleted_by = Arr::get(auth()->user(), 'id');
            $user->save();

        });

        static::creating(static function ($model) {
            if (!$model->isDirty('created_by')) {
                $model->created_by = Arr::get(auth()->user(), 'id');
            }
            if (!$model->isDirty('updated_by')) {
                $model->updated_by = Arr::get(auth()->user(), 'id');
            }
        });

        static::updating(static function ($model) {
            if (!$model->isDirty('updated_by')) {
                $model->updated_by = Arr::get(auth()->user(), 'id');
            }
        });
    }
}
