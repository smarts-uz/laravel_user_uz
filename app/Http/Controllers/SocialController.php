<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use App\Models\WalletBalance;
use App\Services\NotificationService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Laravel\Socialite\Facades\Socialite;
use RealRashid\SweetAlert\Facades\Alert;

class SocialController extends Controller
{
    //login with facebook
    public function facebookRedirect()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function loginWithFacebook()
    {
        $user = Socialite::driver('facebook')->setScopes(['email'])->user();
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
        } else {
            $new_user = new User();
            $new_user->name = $user->name;
            $new_user->email = $user->email;
            $new_user->facebook_id = $user->id;
            $new_user->avatar = self::get_avatar($user);
            $new_user->save();
            $wallBal = new WalletBalance();
            $wallBal->balance = setting('admin.bonus');
            $wallBal->user_id = $new_user->id;
            $wallBal->save();
            Auth::login($new_user);
        }
        return redirect()->route('profile.profileData');
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
                Alert::success(__('Успешно'), __('Вы успешно связали свой аккаунт Google'));
            } else {
                $new_user = new User();
                $new_user->name = $user->name;
                $new_user->email = $user->email;
                $new_user->apple_id = $user->id;
//                $new_user->avatar = self::get_avatar($user);
                $new_user->is_email_verified = 1;
                $new_user->save();
                $wallBal = new WalletBalance();
                $wallBal->balance = setting('admin.bonus');
                $wallBal->user_id = $new_user->id;
                $wallBal->save();
                Auth::login($new_user);
            }
            return redirect()->route('profile.profileData');
        } catch (Exception $e) {
            dd($e, 11);
        }
        return false;
    }


    private static function get_avatar($user)
    {
        $fileContents = file_get_contents($user->getAvatar());
        File::put(public_path() . '/storage/users-avatar/' . $user->getId() . ".jpg", $fileContents);
        $picture = 'users-avatar/' . $user->getId() . ".jpg";
        return $picture;
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
                Alert::success(__('Успешно'), __('Вы успешно связали свой аккаунт Google'));
            } else {
                $new_user = new User();
                $new_user->name = $user->name;
                $new_user->email = $user->email;
                $new_user->google_id = $user->id;
                $new_user->avatar = self::get_avatar($user);
                $new_user->is_email_verified = 1;
                $new_user->save();
                $wallBal = new WalletBalance();
                $wallBal->balance = setting('admin.bonus');
                $wallBal->user_id = $new_user->id;
                $wallBal->save();
                Auth::login($new_user);
            }
            if ($findUser->password===null){
                /** @var Notification $notification */
                $notification = Notification::query()->create([
                    'user_id' => $findUser->id,
                    'description' => 'password',
                    'type' => Notification::NEW_PASSWORD,
                ]);
                NotificationService::sendNotificationRequest([$findUser->id], [
                    'created_date' => $notification->created_at->format('d M'),
                    'title' => NotificationService::titles($notification->type),
                    'url' => route('show_notification', [$notification]),
                    'description' => NotificationService::descriptions($notification)
                ]);
            }
            return redirect()->route('profile.profileData');
        } catch (Exception) {
            // Log to File
        }
        return false;
    }
}
