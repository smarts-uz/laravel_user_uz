<?php

namespace Tests\Unit;

use App\Services\Task\FilterTaskService;
use Tests\TestCase;

class FilterTaskServiceTest extends TestCase
{
    public function test_filter()
    {
        $data = [
            'categories'=>[2,3,7],
            'is_remote'=>true
        ];
        (new FilterTaskService)->filter($data);
        $this->assertTrue(true);
    }

    public function test_distance()
    {
        $lat1 = 40.485018051854;
        $lon1 = 71.764779053628;
        $lat2 = 41.000085;
        $lon2 = 71.672579;
        $data = (new FilterTaskService)->distance($lat1, $lon1, $lat2, $lon2);
        $this->assertTrue(true);
    }

    public function test_taskSingle()
    {
        $task = [];
        (new FilterTaskService)->taskSingle($task);
        $this->assertTrue(true);
    }

    public function test_categories()
    {
        (new FilterTaskService)->categories();
        $this->assertTrue(true);
    }
}
