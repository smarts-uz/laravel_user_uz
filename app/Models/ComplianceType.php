<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Translatable;

/**
 * @property $id
 * @property $name
 * @property $created_at
 * @return array //Value Returned
 */

class ComplianceType extends Model
{
    use HasFactory;
    use Translatable;

    protected $translatable = ['name'];
}
