<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class CustomFieldsValue extends Model
{
    use SoftDeletes;


    public function custom_field(){
    return $this->belongsTo(CustomField::class);

    }

}
