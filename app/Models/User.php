<?php

namespace App\Models;
use App\Models\Portfolio;
use App\Models\Portfoliocomment;
use App\Models\Message;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
//use App\Models\Message;
use Laravel\Passport\HasApiTokens;
use App\Models\All_transaction;
use Illuminate\Support\Facades\Storage;

class User extends \TCG\Voyager\Models\User
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';

/*
* The attributes that are mass assignable.
*
* @var array<int, string>
     */
    protected $fillable = [
        'name',
        'role_id',
        'email',
        'phone_number',
        'age',
        'password',
        'description',
        'location',
        'text',
        'status',
        'facebook_id',
        'avatar',
        'google_id',
        'active_status',
        'is_email_verified'
    ];



    protected $withCount = ['views', 'tasks'];


    /*
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
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
    public function appeals(){
        return $this->hasMany(Message::class);
    }
    public function Socials(){
        return $this->hasMany(Social::class);
    }
    public function scopeUpdateViews($query, $id) {
        return $query->whereId($id)->increment('views', 1);
    }
    public function reviews() {
        return $this->hasMany(Review::class,'user_id','id');
    }
    public function views(){
        return $this->hasMany(UserView::class,'performer_id');
    }
    public function transactions(){
        return $this->hasMany(All_transaction::class)->orderBy('created_at',"DESC");
    }
    public function alerts(){
        return $this->hasMany(Notification::class);
    }

    public function closedResponses(){
        return $this->hasMany(Task::class, 'performer_id')->where('status', Task::STATUS_COMPLETE);
    }

    public function tasks(){
        return $this->hasMany(Task::class);
    }
<<<<<<< HEAD




=======
    public function walletBalance(){
        return $this->hasOne(WalletBalance::class);
    }
    public function portfoliocomments(){
        return $this->hasMany(Portfoliocomment::class);
    }
    public function portfolios(){
        return $this->hasMany(Portfolio::class);
    }
>>>>>>> cca6c75ca14e9c65a5d78780772060cd9b9ba20f
}
