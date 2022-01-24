<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Task extends Model
{
    use HasFactory;

    const STATUS_NEW = 0;
    const STATUS_OPEN = 1;
    const STATUS_IN_PROGRESS = 2;
    const STATUS_COMPLETE = 3;
    const STATUS_CLOSED = 4;


    protected $guarded  = [];



    public function custom_field_values(){
        return $this->hasMany(CustomFieldsValue::class);
    }

    public function category(){
        return $this->belongsTo(Category::class);
    }



}
