<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResetCodeRequest;
use App\Http\Requests\ResetEmailRequest;
use App\Http\Requests\ResetRequest;
use App\Services\User\UserService;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class DeleteAccountController extends Controller
{
    public UserService $service;

    public function __construct(UserService $userService)
    {
        $this->service = $userService;
    }

    public function delete_account(): Factory|View|Application
    {
        return view('delete_account.delete');
    }

    /**
     * @throws Exception
     */
    public function delete_email(ResetEmailRequest $request): Factory|View|Application
    {
        $data = $request->validated();
        $email = $data['email'];
        $this->service->reset_by_email($email);

        return view('delete_account.code');
    }

    /**
     * @throws Exception
     */
    public function delete_phone(ResetRequest $request): Factory|View|Application
    {
        $data = $request->validated();
        $phone_number = $data['phone_number'];
        $this->service->reset_submit($phone_number);

        return view('delete_account.code');
    }

    public function delete_code(ResetCodeRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $verifications = $request->session()->get('verifications');
        $code = $data['code'];
        return $this->service->delete_code($verifications, $code);
    }
}
