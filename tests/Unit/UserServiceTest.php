<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\User\UserService;
use Exception;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function test_reset_submit()
    {
        $phone_number = '+998945480514';
        (new UserService)->reset_submit($phone_number);
        $this->assertTrue(true);
    }

    /**
     * @throws Exception
     */
    public function test_reset_by_email()
    {
        $email = 'admin@admin.com';
        (new UserService)->reset_by_email($email);
        $this->assertTrue(true);
    }

    public function test_login_api_service()
    {
        $user = User::find(1);
        (new UserService)->login_api_service($user);
        $this->assertTrue(true);
    }

    /**
     * @throws Exception
     */
    public function test_reset_submit_api()
    {
        $phone_number = '998879799484';
        (new UserService)->reset_submit_api($phone_number);
        $this->assertTrue(true);
    }

    public function test_reset_save()
    {
        $phone_number = '998879799484';
        $password = '$$@@admin@@$$';
        (new UserService)->reset_save($phone_number, $password);
        $this->assertTrue(true);
    }

    public function test_reset_code()
    {
        $phone_number = '998879799484';
        $code = 123456;
        (new UserService)->reset_code($phone_number, $code);
        $this->assertTrue(true);
    }


}
