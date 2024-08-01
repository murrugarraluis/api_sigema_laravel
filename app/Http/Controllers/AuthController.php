<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Resources\AuthUserResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * @throws ValidationException
     */
    public function login(LoginRequest $request)
    {
        $user = User::has('roles')->where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message'=>'Invalid Credentials.'],401);
        }
        $token = $user->createToken($request->email)->plainTextToken;
        return (new AuthUserResource($user))->additional(['token' => $token]);
    }
    public function logout(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Token removed.'], 200);
    }
}
