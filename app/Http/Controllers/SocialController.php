<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\User\SocialService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Laravel\Socialite\Facades\Socialite;
use RealRashid\SweetAlert\Facades\Alert;
use Symfony\Component\HttpFoundation\RedirectResponse as RedirectResponseAlias;

class SocialController extends Controller
{
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

    public function loginWithFacebook(): RedirectResponse
    {
        $user = Socialite::driver('facebook')->setScopes(['name','email'])->user();
        /** @var User $findUser */
        $findUser = User::query()->where('email', $user->email)->first();

        if (!$user->email) {
            $findUser = User::query()->where('facebook_id', $user->id)->first();
        }
        if ($findUser) {
            $findUser->facebook_id = $user->id;
            $findUser->save();
            Alert::success(__('Успешно'), __('Вы успешно связали свой аккаунт Facebook'));
            (new SocialService)->password_notif($findUser);
        } else {
            $new_user = new User();
            $new_user->name = $user->name;
            $new_user->email = $user->email;
            $new_user->facebook_id = $user->id;
            $new_user->avatar = SocialService::get_avatar($user);
            $new_user->save();
            (new SocialService)->social_wallet($new_user);
        }

        return redirect()->route('profile.profileData');
    }

    public function loginWithGoogle(): bool|RedirectResponse
    {
        try {
            $user = Socialite::driver('google')->setScopes(['openid', 'email'])->user();

            /** @var User $findUser */
            $findUser = User::query()->where('email', $user->email)->first();

            if (!$user->email) {
                $findUser = User::query()->where('google_id', $user->id)->first();
            }
            if ($findUser) {
                $findUser->google_id = $user->id;
                $findUser->save();
                Alert::success(__('Успешно'), __('Вы успешно связали свой аккаунт Google'));
                (new SocialService)->password_notif($findUser);
            } else {
                $new_user = new User();
                $new_user->name = $user->name;
                $new_user->email = $user->email;
                $new_user->google_id = $user->id;
                $new_user->avatar = SocialService::get_avatar($user);
                $new_user->is_email_verified = 1;
                $new_user->save();
                (new SocialService)->social_wallet($new_user);
            }

            return redirect()->route('profile.profileData');
        } catch (Exception $e) {
            // Log to File
        }
        return false;
    }

    public function loginWithApple(): bool|RedirectResponse
    {
        try {
            $user = Socialite::driver('apple')->user();

            /** @var User $findUser */
            $findUser = User::query()->where('email', $user->email)->first();
            if (!$user->email) {
                $findUser = User::query()->where('apple_id', $user->id)->first();
            }

            if ($findUser) {
                $findUser->apple_id = $user->id;
                $findUser->save();
                Alert::success(__('Успешно'), __('Вы успешно связали свой аккаунт Google'));
                (new SocialService)->password_notif($findUser);
            } else {
                $new_user = new User();
                $new_user->name = $user->name ?? SocialService::emailToName($user->email);
                $new_user->email = $user->email;
                $new_user->apple_id = $user->id;
                $new_user->is_email_verified = 1;
                $new_user->save();
                (new SocialService)->social_wallet($new_user);
            }

            return redirect()->route('profile.profileData');
        } catch (Exception $e) {
            // Log to File
        }
        return false;
    }
}
