<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property $id privacy id
 * @property $title privacy title
 * @property $text privacy text
 * @property $created_at privacy kiritilgan vaqt
 */

class Privacy extends Model
{
    protected $table = "privacies";
}
