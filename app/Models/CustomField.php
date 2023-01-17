<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
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
 * @property \Illuminate\Support\Carbon $deleted_at
 * @property $deleted_by
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

    public function delete(): void
    {
        $this->deleted_at = now();
        $this->deleted_by = Auth::user()->id;
        $this->save();
    }

    protected static function boot()
    {
        parent::boot();

        static::updating(static function ($model) {
            if (!$model->isDirty('updated_by')) {
                $model->updated_by = auth()->user()->id;
            }
        });

        static::creating(static function ($model) {
            if (!$model->isDirty('created_by')) {
                $model->created_by = auth()->user()->id;
            }
            if (!$model->isDirty('updated_by')) {
                $model->updated_by = auth()->user()->id;
            }
        });

    }
}
