<?php

namespace App\Http\Controllers;

use App\Http\Requests\ModalNumberRequest;
use App\Http\Requests\ResetCodeRequest;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Models\User;
use App\Services\CustomService;
use App\Services\User\LoginService;
use App\Services\VerificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use RealRashid\SweetAlert\Facades\Alert;

class LoginController extends Controller
{
    protected LoginService $loginService;

    public function __construct()
    {
        $this->loginService = new LoginService();
    }

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
        $email =  $data['email'];
        $password = $data['password'];
        $session = $request->session();
        $authUser = auth()->user();
        return $this->loginService->login($email, $password, $session, $authUser);

    }

    /**
     * @throws \Exception
     */
    public function customRegister(UserRegisterRequest $request)
    {
        $data = $request->validated();
        $this->loginService->customRegister($data);
        return redirect()->route('profile.profileData');
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
        $phone_number = (new CustomService)->correctPhoneNumber($user->phone_number);
        VerificationService::send_verification('phone', $user, $phone_number);
        return redirect()->back()->with([
            'code' => __('Код отправлен!')
        ]);
    }

    public function verifyAccount(User $user, $hash)
    {
        LoginService::verifyColum('email', $user, $hash);
        auth()->login($user);
        Alert::success(__('Поздравляю'), __('Ваш адрес электронной почты успешно подтвержден'));
        return redirect()->route('profile.profileData');

    }

    public function verify_phone(ResetCodeRequest $request)
    {
        $data = $request->validated();
        $code = $data['code'];
        $user = auth()->user();
        return $this->loginService->verify_phone($code, $user);
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
        }

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

    public function change_phone_number(ModalNumberRequest $request)
    {
        /** @var User $user */
        $user = auth()->user();
        $phone_number = (new CustomService)->correctPhoneNumber($user->phone_number);
        if ($request->get('phone_number') === $phone_number) {
            return back()->with([
                'email-message' => 'Your phone',
                'email' => $request->get('email')
            ]);
        }

        $request->validated();

        $user->phone_number = $request->get('phone_number');
        $user->save();
        VerificationService::send_verification('phone', $user, $phone_number);

        return redirect()->back()->with([
            'code' => __('Код отправлен!')
        ]);
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }
}
