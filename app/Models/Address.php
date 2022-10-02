<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Translatable;

/**
 * @property $id
 * @property $task_id  Address qaysi taskka tegishliligini bildiradi
 * @property $location Taskka kiritilgan joylashuvi
 * @property $latitude Address kengligi koordinatasi
 * @property $longitude Address uzunligi koordinatasi
 * @property $default
 * @property $created_at  Address kiritilgan vaqti
 * @property $updated_at  Address o'zgartirilgan vaqti
 * @return array //Value Returned
 */

class Address extends Model
{
    use HasFactory;

    protected $guarded = [];


    public function task()
    {
        $this->belongsTo(Task::class);
    }



}
