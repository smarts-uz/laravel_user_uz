<?php


namespace App\Services;

use App\Item\ControllerItem;
use App\Item\SearchServiceTaskItem;
use App\Item\MyTaskItem;
use App\Models\Task;
use TCG\Voyager\Models\Category;

class ControllerService
{
    public const MAX_HOMEPAGE_TASK = 20;
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
        $item -> tasks  =  Task::query()->where('status', Task::STATUS_OPEN)->orWhere('status',Task::STATUS_RESPONSE)->orderBy('id', 'desc')->take(self::MAX_HOMEPAGE_TASK)->get();
        $item -> child_categories = Category::withTranslations(['ru', 'uz'])->where('parent_id','!=',null)->get();
        return $item;

    }
    /**
     *
     * Function  category
     * Mazkur metod barcha kategoriyalarni chiqarib beradi
     * @param $id
     * @return  SearchServiceTaskItem
     */
    public function category($id){
        $item = new SearchServiceTaskItem();
        $item -> categories = Category::withTranslations(['ru', 'uz'])->where('parent_id', null)->get();
        $item -> choosed_category = Category::withTranslations(['ru', 'uz'])->where('id', $id)->get();
        $item -> child_categories = Category::withTranslations(['ru', 'uz'])->where('parent_id', $id)->get();
        $item -> idR = $id;
        return $item;
    }
    /**
     *
     * Function  my_tasks
     * Mazkur metod my_task sahifasini ochib beradi
     * @param   Object
     * @return  MyTaskItem
     */
    public function my_tasks(){
        $item = new MyTaskItem();
        $item->user = auth()->user();
        $item->tasks = $item->user->tasks()->whereIn('status', [Task::STATUS_OPEN, Task::STATUS_RESPONSE, Task::STATUS_IN_PROGRESS, Task::STATUS_COMPLETE, Task::STATUS_NOT_COMPLETED, Task::STATUS_CANCELLED])->orderBy('created_at', 'desc')->get();
        $item->perform_tasks = $item->user->performer_tasks()->orderBy('created_at', 'desc')->get();
        $item->categories = Category::query()->where('parent_id', null)->select('id', 'name', 'slug')->get();
        $item->categories2 = Category::query()->where('parent_id', '<>', null)->select('id', 'parent_id', 'name','ico')->get();
        return $item;
    }
}
