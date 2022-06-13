<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\SessionResource;
use App\Models\Session;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    public function index()
    {
        return $this->success(SessionResource::collection(Session::query()->where('user_id', auth()->id())->get()));
    }
}
