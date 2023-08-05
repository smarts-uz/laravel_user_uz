<?php

namespace App\Http\Controllers;

use App\Services\User\SocialService;
use Illuminate\Http\RedirectResponse;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse as RedirectResponseAlias;

class SocialController extends Controller
{
    protected SocialService $social_service;

    public function __construct()
    {
        $this->social_service = new SocialService();
    }


    //login with facebook
    public function facebookRedirect(): RedirectResponseAlias|RedirectResponse
    {
        return Socialite::driver('facebook')->redirect();
    }

    // login with google
    public function googleRedirect(): RedirectResponseAlias|RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    // login with apple
    public function appleRedirect(): RedirectResponseAlias|RedirectResponse
    {
        return Socialite::driver('apple')->redirect();
    }

    public function loginWithFacebook(): bool|RedirectResponse
    {
        return $this->social_service->loginFacebook();
    }

    public function loginWithGoogle(): bool|RedirectResponse
    {
        return $this->social_service->loginGoogle();
    }

    public function loginWithApple(): bool|RedirectResponse
    {
        return $this->social_service->loginApple();
    }
}
