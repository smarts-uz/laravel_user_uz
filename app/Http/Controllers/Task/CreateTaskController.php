<?php

namespace App\Http\Controllers\Task;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use TCG\Voyager\Http\Controllers\VoyagerBaseController;
use TCG\Voyager\Models\Category;

class CreateTaskController extends VoyagerBaseController
{
    //

    public function task_create(Request $request){

        $current_category = Category::find($request->category_id);
        if (!$current_category){
            return back();
        }
<<<<<<< HEAD
        // $task = new Task();
        // $task->id = $request->current_category;
        // $task->status = 0;
        // $task->save();

        return view("create.name", compact( 'current_category'));
=======


        $categories = Category::query()->where("parent_id", null)->get();
        $current_parent_category = Category::find($current_category->parent_id);
        $child_categories = Category::query()->where("parent_id", $current_parent_category->id)->get();


        return view("create.name", compact('categories', 'current_category','child_categories', 'current_parent_category'));
>>>>>>> 994997ab83d3ee5dbf2397e61846aca9fa6e3ee4
    }

    public function task_add(Request $request){

        $request->validate([
            'name'=>"required"
        ]);
<<<<<<< HEAD
        $name = $request->input('name');
        $cat_id = $request->input('cat_id');
        // $task = new Task();
        // $task->name = $request->name;
        // $task->status = 0;
        // $task->save();
        return view("create.location", compact('name','cat_id'));
=======

        $task = new Task();
        $task->name = $request->name;
        $task->status = 0;
        $task->save();
        session()->put('task', $task);
        session()->put('categroy_id', $request->category_id);
        return redirect()->route("task.create.address", $request->category_id);
>>>>>>> 994997ab83d3ee5dbf2397e61846aca9fa6e3ee4

    }

    public function location_create(){
        $task = session('task');
        $category = Category::find(session('categroy_id'));

        if ($task || $category){
            return view('create.location', compact('task', 'category'));
        }else{
            return back();
        }

    }

    public function custom(){
        return view('create.custom');
    }

    public function date(){
        return view('create.date');
    }
    public function budget(){
        return view('create.budget');
    }
    public function notes(){
        return view('create.notes');
    }
    public function contacts(){
        return view('create.contacts');
    }






    public function delete(Task $task){
        $task->delete();
        session()->forget('task');
        session()->forget('category');
        return redirect("/home");
    }


}
