<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property $id
 * @property $image
 * @property $user_id
 */
class Portfolio extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'portfolios';
    protected $guarded =[];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
