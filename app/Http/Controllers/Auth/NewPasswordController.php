<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResetPasswordRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class NewPasswordController extends Controller
{
    public function store(ResetPasswordRequest $request)
    {
        $validatedData = $request->validated();

        $passwordReset = DB::table('password_reset_tokens')
            ->where('email', $validatedData['email'])
            ->first();

        if (!$passwordReset || !Hash::check($validatedData['token'], $passwordReset->token)) {
            return response()->json(['message' => 'Недействительный токен.'], 400);
        }

        // Поиск пользователя по email
        $user = DB::table('users')->where('email', $validatedData['email'])->first();

        if (!$user) {
            return response()->json(['message' => 'Пользователь с таким адресом электронной почты не существует.'], 404);
        }

        // Обновление пароля пользователя
        DB::table('users')
            ->where('email', $validatedData['email'])
            ->update(['password' => Hash::make($validatedData['password'])]);

        // Удаление записи сброса пароля
        DB::table('password_reset_tokens')->where('email', $validatedData['email'])->delete();

        return response()->json(['message' => 'Ваш пароль успешно сброшен.'], 200);
    }
}

