<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Translatable;

/**
 * @property $id shikoyat turi idsi
 * @property $name shikoyat turining nomi
 * @property $created_at shikoyat turi kiritligan vaqti
 */

class ComplianceType extends Model
{
    use HasFactory;
    use Translatable;

    protected $translatable = ['name'];
}
