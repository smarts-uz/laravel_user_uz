<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property $task_id
 * @property $custom_field_id
 * @property $value
 */
class CustomFieldsValue extends Model
{
    use SoftDeletes;


    public function custom_field(){
    return $this->belongsTo(CustomField::class);

    }

}
