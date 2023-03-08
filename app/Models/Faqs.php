<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Translatable;

/**
 * @property $id
 * @property $question faq savoli
 * @property $q_descript faq savol tavsiloti
 * @property $answer_text faq javobi
 * @property $category_id faq category id
 * @property $created_at faq kiritilgan vaqti
 */

class Faqs extends Model
{
    use HasFactory;
    use Translatable;
    protected $translatable = ['question','q_descript', 'answer_text'];

}
