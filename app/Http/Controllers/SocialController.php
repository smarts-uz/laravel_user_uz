<?php

namespace App\Http\Controllers;

use App\Services\User\SocialService;
use Exception;
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
        try {
            return $this->social_service->loginFacebook();
        }catch (Exception $e) {
            // Log to File
        }
        return false;

    }

    public function loginWithGoogle(): bool|RedirectResponse
    {
        try {
            return $this->social_service->loginGoogle();
        } catch (Exception $e) {
            // Log to File
        }
        return false;
    }

    public function loginWithApple(): bool|RedirectResponse
    {
        try {
            return $this->social_service->loginApple();
        } catch (Exception $e) {
            // Log to File
        }
        return false;
    }
}
