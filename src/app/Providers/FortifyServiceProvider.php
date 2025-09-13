<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Laravel\Fortify\Fortify;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Actions\Fortify\CreateNewUser;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // 会員登録画面
        Fortify::registerView(fn() => view('register'));

        // ログイン画面
        Fortify::loginView(fn() => view('login'));

        // ユーザー登録（FormRequest を使用して CreateNewUser に渡す）
        Fortify::createUsersUsing(CreateNewUser::class);

        // ログイン認証
        Fortify::authenticateUsing(function (\Illuminate\Http\Request $request) {
            $user = User::where('email', $request->email)->first();

            if ($user && Hash::check($request->password, $user->password)) {
                return $user;
            }
        });


        // ログイン試行のレート制限
        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->email;
            return Limit::perMinute(10)->by($email.$request->ip());
        });
    }
}
