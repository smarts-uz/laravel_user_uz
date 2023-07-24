<?php

namespace Tests\Unit;

use App\Services\ControllerService;
use Tests\TestCase;

class ControllerServiceTest extends TestCase
{
   public function test_home()
   {
       $lang = 'uz';
       (new ControllerService)->home($lang);
       $this->assertTrue(true);
   }

   public function test_category()
   {
       $category_id = 31;
       $lang = 'ru';
       (new ControllerService)->category($category_id, $lang);
       $this->assertTrue(true);
   }

   public function test_my_tasks()
   {
       $userId = 1662;
       $lang = 'ru';
       (new ControllerService)->my_tasks($userId, $lang);
       $this->assertTrue(true);
   }

   public function test_user_info()
   {
       $userId = 1662;
       (new ControllerService)->user_info($userId);
       $this->assertTrue(true);
   }
}
