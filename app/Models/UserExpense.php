<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property $user_id taskka otklik qoldirishda to'lov qilgan user idsi
 * @property $task_id otklik qilingan task id
 * @property $client_id taskni yaratgan user id
 * @property $amount yechib olingan pul miqdori
 */
class UserExpense extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'user_expenses';

    /**
     * @var array <string, string>
     */
    protected $fillable = [
        'user_id',
        'amount',
        'task_id',
        'client_id'
    ];

    /**
     * @var array <string>
     */
    protected $hidden = [];

    /**
     * @var array <string, string>
     */
    protected $casts = [];

}
