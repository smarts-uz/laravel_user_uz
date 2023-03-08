<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property $task_id custom field task id
 * @property $custom_field_id custom field id
 * @property $value custom field value task id bo'yicha
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
