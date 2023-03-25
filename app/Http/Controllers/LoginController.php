<?php

namespace App\Http\Controllers;

use App\Http\Requests\{ModalNumberRequest, ResetCodeRequest, UserLoginRequest, UserRegisterRequest};
use App\Models\User;
use App\Services\CustomService;
use App\Services\User\LoginService;
use App\Services\VerificationService;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
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

    public function login(): Factory|View|Application
    {
        return view('auth.signin');
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function loginPost(UserLoginRequest $request): Redirector|Application|RedirectResponse
    {
        $data = $request->validated();
        $email =  $data['email'];
        $password = $data['password'];
        $session = $request->session();
        return $this->loginService->login($email, $password, $session);

    }

    /**
     * @throws Exception
     */
    public function customRegister(UserRegisterRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $this->loginService->customRegister($data);
        return redirect()->route('profile.profileData');
    }


    /**
     * @throws Exception
     */
    public function send_email_verification(): RedirectResponse
    {
        VerificationService::send_verification('email', auth()->user());
        Alert::info(__('Ваша ссылка для подтверждения успешно отправлена!'));
        return redirect()->route('profile.profileData');
    }

    /**
     * @throws Exception
     */
    public function send_phone_verification(): RedirectResponse
    {
        /** @var User $user */
        $user = auth()->user();
        $phone_number = (new CustomService)->correctPhoneNumber($user->phone_number);
        VerificationService::send_verification('phone', $user, $phone_number);
        return redirect()->back()->with([
            'code' => __('Код отправлен!')
        ]);
    }

    /**
     * @throws Exception
     */
    public function verifyAccount(User $user, $hash): RedirectResponse
    {
        LoginService::verifyColum('email', $user, $hash);
        auth()->login($user);
        Alert::success(__('Поздравляю'), __('Ваш адрес электронной почты успешно подтвержден'));
        return redirect()->route('profile.profileData');

    }

    /**
     * @throws Exception
     */
    public function verify_phone(ResetCodeRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $code = $data['code'];
        $user = auth()->user();
        return $this->loginService->verify_phone($code, $user);
    }

    /**
     * @throws Exception
     */
    public function change_email(Request $request): RedirectResponse
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

    /**
     * @throws Exception
     */
    public function change_phone_number(ModalNumberRequest $request): RedirectResponse
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

    public function logout(): Redirector|Application|RedirectResponse
    {
        Auth::logout();
        return redirect('/');
    }
}
