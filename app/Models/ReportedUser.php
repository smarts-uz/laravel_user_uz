<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property $id
 * @property $user_id report qoldirgan user id
 * @property $reported_user_id report qoldirilgan user id
 * @property $message report matni
 * @property $created_at report kiritilgan vaqt
 */

class ReportedUser extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];
}
