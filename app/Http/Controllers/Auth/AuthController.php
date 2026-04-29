<?php

namespace App\Http\Controllers\Auth;

use App\DTOs\Auth\RegisterDto;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\Auth\UserService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use App\DTOs\UpdateProfileDto;// их нету еще
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\UpdateProfileRequest;


class AuthController
{
    public function __construct(
        private readonly UserService $userService
    ) {}

    public function showRegistrationForm(): Factory|View
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request): RedirectResponse
    {
        $dto = RegisterDto::fromRequest($request);
        $user = $this
            ->userService
            ->register($dto);

        return redirect()
            ->route('login.form')
            ->with('status', 'Регистрация прошла успешно');
    }

    public function showLoginForm(): Factory|View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->validated();

        if (Auth::attempt($credentials)) {
            return redirect()->intended('profile');
        }

        return back()->withErrors(['email' => 'Invalid credentials']);
    }

    public function logout(): RedirectResponse
    {
        Auth::logout();

        return redirect()->route('login.form');
    }
}


