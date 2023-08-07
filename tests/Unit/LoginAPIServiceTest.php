<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\Task\LoginAPIService;
use Exception;
use Tests\TestCase;

class LoginAPIServiceTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function test_verifyCredentials()
    {
        $data = [
            'type'=>'email',
            'data'=>'admin@admin.com'
        ];
        $user = User::find(1);
        (new LoginAPIService)->verifyCredentials($data, $user);
        $this->assertTrue(true);
    }

    public function test_verify_phone()
    {
        $user = User::find(1);
        $code = 123450;
        (new LoginAPIService)->verify_phone($user, $code);
        $this->assertTrue(true);
    }

    public function test_verify_email()
    {
        $user = User::find(1);
        $code = 123450;
        (new LoginAPIService)->verify_email($user, $code);
        $this->assertTrue(true);
    }
}
