<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property $id favorite id
 * @property $task_is favorite task id
 * @property $user_id favorite task user id
 * @property $created_at favorite task create date
 */

class FavoriteTask extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'favorite_tasks';

    protected $fillable = [
        'user_id',
        'task_id',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
