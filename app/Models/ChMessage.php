<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property $id message id
 * @property $type message type
 * @property $from_id xabar yuborgan user idsi
 * @property $to_id xabar yuborilgan user idsi
 * @property $body xabar matni
 * @property $attachment yuborilgan fayl
 * @property $seen xabar o'qilgan yoki o'qilmagani
 * @property $created_at xabar kiritilgan vaqti
 */

class ChMessage extends Model
{
    use SoftDeletes;
}
