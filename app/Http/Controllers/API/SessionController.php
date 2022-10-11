<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SessionDeleteRequest;
use App\Http\Resources\SessionResource;
use App\Models\Session;
use App\Models\User;

class SessionController extends Controller
{
    public function index()
    {
        return $this->success(SessionResource::collection(Session::query()->where('user_id', auth()->id())->get()));
    }

    public function clearSessions(SessionDeleteRequest $request)
    {
        /** @var User $user */
        $user = auth()->user();
        Session::query()->where('user_id', $user->id)->whereNot('id', $request->get('session_id'))->delete();
        $user->tokens->each(function ($token, $key) use ($user) {
            if ($token->id != $user->token()->id) {
                $token->delete();
            }
        });
        return $this->success('', __('Успешно удалено'));
    }
}
