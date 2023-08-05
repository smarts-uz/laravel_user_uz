<?php

namespace App\Services\User;

use App\Models\Notification;
use App\Models\User;
use App\Models\WalletBalance;
use App\Services\PerformersService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Laravel\Socialite\Facades\Socialite;
use RealRashid\SweetAlert\Facades\Alert;

class SocialService
{
    /**
     * Social login api
     * @param $provider
     * @param $token
     * @return JsonResponse
     */
    public static function login($provider, $token): JsonResponse
    {

        try {
            $providerUser = Socialite::driver($provider)->userFromToken($token);

            $query = User::query()
                ->where($provider . '_id', '=', $providerUser->id);

            if ($providerUser->email !== null) {
                $query->orWhere('email', '=', $providerUser->email);
            }

            $user = $query->first();

            // if there is no record with these data, create a new user
            if ($user === null) {
                $user = User::query()->create([
                    $provider . '_id' => $providerUser->id,
                    'name' => $providerUser->name ?? self::emailToName($providerUser->email),
                    'email' => $providerUser->email,
                    'is_email_verified' => 1,
                    'is_active' => 1,
                    'avatar' => $provider !== 'apple' ? self::get_avatar($providerUser) : null
                ]);
                $wallBal = new WalletBalance();
                $wallBal->balance = setting('admin.bonus',0);
                $wallBal->user_id = $user->id;
                $wallBal->save();
            }
            if (!$user->isActive()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Аккаунт отключен'),
                ]);
            }

            $user->update([$provider . '_id' => $providerUser->id]);

            // create a token for the user, so they can login
            Auth::login($user);
            $accessToken = $user->createToken('authToken')->accessToken;
            // return the token for usage
            return response()->json([
                'user' => (new PerformersService)->performerData(auth()->user()),
                'access_token' => $accessToken,
                'socialpas' => $user->has_password
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'data' => $e->getMessage(),
                'message' => "Record not found"
            ]);
        }
    }

    /**
     * social loginda user avatarini qaytaradi
     * @param $user
     * @return string
     */
    public static function get_avatar($user): string
    {
        $fileContents = file_get_contents($user->getAvatar());
        File::put(public_path() . '/storage/user-avatar/' . $user->getId() . ".jpg", $fileContents);
        return 'user-avatar/' . $user->getId() . ".jpg";
    }

    /**
     * @param $email
     * @return string
     */
    public static function emailToName($email): string
    {
        $name = explode('@', $email);
        return ucfirst(Arr::get($name, '0'));
    }

    /**
     * Social loginda password bo'lmasa notification create qilish
     * @param $findUser
     * @return void
     */
    public function password_notif($findUser): void
    {
        Auth::login($findUser);
        if (!($findUser->password)){
            /** @var Notification $notification */
            Notification::query()->create([
                'user_id' => $findUser->id,
                'description' => 'password',
                'type' => Notification::NEW_PASSWORD,
            ]);
        }
    }

    /**
     * Social orqali ro'yxatdan o'tgan userga balance berish va parol o'rnatish haqida bildirishnoma yuborish
     * @param $new_user_id
     * @return void
     */
    public function social_wallet($new_user_id): void
    {
        $wallBal = new WalletBalance();
        $wallBal->balance = setting('admin.bonus',0);
        $wallBal->user_id = $new_user_id;
        $wallBal->save();
        /** @var Notification $notification */
        Notification::query()->create([
            'user_id' => $new_user_id,
            'description' => 'password',
            'type' => Notification::NEW_PASSWORD,
        ]);
        if(setting('admin.bonus',0)>0){
            Notification::query()->create([
                'user_id' => $new_user_id,
                'description' => 'wallet',
                'type' => Notification::WALLET_BALANCE,
            ]);
        }
    }

    /**
     * facebook orqali login qilish(web)
     * @return false|RedirectResponse
     */
    public function loginFacebook(): bool|RedirectResponse
    {
        try {
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
                (new self)->password_notif($findUser);
            } else {
                $new_user = new User();
                $new_user->name = $user->name;
                $new_user->email = $user->email;
                $new_user->facebook_id = $user->id;
                $new_user->avatar = self::get_avatar($user);
                $new_user->save();
                (new self)->social_wallet($new_user->id);
                Auth::login($new_user);
            }
            return redirect()->route('profile.profileData');
        }catch (Exception $e) {
            // Log to File
        }
        return false;

    }

    /**
     * google orqali login qilish(web)
     * @return false|RedirectResponse
     */
    public function loginGoogle(): bool|RedirectResponse
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
                (new self)->password_notif($findUser);
            } else {
                $new_user = new User();
                $new_user->name = $user->name;
                $new_user->email = $user->email;
                $new_user->google_id = $user->id;
                $new_user->avatar = self::get_avatar($user);
                $new_user->is_email_verified = 1;
                $new_user->save();
                (new self)->social_wallet($new_user->id);
                Auth::login($new_user);
            }

            return redirect()->route('profile.profileData');
        } catch (Exception $e) {
            // Log to File
        }
        return false;
    }

    /**
     * apple orqali login qilish(web)
     * @return false|RedirectResponse
     */
    public function loginApple(): bool|RedirectResponse
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
                (new self)->password_notif($findUser);
            } else {
                $new_user = new User();
                $new_user->name = $user->name ?? self::emailToName($user->email);
                $new_user->email = $user->email;
                $new_user->apple_id = $user->id;
                $new_user->is_email_verified = 1;
                $new_user->save();
                (new self)->social_wallet($new_user->id);
                Auth::login($new_user);
            }

            return redirect()->route('profile.profileData');
        } catch (Exception $e) {
            // Log to File
        }
        return false;
    }
}
