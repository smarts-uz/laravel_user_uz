<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property $custom_field
 *
 *
 * @property $task_id
 * @property $custom_field_id
 * @property $value
 */
class CustomFieldsValue extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function custom_field()
    {
        return $this->belongsTo(CustomField::class);
    }

    public function getValuesByIds()
    {
        if (app()->getLocale() === 'uz') {
            return array_intersect_key($this->custom_field->options['options'], array_flip(json_decode($this->value)));
        }
        return array_intersect_key($this->custom_field->options['options_ru'], array_flip(json_decode($this->value)));
    }

}
