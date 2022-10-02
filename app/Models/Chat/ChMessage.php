<?php

namespace App\Models\Chat;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property $id
 * @property $type
 * @property $from_id
 * @property $to_id
 * @property $body
 * @property $attachment
 * @property $seen
 * @property $created_at
 */

class ChMessage extends Model
{
    use SoftDeletes;
}
