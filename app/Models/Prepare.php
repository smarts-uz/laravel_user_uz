<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property $id
 * @property $click_trans_id
 * @property $service_id
 * @property $click_paydoc_id
 * @property $merchant_trans_id
 * @property $amount
 * @property $action
 * @property $error
 * @property $error_note
 * @property $sign_time
 * @property $sign_string
 * @property $created_at
 * @return array //Value Returned
 */

class Prepare extends Model
{
    use HasFactory;
    protected $table = 'prepare';
}
