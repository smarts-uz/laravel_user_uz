<?php

namespace Tests\Unit;

use App\Services\SmsMobileService;
use Tests\TestCase;

class SmsServiceTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_sms_service(): void
    {
        $phone = '';
        $message = 'Hello world';
        SmsMobileService::sms_packages($phone, $message);
        $this->assertTrue(true);
    }
}
