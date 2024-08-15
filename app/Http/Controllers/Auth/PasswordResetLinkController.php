<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class PasswordResetLinkController extends Controller
{
    public function store(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = DB::table('users')->where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'Пользователь с таким адресом электронной почты не существует.'], 404);
        }
        //Используется для сброса
        $token = Str::random(60);

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => Hash::make($token),
            'created_at' => Carbon::now()
        ]);

        //ссылка создана для примера. В идеале нужно отправлять ссылку ну форму на сайте, там где юзер будет вводить новый пароль.
        $resetUrl = url('/api/reset-password?token=' . $token . '&email=' . urlencode($request->email));

        try {
            Mail::raw("Нажмите на ссылку, чтобы сбросить пароль: {$resetUrl}", function ($message) use ($request) {
                $message->to($request->email)
                    ->subject('Уведомление о сбросе пароля');
            });

            return response()->json(['message' => 'Ссылка для сброса пароля была отправлена на ваш адрес электронной почты.'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Не удалось отправить письмо. Попробуйте позже.'], 500);
        }
    }
}
