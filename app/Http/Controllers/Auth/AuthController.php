<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\DTO\Auth\RegisterDto;
use App\DTO\Auth\UpdateProfileDto;
//use App\Http\Controllers\Request\EmailVerificationRequest;
use App\Http\Job\SendRegistrationVerificationJob;
use App\Http\Job\SendWelcomeAfterVerificationJob;
use App\Http\Requests\Auth\AddressRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\Address;
use App\Services\Auth\UserService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController
{
    public function __construct(
        private readonly UserService $userService
    ) {
    }

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

        Auth::login($user);
        //$request->session()->regenerate();
        SendRegistrationVerificationJob::dispatch($user->id);

        return redirect()
            ->route('email.verify')
            ->with('status', 'Регистрация прошла успешно, подтвердите профиль: укажите почту на которую отправить пиьсом подтверждения');
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

    public function showProfile(): Factory|View
    {
        $user = Auth::user();
        $addresses = $user->addresses()->orderByDesc('is_default')->get();
        return view('auth.profile', compact('user', 'addresses'));
    }

    public function updateProfile(UpdateProfileRequest $request): RedirectResponse
    {
        $dto = UpdateProfileDto::fromRequest($request);
        $user = $this
            ->userService
            ->updateProfile($dto);

        return redirect()->route('profile.form');
    }

    public function showChangePasswordForm(): Factory|View
    {
        return view('auth.change-password');
    }

    /**
     * @throws ValidationException
     */
    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        $validatedData = $request->validated();
        $user = Auth::user();

        $this->userService
            ->updatePassword(
                $user,
                $validatedData['current_password'],
                $validatedData['new_password']
            );
        return redirect()
            ->route('profile.form')
            ->with('status', 'Password successfully changed!');
    }

    public function create()
    {
        return view('auth.address');
    }

    public function store(AddressRequest $request)
    {
        $data = $request->validate([
            'title' => 'nullable|string|max:100',
            'recipient_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|min:2',
            'city' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'comment' => 'nullable|string',
            'is_default' => 'boolean',
        ]);

        $data['user_id'] = Auth::id();

        if (!empty($data['is_default'])) {
            Auth::user()->addresses()->update(['is_default' => false]);
        }

        Address::create($data);

        return redirect()->route('profile.form')->with('success', 'Адрес добавлен');
    }

    public function destroy(Address $address)
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $address->delete();

        return redirect()->route('profile.edit')->with('success', 'Адрес удалён');
    }

    public function setDefault(Address $address)
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        Auth::user()->addresses()->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        return redirect()->route('profile.edit')->with('success', 'Основной адрес изменён');
    }

    public function verify(): View
    {
        return view('auth.verify-email');
    }

    public function signed(EmailVerificationRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $wasVerified = $user->hasVerifiedEmail();
        \Log::info('wasVerified', ['value' => $wasVerified]);
        $request->fulfill();

        if (!$wasVerified) {
            SendWelcomeAfterVerificationJob::dispatch($user->id);
        }

        return redirect()->route('profile.form')->with('success', 'Email подтверждён!');
    }

    public function send()
    {
        Auth::user()->sendEmailVerificationNotification();
        return back()->with('success', 'Ссылка для подтверждения отправлена!');
    }
}
