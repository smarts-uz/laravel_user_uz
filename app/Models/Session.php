<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    use HasFactory;





    public function getLastActiveAttribute()
    {
        $value = Carbon::parse($this->last_activity)->locale(getLocale());
        $value->minute<10 ? $minut = '0'.$value->minute : $minut = $value->minute;
        return "$value->day-$value->monthName  $value->noZeroHour:$minut";
    }


}
