<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Google_Client;
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
     * Log in the user.
     */
    public function verify(Request $request): Response
    {

        $client = new Google_Client(['client_id' => '334836814599-trhjl192sj725fn9nbjubddejdmh5s8m.apps.googleusercontent.com']);  // Specify the CLIENT_ID of the app that accesses the backend
        $payload = $client->verifyIdToken($request->token);
        if ($payload) {
        $userid = $payload['sub'];
        // If the request specified a Google Workspace domain
        //$domain = $payload['hd'];
        } else {
            // Invalid ID token
        }

        // $user = User::where('email', $request->email)->first();

        // if(!$user || !Hash::check($request->password, $user->password)) {
        //     return response([
        //         'message' => 'These credentials do not match our records.',
        //         'status' => 0
        //     ], 401);
        // }

        // $token = $user->createToken('myapptokens')->plainTextToken;

        return response([
            'token' => $payload,
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
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'nickname' => $request->nickname,
            'role_id' => 4,
            'status' => 1,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'profile_photo_path'    => config('image.default-profile-image'),
            'bg_photo_path'         => config('image.default-background-image'),
        ]);

        $user->assignRole(['user']);

        $token = $user->createToken('myapptokens')->plainTextToken;

        return response([
            'token' => $token,
            'status' => 1
        ], Response::HTTP_CREATED);
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
