<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use TCG\Voyager\Traits\Translatable;

/**
 * @property $id custom field id
 * @property $name custom field name
 * @property $title maxsus maydon sarlavhasi
 * @property $type custom field turi
 * @property $options custom field options qiymatlar
 * @property $values custom field value
 * @property $category_id custom field yaratilgan category id
 * @property $route task create route
 * @property $order custom field joylashuv o'rni
 * @property $description custom field tavsifi
 * @property $placeholder custom field type value placeholder
 * @property $label  custom field label
 * @property $created_at custom field kiritilgan vaqti
 * @property $deleted_at custom field o'chirilgan vaqti
 * @property $deleted_by custom fieldni o'chirgan user id
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


    public const ROUTE_NAME = 'name';
    public const ROUTE_ADDRESS = 'address';
    public const ROUTE_CUSTOM = 'custom';
    public const ROUTE_DATE = 'date';
    public const ROUTE_NOTE = 'note';
    public const ROUTE_BUDGET = 'budget';
    public const ROUTE_CONTACTS = 'contacts';
    public const ROUTE_REMOTE = 'remote';


    public function custom_field_values(){
        return $this->hasMany(CustomFieldsValue::class);
    }

    public function delete(): void
    {
        $this->deleted_at = now();
        $this->deleted_by = Arr::get(auth()->user(), 'id');
        $this->save();
    }

    protected static function boot()
    {
        parent::boot();

        static::updating(static function ($model) {
            if (!$model->isDirty('updated_by')) {
                $model->updated_by =  Arr::get(auth()->user(), 'id');
            }
        });

        static::creating(static function ($model) {
            if (!$model->isDirty('created_by')) {
                $model->created_by =  Arr::get(auth()->user(), 'id');
            }
            if (!$model->isDirty('updated_by')) {
                $model->updated_by =  Arr::get(auth()->user(), 'id');
            }
        });

    }
}
