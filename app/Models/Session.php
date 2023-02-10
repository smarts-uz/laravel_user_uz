<?php

namespace App\Models;

use App\Services\CustomService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Rennokki\QueryCache\Traits\QueryCacheable;

/**
 * @property $id
 * @property $user_id
 * @property $ip_address
 * @property $user_agent
 * @property $payload
 * @property $last_activity
 * @property $device_id
 * @property $device_name
 * @property $platform
 * @property $is_mobile
 * @property $firebase_token
 */

class Session extends Model
{
    use HasFactory;
    use QueryCacheable;

    public $cacheFor = 3600;

    public $cacheTags = ['session'];

    protected $guarded = [];
    protected $keyType = 'string';

    public $timestamps = false;

    public function getLastActiveAttribute()
    {
        $value = Carbon::parse($this->last_activity)->tz('Asia/Tashkent')->locale((new CustomService)->getlocale());
        $value->minute < 10 ? $minut = '0' . $value->minute : $minut = $value->minute;
        return "$value->day-$value->monthName  $value->noZeroHour:$minut";
    }

}
