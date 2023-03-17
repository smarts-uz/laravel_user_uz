<?php

namespace App\Services\User;

use App\Http\Resources\PerformerIndexResource;
use App\Models\Notification;
use App\Models\User;
use App\Models\WalletBalance;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Laravel\Socialite\Facades\Socialite;

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
                'user' => new PerformerIndexResource(auth()->user()),
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
     * @param $new_user
     * @return void
     */
    public function social_wallet($new_user): void
    {
        $wallBal = new WalletBalance();
        $wallBal->balance = setting('admin.bonus',0);
        $wallBal->user_id = $new_user->id;
        $wallBal->save();
        /** @var Notification $notification */
        Notification::query()->create([
            'user_id' => $new_user->id,
            'description' => 'password',
            'type' => Notification::NEW_PASSWORD,
        ]);
        if(setting('admin.bonus',0)>0){
            Notification::query()->create([
                'user_id' => $new_user->id,
                'description' => 'wallet',
                'type' => Notification::WALLET_BALANCE,
            ]);
        }
        Auth::login($new_user);
    }

}
