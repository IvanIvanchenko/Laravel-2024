<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(RegisterUserRequest $request)
    {
        $requestValidate = $request->validated();
        User::create([
            'name' => $requestValidate['name'],
            'email' => $requestValidate['email'],
            'password' => Hash::make($requestValidate['password']),
        ]);

        return response()->json(['message' => 'Пользователь успешно зарегистрирован'], 201);
    }

    public function login(LoginUserRequest $request)
    {
        $requestValidate=$request->validated();

        $user = User::where('email', $requestValidate['email'])->first();

        if (! $user || ! Hash::check($requestValidate['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Предоставленные учетные данные неверны.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => 'Bearer '. $token,
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Вы успешно вышли из системы'], 200);
    }
}
