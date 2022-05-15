<?php


namespace App\Services;

use Illuminate\Database\Eloquent\Collection;
use App\Events\MyEvent;
use App\Item\ControllerItem;
use App\Item\SearchServiceTaskItem;
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
    public function home($id)
    {
        $item = new ControllerItem();
        $item -> categories = Category::withTranslations(['ru', 'uz'])->where('parent_id', null)->get();
        $item -> tasks  =  Task::where('status', 1)->orWhere('status',2)->orderBy('id', 'desc')->take(20)->get();
        $item -> child_categories = Category::withTranslations(['ru', 'uz'])->where('parent_id','!=',null)->get();
        return $item;

    }
    public function category($id){
        $item = new SearchServiceTaskItem();
        $item -> categories = Category::withTranslations(['ru', 'uz'])->where('parent_id', null)->get();
        $item -> choosed_category = Category::withTranslations(['ru', 'uz'])->where('id', $id)->get();
        $item -> child_categories = Category::withTranslations(['ru', 'uz'])->where('parent_id', $id)->get();
        $item -> idR = $id;
        return $item;
    }
    public function my_tasks(){
        $item = new MyTaskItem();
        $item->user = auth()->user();
        $item->tasks = $item ->user->tasks()->orderBy('created_at', 'desc')->get();
        $item->perform_tasks = $item ->user->performer_tasks()->orderBy('created_at', 'desc')->get();
        $item->categories = Category::where('parent_id', null)->select('id', 'name', 'slug')->get();
        $item->categories2 = Category::where('parent_id', '<>', null)->select('id', 'parent_id', 'name','ico')->get();
        return $item;
    }
}
