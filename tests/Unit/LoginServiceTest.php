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
            'email' => 'adminjonaka@gnail.com',
            'phone_number' =>  '+998123456789',
            'password' => '123456789',
        ];
        (new LoginService)->customRegister($data);
        User::query()->where('email','adminjonaka@gnail.com')->delete();
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
}
