<?php

namespace App\Services\Task;


use App\Models\Category;

class CategoriesAPIService
{
    public function index() {
        $categories = Category::query()->select('id', 'parent_id', 'name', 'ico')->withTranslation(app()->getLocale())->whereNull('parent_id')->orderBy("order", "asc")->get();
        return $this->category($categories);
    }
    public function show($category_id, $lang) {
        $category = Category::select('parent_id','name', 'ico', 'max', 'min', 'double_address')
        ->withTranslation($lang)->find($category_id);
        $data = (!empty($category)) ? [
            'id' => $category_id,
            'parent_id' => $category->parent_id,
            'name' => $category->name,
            'ico' => asset('storage/' . $category->ico),
            'max' => $category->max,
            'min' => $category->min,
            'isDoubleAddress' => $category->double_address
        ] : [];
        return ['data' => $data];
    }

    public function parents() {
        $categories = Category::query()->whereNull('parent_id')->orderBy("order")->get();
        return $this->category($categories);
    }

    public function search($parentId, $name) {

        $categories = Category::query()->whereNotNull('parent_id')->orderBy("order");
        if ($parentId) {
            $categories->where('parent_id', $parentId);
        }
        if ($name) {
            $categories->where('name', 'LIKE', "%$name%");
        }
        return $this->category($categories->get());
    }

    private function category($categories) {
        $data = [];
        foreach ($categories as $category) {
            $data[] = (!empty($categories)) ? [
                'id' => $category->id,
                'parent_id' => $category->parent_id,
                'name' => $category->getTranslatedAttribute('name'),
                'child_count' =>$category->childs()->count(),
                'ico' => asset('storage/' . $category->ico),
            ] : [];
        }

        return ['data' => $data];
    }

    public function popular($name): array
    {
        $categories = Category::query()->select('id', 'parent_id', 'name', 'ico')->withCount('tasks')->withTranslation('uz')
            ->whereTranslation('name', 'like', "%$name%")->orWhere('name', 'like', "%$name%")->whereNotNull('parent_id')->orderByDesc('tasks_count')->get();
        $response = [];
        if (!empty($categories)) {
            foreach ($categories as $category) {
                $object['category_id'] = (!empty($category->id)) ? $category->id : '';
                (int)$object['total'] = (!empty($category->tasks_count)) ? $category->tasks_count : '';
                $category->name = (!empty($category->name)) ? $category->getTranslatedAttribute('name', app()->getLocale(), 'ru') : '';
                unset($category->translations);
                $object['category'] = $category;
                $response[] = $object;
            }
        }
        return $response;
    }

    public function AllCategoriesChildsId(): array
    {
        $data = Category::query()->where('parent_id','!=',null)->pluck('id')->toArray();
        return ['data' => $data];
    }
}
