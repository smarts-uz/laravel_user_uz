<?php

namespace App\Services\User;

use App\Models\Notification;
use App\Models\User;
use App\Models\WalletBalance;
use App\Services\CustomService;
use App\Services\VerificationService;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Hash;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use RealRashid\SweetAlert\Facades\Alert;

class LoginService
{

    /**
     * @param $email
     * @param $password
     * @param $session
     * @param $authUser
     * @return Redirector|RedirectResponse|Application
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function login($email, $password, $session, $authUser): Redirector|RedirectResponse|Application
    {
        /** @var User $user */
        $user = User::query()->where('email', $email)
            ->orWhere('phone_number', $email)
            ->first();

        if (!$user || !Hash::check($password, $user->password)) {
            Alert::error(__('Пароль неверен'));
            return back();
        }
        if (!$user->isActive()) {
            Alert::error(__('Аккаунт отключен'));
            return back();
        }

        auth()->login($user);
        if (!$user->is_email_verified && $user->email) {
            VerificationService::send_verification('email', $authUser);
        }

        $session->regenerate();

        if (session()->has('redirectTo')) {
            $url = session()->get('redirectTo');
            session()->forget('redirectTo');
            return redirect($url);
        }
        return redirect()->intended('/profile');
    }

    /**
     * @param $data
     * @return void
     * @throws \Exception
     */
    public function customRegister($data): void
    {
        $data['password'] = Hash::make($data['password']);
        unset($data['password_confirmation']);
        /** @var User $user */
        $user = User::query()->create($data);
        $user->phone_number = $data['phone_number'] . '_' . $user->id;
        $user->save();
        $wallBal = new WalletBalance();
        $wallBal->balance = setting('admin.bonus');
        $wallBal->user_id = $user->id;
        $wallBal->save();
        /** @var Notification $notification */
        if(setting('admin.bonus')>0){
            Notification::query()->create([
                'user_id' => $user->id,
                'description' => 'wallet',
                'type' => Notification::WALLET_BALANCE,
            ]);
        }
        auth()->login($user);

        VerificationService::send_verification('email', $user);
    }

    /**
     * @param $needle
     * @param $user
     * @param $hash
     * @return bool
     * @throws \Exception
     */
    public static function verifyColum($needle, $user, $hash): bool
    {
        $needle = 'is_' . $needle . "_verified";

        $result = false;

        if (strtotime($user->verify_expiration) >= strtotime(Carbon::now())) {
            if ($hash === $user->verify_code || $hash === setting('admin.CONFIRM_CODE')) {
                $user->$needle = 1;
                $user->save();
                $result = true;
                if ($needle !== 'is_phone_number_verified' && !$user->is_phone_number_verified) {
                    VerificationService::send_verification('phone', $user, (new CustomService)->correctPhoneNumber($user->phone_number));
                }
            }
        } else {
            abort(419);
        }
        return $result;
    }

    /**
     * @param $code
     * @param $user
     * @return RedirectResponse
     * @throws \Exception
     */
    public function verify_phone($code, $user): RedirectResponse
    {
        if (self::verifyColum('phone_number', $user, $code)) {
            $user->phone_number = (new CustomService)->correctPhoneNumber($user->phone_number);
            $user->save();
            Alert::success(__('Поздравляю'), __('Ваш телефон успешно подтвержден'));
            return redirect()->route('profile.profileData');
        }

        return back()->with([
            'code' => __('Неправильный код!')
        ]);
    }
}
