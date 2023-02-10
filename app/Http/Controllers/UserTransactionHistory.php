<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\User\UserService;
use Illuminate\Http\JsonResponse;

class UserTransactionHistory extends Controller
{
    public UserService $service;

    public function __construct(UserService $userService)
    {
        $this->service = $userService;
    }
    /**
     * @return JsonResponse
     */
    public function getTransactions(): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();
        $payment = strtolower($_GET['method']);
        return $this->service->getTransactions($user, $payment);
    }
}
