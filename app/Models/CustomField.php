<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use TCG\Voyager\Traits\Translatable;

/**
 * @property $id
 * @property $name
 * @property $title
 * @property $type
 * @property $options
 * @property $values
 * @property $category_id
 * @property $route
 * @property $order
 * @property $description
 * @property $placeholder
 * @property $label
 * @property $options_ru
 * @property $created_at
 */

class CustomField extends Model
{
    use HasFactory, SoftDeletes;
    use Translatable;

    protected $casts = [
        'options' => 'array',
        'options_ru' => 'array'
    ];
    protected $translatable = ['title','description','placeholder','label','error_message'];


    const ROUTE_NAME = 'name';
    const ROUTE_ADDRESS = 'address';
    const ROUTE_CUSTOM = 'custom';
    const ROUTE_DATE = 'date';
    const ROUTE_NOTE = 'note';
    const ROUTE_BUDGET = 'budget';
    const ROUTE_CONTACTS = 'contacts';
    const ROUTE_REMOTE = 'remote';


    public function custom_field_values(){
        return $this->hasMany(CustomFieldsValue::class);
    }
}
