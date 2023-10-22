<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Log in the user.
     */
    public function login(LoginRequest $request): Response
    {

        $user = User::where('email', $request->email)->first();

        if(!$user || !Hash::check($request->password, $user->password)) {
            return response([
                'message' => 'These credentials do not match our records.',
                'status' => 0
            ], 401);
        }

        $token = $user->createToken('myapptokens')->plainTextToken;

        return response([
            'token' => $token,
            'mesasge' => 'success',
            'status' => 1
        ], 200);
    }

    /**
     * Register the user.
     */
    public function register(RegisterRequest $request): Response
    {
        $user = User::create([
            'first_name'    => $request->name,
            'nickname'    => $request->name,
            'role_id'       => 1,
            'status'        => 1,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $token = $user->createToken('myapptokens')->plainTextToken;

        return response([
            'token' => $token,
            'mesasge' => 'success',
            'status' => 1
        ], 201);
    }

    /**
     * Log out the user.
     */
    public function logout(): Response
    {

        auth()->user()->tokens()->delete();
        return response([
            'mesasge' => 'success',
            'status' => 1
        ], 201);
    }
}
