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
        $service->reset_submit($request);
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
        return $service->reset_code($request);
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
        $service = new UserService();
        $service->reset_password_save($request);
        return redirect('/login');
    }

    public function verifyProfile(VerifyProfileRequest $request, User $user): RedirectResponse
    {
        $service = new UserService();
        return $service->verifyProfile($request, $user);
    }

    /**
     * @throws \Exception
     */
    public function self_delete(): RedirectResponse
    {
        $service = new UserService();
        return $service->self_delete();
    }

    public function confirmationSelfDelete(ResetCodeRequest $request)
    {
        $service = new UserService();
        return $service->confirmationSelfDelete($request);
    }

}
