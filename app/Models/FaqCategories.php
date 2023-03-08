<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
    use Translatable;
    protected $table = 'faq_categories';
    protected $translatable = ['title','description', 'cat_author'];

    protected $with = ['faqs'];

    public function faqs(){
        return $this->hasMany(Faqs::class,'category_id');
    }

}


