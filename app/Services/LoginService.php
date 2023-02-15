<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;

class LoginService
{
    /**
     * @throws \Exception
     */
    public function loginPost($data): User
    {
        /** @var User $user */
        $user = User::query()->where('email', $data['email'])
            ->orWhere('phone_number', $data['email'])
            ->first();

        return $user;
    }
}
