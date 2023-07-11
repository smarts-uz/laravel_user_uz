<?php


namespace Tests\Unit;

use App\Models\User;
use App\Services\PerformersService;
use Codeception\Test\Unit;
use Tests\Support\UnitTester;

class LoginServiceTest extends Unit
{

    protected UnitTester $tester;

    protected function _before()
    {
    }

    // tests
    public function test_performer()
    {
        $this->assertTrue(true);
    }
}
