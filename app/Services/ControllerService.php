<?php


namespace App\Services;


use App\Events\MyEvent;
use App\Item\ControllerItem;
use App\Item\CategoryItem;
use App\Item\MyTaskItem;
use App\Models\Task;
use Illuminate\Support\Facades\Http;
use TCG\Voyager\Models\Category;

class ControllerService
{

    /**
     *
     * Function  home
     * @link https://user.uz/
     * @return  ControllerItem
     */
    public function home()
    {
        $item = new ControllerItem();
        $item -> categories = Category::withTranslations(['ru', 'uz'])->where('parent_id', null)->get();
        $item -> tasks  =  Task::where('status', 1)->orWhere('status',2)->orderBy('id', 'desc')->take(20)->get();
        return $item;

    }
    public function category($id){
        $item = new CategoryItem();
        $item -> categories = Category::withTranslations(['ru', 'uz'])->where('parent_id', null)->get();
        $item -> choosed_category = Category::withTranslations(['ru', 'uz'])->where('id', $id)->get();
        $item -> child_categories = Category::withTranslations(['ru', 'uz'])->where('parent_id', $id)->get();
        $item -> idR = $id;
        return $item;
    }
}
