<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\User\LoginService;
use Exception;
use Tests\TestCase;

class LoginServiceTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function test_customRegister()
    {
        $data = [
            'name' => 'Adminjon',
            'email' => "adminjonaka@gmail.com".rand(100,1000),
            'phone_number' =>  '+998123456789',
            'password' => '12345673245',
            'youtube_link'=>'test_user_123'
        ];
        (new LoginService)->customRegister($data);
        User::query()->where('youtube_link','test_user_123')->delete();
        $this->assertTrue(true);
    }

    /**
     * @throws Exception
     */
    public function test_verifyColum()
    {
        $user = User::find(1);
        $hash = '123456';
        LoginService::verifyColum('email', $user, $hash);
        $this->assertTrue(true);
    }

    /**
     * @throws Exception
     */
    public function test_verify_phone()
    {
        $code = 123456;
        $user = User::find(1);
        (new LoginService)->verify_phone($code, $user);
        $this->assertTrue(true);
    }
}
