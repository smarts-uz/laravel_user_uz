<?php

namespace Tests\Unit;

use App\Models\Notification;
use App\Models\User;
use App\Models\WalletBalance;
use App\Services\User\SocialService;
use Tests\TestCase;

class SocialServiceTest extends TestCase
{
    public function test_login()
    {
        $provider = 'google';
        $token = 'ewfgrthyt';
        SocialService::login($provider, $token);
        $this->assertTrue(true);
    }

    public function test_password_notif()
    {
        $user = User::find(1);
        (new SocialService)->password_notif($user);
        $this->assertTrue(true);
    }

    public function test_social_wallet()
    {
        $userId = 12345678;
        (new SocialService)->social_wallet($userId);
        WalletBalance::query()->where('user_id',$userId)->delete();
        Notification::query()
            ->where('type',Notification::WALLET_BALANCE)
            ->where('user_id',$userId)
            ->delete();
        Notification::query()
            ->where('type',Notification::NEW_PASSWORD)
            ->where('user_id',$userId)
            ->delete();
        $this->assertTrue(true);
    }

    public function test_loginFacebook()
    {
        (new SocialService)->loginFacebook();
        $this->assertTrue(true);
    }

    public function test_loginGoogle()
    {
        (new SocialService)->loginGoogle();
        $this->assertTrue(true);
    }

    public function test_loginApple()
    {
        (new SocialService)->loginApple();
        $this->assertTrue(true);
    }
}
