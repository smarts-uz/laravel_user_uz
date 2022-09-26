<?php

namespace App\Http\Controllers;

use App\Http\Requests\ModalNumberRequest;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Models\User;
use App\Models\WalletBalance;
use App\Services\SmsMobileService;
use App\Services\VerificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use RealRashid\SweetAlert\Facades\Alert;

class LoginController extends Controller
{
    public function login()
    {
        return view('auth.signin');
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function loginPost(UserLoginRequest $request)
    {
        $data = $request->validated();
        /** @var User $user */
        $user = User::query()->where('email', $data['email'])
            ->orWhere('phone_number', $data['email'])
            ->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            Alert::error(__('Пароль неверен'));
            return back();
        }
        if (!$user->isActive()) {
            Alert::error(__('Аккаунт отключен'));
            return back();
        }

        auth()->login($user);
        if (!$user->is_email_verified)
            VerificationService::send_verification('email', auth()->user());

        $request->session()->regenerate();

        if (session()->has('redirectTo')) {
            $url = session()->get('redirectTo');
            session()->forget('redirectTo');
            return redirect($url);
        }
        return redirect()->intended('/profile');

    }


    public function customRegister(UserRegisterRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($request->get('password'));
        unset($data['password_confirmation']);
        /** @var User $user */
        $user = User::query()->create($data);
        $wallBal = new WalletBalance();
        $wallBal->balance = setting('admin.bonus');
        $wallBal->user_id = $user->id;
        $wallBal->save();
        auth()->login($user);

        VerificationService::send_verification('email', auth()->user());

        return redirect()->route('profile.profileData');

    }


    public static function send_verification_for_task_phone($task, $phone_number)
    {
        $message = rand(100000, 999999);

        $task->phone = $phone_number;
        $task->verify_code = $message;
        $task->verify_expiration = Carbon::now()->addMinutes(2);
        $task->save();

        SmsMobileService::sms_packages($phone_number,"USer.Uz ". __("Код подтверждения") . ' ' . $message);
    }

    public function send_email_verification()
    {
        VerificationService::send_verification('email', auth()->user());
        Alert::info(__('Ваша ссылка для подтверждения успешно отправлена!'));
        return redirect()->route('profile.profileData');
    }

    public function send_phone_verification()
    {
        /** @var User $user */
        $user = auth()->user();
        VerificationService::send_verification('phone', $user, $user->phone_number);
        return redirect()->back()->with([
            'code' => __('Код отправлен!')
        ]);
    }


    public static function verifyColum($needle, $user, $hash)
    {
        $needle = 'is_' . $needle . "_verified";

        $result = false;

        if (strtotime($user->verify_expiration) >= strtotime(Carbon::now())) {
            if ($hash === $user->verify_code || $hash === setting('admin.CONFIRM_CODE')) {
                $user->$needle = 1;
                $user->save();
                $result = true;
                if ($needle !== 'is_phone_number_verified' && !$user->is_phone_number_verified)
                    VerificationService::send_verification('phone', $user, $user->phone_number);
            }
        } else {
            abort(419);
        }
        return $result;
    }


    public function verifyAccount(User $user, $hash)
    {
        self::verifyColum('email', $user, $hash);
        auth()->login($user);
        Alert::success(__('Поздравляю'), __('Ваш адрес электронной почты успешно подтвержден'));
        return redirect()->route('profile.profileData');

    }

    public function verify_phone(Request $request)
    {
        $request->validate([
            'code' => 'required'
        ]);
        if (self::verifyColum('phone_number', auth()->user(), $request->code)) {
            Alert::success(__('Поздравляю'), __('Ваш телефон успешно подтвержден'));
            return redirect()->route('profile.profileData');
        } else {
            return back()->with([
                'code' => __('Неправильный код!')
            ]);

        }
    }

    public function change_email(Request $request)
    {
        /** @var User $user */
        $user = auth()->user();
        if ($request->get('email') === $user->email) {
            return back()->with([
                'email-message' => 'Your email',
                'email' => $request->get('email')
            ]);
        } else {
            $request->validate([
                'email' => 'required|unique:users|email'
            ], [
                'email.required' => __('login.email.required'),
                'email.email' => __('login.email.email'),
                'email.unique' => __('login.email.unique'),
            ]);
            $user->email = $request->get('email');
            $user->save();
            VerificationService::send_verification('email', $user);
            Alert::success(__('Поздравляю'), __('Ваш адрес электронной почты успешно изменен, и мы отправили ссылку для подтверждения на') . $user->email);
            return redirect()->back();
        }
    }

    public function change_phone_number(ModalNumberRequest $request)
    {
        /** @var User $user */
        $user = auth()->user();
        if ($request->get('phone_number') === $user->phone_number) {
            return back()->with([
                'email-message' => 'Your phone',
                'email' => $request->get('email')
            ]);
        } else {
            $request->validated();

            $user->phone_number = $request->get('phone_number');
            $user->save();
            VerificationService::send_verification('phone', $user, $user->phone_number);

            return redirect()->back()->with([
                'code' => __('Код отправлен!')
            ]);
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }
}
