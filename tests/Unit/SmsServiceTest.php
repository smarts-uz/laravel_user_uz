<?php

namespace Tests\Unit;

use App\Services\SmsMobileService;
use PHPUnit\Framework\TestCase;

class SmsServiceTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_sms_service(): void
    {
        $phone = '+998915480312';
        $message = 'Hello world';
        SmsMobileService::sms_packages($phone, $message);
        $this->assertTrue(true);
    }
}
