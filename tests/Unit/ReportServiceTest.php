<?php

namespace Tests\Unit;

use App\Services\ReportService;
use Exception;
use Tests\TestCase;

class ReportServiceTest extends TestCase
{
    public function test_report()
    {
        (new ReportService)->report();
        $this->assertTrue(true);
    }

    /**
     * @throws Exception
     */
    public function test_child_report()
    {
        $child_id = 31;
        (new ReportService)->child_report($child_id);
        $this->assertTrue(true);
    }
}
