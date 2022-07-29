<?php

namespace App\Models;


use App\Models\Chat\ChMessage;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

/**
 * @property $is_phone_number_verified
 * @property $is_email_verified
 * @property $verify_expiration
 * @property $verify_code
 * @property $phone_number
 * @property $id
 * @property $oplata
 * @property $photos
 * @property $user_id
 * @property $phone
 * @property $firebase_token
 * @property $sms_notification
 * @property $email_notification
 * @property $email
 * @property $name
 * @property $dark_mode
 * @property $password
 * @property $avatar
 * @property $role_id
 * @property $api_token
 * @property $remember_token
 * @return array //Value Returned
 */
class User extends \TCG\Voyager\Models\User
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $table = 'users';

    protected $guarded = [];

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

    public function getAgeAttribute()
    {
        return Carbon::parse($this->attributes['born_date'])->age;
    }

    public function appeals()
    {
        return $this->hasMany(Message::class);
    }

    public function Socials()
    {
        return $this->hasMany(Social::class);
    }

    public function scopeUpdateViews($query, $id)
    {
        return $query->whereId($id)->increment('views', 1);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'user_id', 'id');
    }

    public function goodReviews()
    {
        return $this->hasMany(Review::class, 'user_id', 'id')->where('good_bad', 1)->whereHas('task');
    }

    public function badReviews()
    {
        return $this->hasMany(Review::class, 'user_id', 'id')->where('good_bad', 0)->whereHas('task');
    }

    public function views()
    {
        return $this->hasMany(UserView::class, 'user_id');
    }

    public function performer_views()
    {
        return $this->hasMany(UserView::class, 'performer_id');
    }

    public function performer_tasks()
    {
        return $this->hasMany(Task::class, 'performer_id');
    }

    public function transactions()
    {
        return $this->hasMany(All_transaction::class)->orderBy('created_at', "DESC");
    }

    public function alerts()
    {
        return $this->hasMany(Notification::class);
    }


    public function closedResponses()
    {
        return $this->hasMany(Task::class, 'performer_id')->where('status', Task::STATUS_COMPLETE);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function walletBalance()
    {
        return $this->hasOne(WalletBalance::class);
    }

    public function getLastSeenAtAttribute()
    {
        $value = Carbon::parse($this->last_seen)->locale(getLocale());
        $value->minute<10 ? $minut = '0'.$value->minute : $minut = $value->minute;
        $day = $value == now()->toDateTimeString()? "Bugun": "$value->day-$value->monthName";
        return "$day  $value->noZeroHour:$minut";
    }

    public function portfolios()
    {
        return $this->hasMany(Portfolio::class);
    }

    public function sessions()
    {
        return $this->hasMany(Session::class);
    }

    public function messages()
    {

        return $this->hasMany(\App\Models\Chat\ChMessage::class, '');
    }

    public function getBalanceAttribute()
    {
        return $this->walletBalance->balance ?? 0;
    }

    public function isActive()
    {
        if ($this->is_active == 1) {
            return true;
        }
        return false;
    }

    public function compliances()
    {
        return $this->hasMany(Compliance::class);
    }

    public static function boot ()
    {
        parent::boot();

        self::deleting(function (User $user) {

            $user->tasks()->delete();
            $user->reviews()->delete();
            $user->portfolios()->delete();
            $user->compliances()->delete();

            ChMessage::query()->where('from_id', $user->id)->where('to_id', $user->id)->delete();
            TaskResponse::query()->where('user_id', $user->id)->orWhere('performer_id', $user->id)->delete();
            Notification::query()->where('user_id', $user->id)->orWhere('performer_id', $user->id)->delete();

            $user->walletBalance()->delete();
            $user->email = '_' . $user->email . '_' . Carbon::now();
            $user->phone_number = '_' . $user->phone_number . '_' . Carbon::now();
            $user->save();
        });
    }
}
