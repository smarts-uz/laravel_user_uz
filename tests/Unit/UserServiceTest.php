<?php

namespace Tests\Unit;

use App\Models\Notification;
use App\Models\User;
use App\Models\WalletBalance;
use App\Services\User\UserService;
use Exception;
use JsonException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
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

    public function test_register_api_service()
    {
        $data = [
            'name' => 'Adminjon',
            'email' => "adminjonaka@gmail.com".rand(100,1000),
            'phone_number' =>  '+998123456789',
            'password' => '12345673245',
            'youtube_link'=>'test_user_123'
        ];
        (new UserService)->register_api_service($data);
        User::query()->where('youtube_link','test_user_123')->delete();
        $this->assertTrue(true);
    }

    public function test_getSupportId()
    {
        (new UserService)->getSupportId();
        $this->assertTrue(true);
    }

    public function test_logout()
    {
        $device_id = '4e9abed6116fb4c0';
        $user = User::find(1);
        (new UserService)->logout($device_id, $user);
        $this->assertTrue(true);
    }

    public function test_confirmationSelfDelete()
    {
        $code = 123456;
        $user = User::find(1);
        (new UserService)->confirmationSelfDelete($user, $code);
        $this->assertTrue(true);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws JsonException
     */
    public function test_verifyProfile()
    {
        $for_ver_func = 3033;
        $user = User::find(1);
        $sms_otp = 123456;
        (new UserService)->verifyProfile($for_ver_func, $user, $sms_otp);
        $this->assertTrue(true);
    }

    public function test_getTransactions()
    {
        $user = User::find(1);
        $payment = '';
        (new UserService)->getTransactions($user, $payment);
        $this->assertTrue(true);
    }

    public function test_new_user()
    {
        $userId = 12345678910;
        (new UserService)->new_user($userId);
        WalletBalance::query()->where('user_id',$userId)->delete();
        Notification::query()
            ->where('type',Notification::WALLET_BALANCE)
            ->where('user_id',$userId)
            ->delete();
        $this->assertTrue(true);
    }

    public function test_sessionIndex()
    {
        $userId = 1;
        (new UserService)->sessionIndex($userId);
        $this->assertTrue(true);
    }

    public function test_clearSessions()
    {
        $session_id = '02jF0F01vXqgknAH5pV2wK1vp1Tysaqv8D4NOnwU';
        $user = User::find(1);
        (new UserService)->clearSessions($user, $session_id);
        $this->assertTrue(true);
    }

    public function test_resetPassword_store()
    {
        $data = [
            'password' => '123'
        ];
        $user = User::find(1);
        (new UserService)->resetPassword_store($data, $user);
        $this->assertTrue(true);
    }

    public function test_activity()
    {
        $user = User::find(1);
        $authUser = User::find(1662);
        (new UserService)->activity($user, $authUser);
        $this->assertTrue(true);
    }

    public function test_delete_code()
    {
        (new UserService)->access_tokens();
        $this->assertTrue(true);
    }


}
