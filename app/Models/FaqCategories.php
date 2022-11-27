<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Translatable;

/**
 * @property $id
 * @property $title
 * @property $description
 * @property $cat_author
 * @property $num_quest
 * @property $logo
 * @property $created_at
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


