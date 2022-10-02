<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Translatable;

/**
 * @property $id
 * @property $question
 * @property $q_descript
 * @property $answer_text
 * @property $category_id
 * @property $created_at
 */

class Faqs extends Model
{
    use HasFactory;
    use Translatable;
    protected $translatable = ['question','q_descript', 'answer_text'];

}
