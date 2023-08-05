<?php

namespace Tests\Unit;

use App\Services\Task\CategoriesAPIService;
use Tests\TestCase;

class CategoriesAPIServiceTest extends TestCase
{
    public function test_show()
    {
        $category_id = 22;
        (new CategoriesAPIService)->show($category_id);
        $this->assertTrue(true);
    }

    public function test_parents()
    {
        (new CategoriesAPIService)->parents();
        $this->assertTrue(true);
    }

    public function test_search()
    {
        $parentId = 1;
        $name = 'Курьерские';
        (new CategoriesAPIService)->search($parentId, $name);
        $this->assertTrue(true);
    }

    public function test_category()
    {
        $categories = [];
        (new CategoriesAPIService)->category($categories);
        $this->assertTrue(true);
    }

    public function test_popular()
    {
        $name = 'Дизайн';
        (new CategoriesAPIService)->popular($name);
        $this->assertTrue(true);
    }

    public function test_AllCategoriesChildsId()
    {
        (new CategoriesAPIService)->AllCategoriesChildsId();
        $this->assertTrue(true);
    }
}
