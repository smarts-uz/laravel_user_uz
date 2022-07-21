<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    protected $guarded = [];

    const STATE_COMPLETED = 2;

    const DRIVER_TASK = 'task';

    const METHODS = ['Payme', 'Click', 'Paynet', 'payme', 'click', 'paynet'];
}
