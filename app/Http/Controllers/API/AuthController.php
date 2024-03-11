<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\LoginRequest;
use App\Http\Requests\API\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends ApiController
{
    public function register(RegisterRequest $request)
    {
        User::create($request->validated());

        return $this->setResponse('Registered', 201);
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        if (!Auth::attempt($credentials)) {
            return $this->setResponse('Unauthorized', 401);
        }

        $token = Auth::user()->createToken('authToken')->plainTextToken;

        return $this->setResponse('Logged in', 200, ['token' => $token]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->setResponse('Logged out', 201);
    }
}
