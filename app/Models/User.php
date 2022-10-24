<?php

namespace App\Models;


use App\Models\Chat\ChMessage;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

/**
 * @property $is_phone_number_verified user raqamini tasdiqlash, 1 bo'lsa tasdiqlangan, null bo'lsa tasdiqlanmagan
 * @property $is_email_verified user emailini tasdiqlash, 1 bo'lsa tasdiqlangan, null bo'lsa tasdiqlanmagan
 * @property $born_date user tug'ilgan kunini kiritadi
 * @property $verify_expiration
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
 * @property $dark_mode
 * @property $password userning passwordi
 * @property $avatar userning profilidagi rasmi
 * @property $last_seen userning oxirgi aktiv vaqti
 * @property $description userning profilida o'zi haqida qoldirgan izohi
 * @property $district
 * @property $role_id userning role_idsi, (1-admin, 2-performer, 5-user)
 * @property $google_id google akkountdan kirgandagi id
 * @property $facebook_id facebook akkountdan kirgandagi id
 * @property $apple_id apple akkountdan kirgandagi id
 * @property $api_token api token
 * @property $remember_token
 * @property $reviews userga qoldirilgan izohlari soni
 * @property $tokens
 * @property $tasks
 * @property $taskResponses
 * @property $reviewsObj
 * @property $map
 * @return array //Value Returned
 */
class User extends \TCG\Voyager\Models\User
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    const ROLE_ADMIN = 1;
    const ROLE_PERFORMER = 2;
    const ROLE_USER = 5;
    const ROLE_MODERATOR = 6;

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

    public function Socials(): HasMany
    {
        return $this->hasMany(Social::class);
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
        return $this->hasMany(All_transaction::class)->orderBy('created_at', "DESC");
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
        return $this->hasMany(Task::class);
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

        return $this->hasMany(\App\Models\Chat\ChMessage::class, '');
    }

    public function getBalanceAttribute()
    {
        return $this->walletBalance->balance ?? 0;
    }

    public function isActive()
    {
        if ((int)$this->is_active === 1) {
            return true;
        }
        return false;
    }

    public function compliances(): HasMany
    {
        return $this->hasMany(Compliance::class);
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

            ChMessage::query()->where('from_id', $user->id)->orWhere('to_id', $user->id)->delete();
            Notification::query()->where('user_id', $user->id)->orWhere('performer_id', $user->id)->delete();

            $user->walletBalance()->delete();
            $user->email = '_' . $user->email . '_' . Carbon::now();
            $user->phone_number = '_' . $user->phone_number . '_' . Carbon::now();
            $user->save();
        });
    }
}
