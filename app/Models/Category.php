<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Rennokki\QueryCache\Traits\QueryCacheable;
use TCG\Voyager\Traits\Translatable;

/**
 * @property $parent_id kategoriyaning qaysi ota-ona guruhiga mansubligini bildiradi
 * @property $order kategoriyanng har bir guruh bo'yicha tartib bilan berilgan idlari
 * @property $name kategoriyaning nomi
 * @property $custom_route_title Custom fielddan qo'shilgan routening sarlavhasi
 * @property $max Kategoriya bo'yicha kiritiladigan maxsimal narx,buni saytda teng beshga bo'lib ko'rsatiladi
 * @property $slug kategoriyalarning o'zbekcha yozuvi, qo'shimchalar uchun
 * @property $ico Kategoriyaning belgisi
 * @property $double_address ikkita adress kiritiladigan kategoriyalar
 * @property $remote masofadan ishlasa bo'ladigan kategoriyalar
 * @property $created_at Kategoriya kiritilgan vaqti
 * @property $deleted_at Kategoriya o'chirilgan vaqti
 * @property $deleted_by kategoriyani o'chirgan userning idsi
 */

class Category extends Model
{
    use HasFactory,SoftDeletes;
    use Translatable;
    use QueryCacheable;

    public $cacheFor = 360000;

    public $cacheTags = ['category'];


    protected array $translatable = ['name'];

    protected $table = "categories";
    protected $withCount = ['tasks'];

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function getIcoAttribute($value): string
    {
        return $value == null ? (self::find($this->parent_id)->ico) : $value;
    }


    public function custom_fields(){
        return $this->hasMany(CustomField::class);
    }

    public function customFieldsInName(){
        return $this->hasMany(CustomField::class)->where('route', CustomField::ROUTE_NAME);
    }
    public function customFieldsInAddress(){
        return $this->hasMany(CustomField::class)->where('route', CustomField::ROUTE_ADDRESS);
    }
    public function customFieldsInBudget(){
        return $this->hasMany(CustomField::class)->where('route', CustomField::ROUTE_BUDGET);
    }
    public function customFieldsInNote(){
        return $this->hasMany(CustomField::class)->where('route', CustomField::ROUTE_NOTE);
    }
    public function customFieldsInContacts(){
        return $this->hasMany(CustomField::class)->where('route', CustomField::ROUTE_CONTACTS);
    }
    public function customFieldsInCustom(){
        return $this->hasMany(CustomField::class)->where('route', CustomField::ROUTE_CUSTOM);
    }
    public function customFieldsInDate(){
        return $this->hasMany(CustomField::class)->where('route', CustomField::ROUTE_DATE);
    }
    public function parent(){
        return $this->belongsTo(self::class, 'parent_id', 'id');
    }

    public function childs(){
        return $this->hasMany(self::class,'parent_id','id');
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

        // updating created_by and updated_by when model is created
        static::creating(static function ($model) {
            if (!$model->isDirty('created_by')) {
                $model->created_by = Arr::get(auth()->user(), 'id');
            }
            if (!$model->isDirty('updated_by')) {
                $model->updated_by = Arr::get(auth()->user(), 'id');
            }
        });

        // updating updated_by when model is updated
        static::updating(static function ($model) {
            if (!$model->isDirty('updated_by')) {
                $model->updated_by = Arr::get(auth()->user(), 'id');
            }
        });

    }

}
