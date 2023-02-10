<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResetCodeRequest;
use App\Http\Requests\ResetEmailRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\ResetRequest;
use App\Http\Requests\VerifyProfileRequest;
use App\Models\User;
use App\Services\User\UserService;
use App\Services\VerificationService;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;


class UserController extends Controller
{
    public UserService $service;

    public function __construct(UserService $userService)
    {
        $this->service = $userService;
    }

    public function code(): Factory|View|Application
    {
        return view('auth.register_code');
    }

    public function signup(): Factory|View|Application
    {
        return view('auth.signup');
    }

    public function reset(): Factory|View|Application
    {
        return view('auth.reset');
    }

    public function confirm(): Factory|View|Application
    {
        return view('auth.confirm');
    }

    /**
     * @throws Exception
     */
    public function reset_submit(ResetRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $phone_number = $data['phone_number'];
        $this->service->reset_submit($phone_number);

        return redirect()->route('user.reset_code_view');
    }

    /**
     * @throws Exception
     */
    public function reset_by_email(ResetEmailRequest $request): RedirectResponse
    {

        $data = $request->validated();
        $email = $data['email'];
        $this->service->reset_by_email($email);

        return redirect()->route('user.reset_code_view_email');
    }

    public function reset_code(ResetCodeRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $verifications = $request->session()->get('verifications');
        $code = $data['code'];
        return $this->service->reset_code_web($verifications, $code);
    }

    public function reset_code_view(): Factory|View|Application
    {
        return view('auth.code');
    }

    public function reset_password(): Factory|View|Application
    {
        return view('auth.confirm_password');
    }

    public function reset_password_save(ResetPasswordRequest $request): Redirector|Application|RedirectResponse
    {
        $data = $request->validated();
        $password = $data['password'];
        $session = $request->session();
        $this->service->reset_password_save($session, $password);
        return redirect('/login');
    }

    public function verifyProfile(VerifyProfileRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();
        $for_ver_func = $data['for_ver_func'];
        $sms_otp = $data['sms_otp'];
        return $this->service->verifyProfile($for_ver_func, $user, $sms_otp);
    }

    /**
     * @throws Exception
     */
    public function self_delete(): RedirectResponse
    {
        /** @var User $user */
        $user = auth()->user();
        VerificationService::send_verification('phone', $user, $user->phone_number);
        return redirect()->back()->with([
            'sms_code' => __('Код отправлен!')
        ]);
    }

    public function confirmationSelfDelete(ResetCodeRequest $request): Redirector|Application|RedirectResponse
    {
        $data = $request->validated();
        /** @var User $user */
        $user = auth()->user();
        $code = $data['code'];
        return $this->service->confirmationSelfDelete($user, $code);
    }

}
