<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id To'lov IDsi
 * @property int $user_id To'lov qilayotgan userning IDsi
 * @property int $amount To'lov qiymati ya'ni qanchaligi
 * @property string $method To'lov turi
 */
class All_transaction extends Model
{
    use HasFactory;
    const DRIVER_PAYME = 'Payme';
    const DRIVER_CLICK = 'Click';
    const DRIVER_PAYNET = 'Paynet';

    /** Pay in progress, order must not be changed. */
    const STATE_WAITING_PAY  = 1;
    /** Transaction completed and not available for sell. */
    const STATE_PAY_ACCEPTED = 2;
    /** Transaction is cancelled. */
    const STATE_CANCELLED    = 3;
    /** Transaction status is new. */
    const STATUS_NEW         = 0;
    /** Transaction status is paid success. */
    const STATUS_SUCCESS     = 1;
    /** Transaction status is rejected. */
    const STATUS_REJECTED    = -1;

    const METHODS = [
        'Payme' => 'Payme',
        'Click' => 'Click',
        'Paynet' => 'Paynet',
        'Task' => 'Task'
    ];

    protected $fillable = [
        'user_id',
        'amount',
        'method'
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
