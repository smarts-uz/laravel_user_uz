<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\SessionResource;
use App\Models\Session;

class SessionController extends Controller
{
    public function index()
    {
        return $this->success(SessionResource::collection(Session::query()->where('user_id', auth()->id())->get()));
    }

    public function clear_sessions()
    {
        $user = auth()->user();
        Session::query()->where('user_id', $user->id)->delete();
        $user->tokens->each(function ($token, $key) {
            $token->delete();
        });
        return $this->success('', 'Successfully deleted');
    }
}
