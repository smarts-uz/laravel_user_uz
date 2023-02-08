<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResetCodeRequest;
use App\Http\Requests\ResetEmailRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\ResetRequest;
use App\Http\Requests\VerifyProfileRequest;
use App\Mail\VerifyEmail;
use App\Models\User;
use App\Models\Task;
use App\Services\NotificationService;
use App\Services\SmsMobileService;
use App\Services\User\UserService;
use App\Services\VerificationService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use RealRashid\SweetAlert\Facades\Alert;


class UserController extends Controller
{

    public function code()
    {
        return view('auth.register_code');
    }

    public function signup()
    {
        return view('auth.signup');
    }

    public function reset()
    {
        return view('auth.reset');
    }

    public function confirm()
    {
        return view('auth.confirm');
    }

    /**
     * @throws \Exception
     */
    public function reset_submit(ResetRequest $request)
    {
        $service = new UserService();
        $data = $request->validated();
        $phone_number = $data['phone_number'];
        $service->reset_submit($phone_number);
        return redirect()->route('user.reset_code_view');
    }

    /**
     * @throws \Exception
     */
    public function reset_by_email(ResetEmailRequest $request): RedirectResponse
    {
        $service = new UserService();
        $service->reset_by_email($request);
        return redirect()->route('user.reset_code_view_email');
    }

    public function reset_code(ResetCodeRequest $request)
    {
        $service = new UserService();
        $data = $request->validated();
        $email = $data['email'];
        return $service->reset_code($email);
    }

    public function reset_code_view()
    {
        return view('auth.code');
    }

    public function reset_password()
    {
        return view('auth.confirm_password');
    }

    public function reset_password_save(ResetPasswordRequest $request)
    {
        $data = $request->validated();
        $service = new UserService();
        $session = $request->session();
        $password = $data['password'];
        $service->reset_password_save($session, $password);
        return redirect('/login');
    }

    public function verifyProfile(VerifyProfileRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();
        $service = new UserService();
        $for_ver_func = $data['for_ver_func'];
        $sms_otp = $data['sms_otp'];
        return $service->verifyProfile($user, $for_ver_func, $sms_otp);
    }

    /**
     * @throws \Exception
     */
    public function self_delete(): RedirectResponse
    {
        $service = new UserService();
        $user = \auth()->user();
        return $service->self_delete($user);
    }

    public function confirmationSelfDelete(ResetCodeRequest $request)
    {
        $data = $request->validated();
        /** @var User $user */
        $user = \auth()->user();
        $code = $data['code'];
        $service = new UserService();
        return $service->confirmationSelfDelete($code, $user);
    }

}
