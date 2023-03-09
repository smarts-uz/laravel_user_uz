<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property $id
 * @property $payment_system
 * @property $system_transaction_id
 * @property $amount
 * @property $currency_code
 * @property $state
 * @property $updated_time
 * @property $comment
 * @property $detail
 * @property $transactionable_type
 * @property $transactionable_id
 * @return array //Value Returned
 */

class Transaction extends Model
{
    use HasFactory;
    protected $guarded = [];

    public const STATE_COMPLETED = 2;

    public const DRIVER_TASK = 'task';

    public const METHODS = ['Payme', 'Click', 'Paynet', 'payme', 'click', 'paynet'];
}
