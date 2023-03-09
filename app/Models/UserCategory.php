<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property $id
 * @property $user_id  user id
 * @property $category_id user tanlagan categoriya idsi
 */

class UserCategory extends Model
{
    use HasFactory;
    protected $table = 'user_category';
    protected $fillable = [
      'user_id',
      'category_id'
    ];
    protected $withCount = ['category'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

}
