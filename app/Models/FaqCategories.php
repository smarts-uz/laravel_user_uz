<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use TCG\Voyager\Traits\Translatable;

/**
 * @property $id faq id
 * @property $title faq categoriyasi
 * @property $description faq tavsifi
 * @property $cat_author faq category yaratgan role
 * @property $num_quest faq number
 * @property $logo faq image
 * @property $created_at faq kiritilgan vaqti
 */

class FaqCategories extends Model
{
    use HasFactory;
    use Translatable, SoftDeletes;
    protected $table = 'faq_categories';
    protected $translatable = ['title','description', 'cat_author'];

    protected $with = ['faqs'];

    public function faqs(){
        return $this->hasMany(Faqs::class,'category_id');
    }

    public static function boot():void
    {
        parent::boot();

        // updating updated_by when model is updated
        static::updating(static function ($model) {
            if (!$model->isDirty('updated_by')) {
                $model->updated_by = Arr::get(auth()->user(), 'id');
            }
        });

        self::deleting(static function ($model) {
            $model->deleted_at = now();
            $model->deleted_by =  Arr::get(auth()->user(), 'id');
            $model->save();
        });

    }

}


