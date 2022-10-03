<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
 */

class Category extends Model
{
    use HasFactory;
    use Translatable;
    protected array $translatable = ['name'];

    protected $table = "categories";
    protected $withCount = ['tasks'];

    public function tasks()
    {
        return $this->hasMany(Task::class);

    }

    public function getIcoAttribute($value)
    {
        if($value == null){
            $parentCategory = Category::find($this->parent_id);
            return $parentCategory->ico;
        }
        return ucfirst($value);
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
        return $this->belongsTo(Category::class, 'parent_id', 'id');
    }

    public function childs(){
        return $this->hasMany(Category::class,'parent_id','id');
    }


}
