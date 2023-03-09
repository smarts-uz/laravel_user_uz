<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property $id
 * @property $user_id profili ko'rilgan user idsi
 * @property $performer_id profilni ko'rgan user idsi
 * @property $created_at kiritilgan vaqti
 */

class UserView extends Model {

    protected $table = 'user_views';
    protected $fillable = ['user_id','count'];
}
