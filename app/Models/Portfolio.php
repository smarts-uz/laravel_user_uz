<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property $id
 * @property $image portfolio images
 * @property $user_id portfolio yaratgan userning idsi
 * @property $comment portfolio comment
 * @property $description portfolio tavsifi
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
