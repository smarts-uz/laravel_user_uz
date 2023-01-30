<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use App\Models\WalletBalance;
use App\Services\User\SocialService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use RealRashid\SweetAlert\Facades\Alert;

class SocialController extends Controller
{
    //login with facebook
    public function facebookRedirect()
    {
        return Socialite::driver('facebook')->redirect();
    }

    // login with google
    public function googleRedirect()
    {
        return Socialite::driver('google')->redirect();
    }

    // login with apple
    public function appleRedirect()
    {

        return Socialite::driver('apple')->redirect();
    }

    public function loginWithFacebook()
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
            Auth::login($findUser);
            if (!($findUser->password)){
                /** @var Notification $notification */
                Notification::query()->create([
                    'user_id' => $findUser->id,
                    'description' => 'password',
                    'type' => Notification::NEW_PASSWORD,
                ]);
            }
        } else {
            $new_user = new User();
            $new_user->name = $user->name;
            $new_user->email = $user->email;
            $new_user->facebook_id = $user->id;
            $new_user->avatar = SocialService::get_avatar($user);
            $new_user->save();
            $wallBal = new WalletBalance();
            $wallBal->balance = setting('admin.bonus');
            $wallBal->user_id = $new_user->id;
            $wallBal->save();
            /** @var Notification $notification */
            Notification::query()->create([
                'user_id' => $new_user->id,
                'description' => 'password',
                'type' => Notification::NEW_PASSWORD,
            ]);
            if(setting('admin.bonus')>0){
                Notification::query()->create([
                    'user_id' => $new_user->id,
                    'description' => 'wallet',
                    'type' => Notification::WALLET_BALANCE,
                ]);
            }
            Auth::login($new_user);
        }

        return redirect()->route('profile.profileData');
    }

    public function loginWithApple()
    {
        try {
            $user = Socialite::driver('apple')->setScopes(['name', 'email'])->user();

            /** @var User $findUser */
            $findUser = User::query()->where('email', $user->email)->first();
            if (!$user->email) {
                $findUser = User::query()->where('apple_id', $user->id)->first();
            }

            if ($findUser) {
                $findUser->apple_id = $user->id;
                $findUser->save();
                Auth::login($findUser);
                if (!($findUser->password)){
                    /** @var Notification $notification */
                    Notification::query()->create([
                        'user_id' => $findUser->id,
                        'description' => 'password',
                        'type' => Notification::NEW_PASSWORD,
                    ]);
                }
                Alert::success(__('Успешно'), __('Вы успешно связали свой аккаунт Google'));
            } else {
                $new_user = new User();
                $new_user->name = $user->name;
                $new_user->email = $user->email;
                $new_user->apple_id = $user->id;
                $new_user->is_email_verified = 1;
                $new_user->save();
                $wallBal = new WalletBalance();
                $wallBal->balance = setting('admin.bonus');
                $wallBal->user_id = $new_user->id;
                $wallBal->save();
                /** @var Notification $notification */
                Notification::query()->create([
                    'user_id' => $new_user->id,
                    'description' => 'password',
                    'type' => Notification::NEW_PASSWORD,
                ]);
                if(setting('admin.bonus')>0){
                    Notification::query()->create([
                        'user_id' => $new_user->id,
                        'description' => 'wallet',
                        'type' => Notification::WALLET_BALANCE,
                    ]);
                }
                Auth::login($new_user);
            }

            return redirect()->route('profile.profileData');
        } catch (Exception $e) {
            dd($e, 11);
        }
        return false;
    }

    public function loginWithGoogle()
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
                Auth::login($findUser);
                if (!($findUser->password)){
                    /** @var Notification $notification */
                    Notification::query()->create([
                        'user_id' => $findUser->id,
                        'description' => 'password',
                        'type' => Notification::NEW_PASSWORD,
                    ]);
                }
                Alert::success(__('Успешно'), __('Вы успешно связали свой аккаунт Google'));
            } else {
                $new_user = new User();
                $new_user->name = $user->name;
                $new_user->email = $user->email;
                $new_user->google_id = $user->id;
                $new_user->avatar = SocialService::get_avatar($user);
                $new_user->is_email_verified = 1;
                $new_user->save();
                $wallBal = new WalletBalance();
                $wallBal->balance = setting('admin.bonus');
                $wallBal->user_id = $new_user->id;
                $wallBal->save();
                /** @var Notification $notification */
                Notification::query()->create([
                    'user_id' => $new_user->id,
                    'description' => 'password',
                    'type' => Notification::NEW_PASSWORD,
                ]);
                if(setting('admin.bonus')>0){
                    Notification::query()->create([
                        'user_id' => $new_user->id,
                        'description' => 'wallet',
                        'type' => Notification::WALLET_BALANCE,
                    ]);
                }
                Auth::login($new_user);
            }

            return redirect()->route('profile.profileData');
        } catch (Exception $e) {
            // Log to File
        }
        return false;
    }
}
