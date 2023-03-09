<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;

/**
 * @property $balance sayt tomonidan berilgan summa
 * @property $user_id sayt tomonidan qaysi userga berilgani
 */
class WalletBalance extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['user_id', 'balance'];


    public static function walletBalanceUpdateOrCreate($user_id, $amount)
    {
        $record = self::where(['user_id' => $user_id,])->latest()->first();

        if (!is_null($record)) {
            return tap($record)->update([
                'balance' => 1 * $record->balance + 1 * $amount
            ]);
        }

        return self::create([
            'user_id' => $user_id,
            'balance' => 1 * $amount
        ]);
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

        self::deleting(static function ($model) {
            if (!$model->isDirty('deleted_by')) {
                $model->deleted_by =  Arr::get(auth()->user(), 'id');
            }
        });

    }
}
