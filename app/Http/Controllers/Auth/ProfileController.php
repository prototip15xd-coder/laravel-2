<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController
{
    public function destroy(Request $request)
    {
        $user = $request->user();

        if (!Hash::check($request->password, $user->password)) {
            return back()->with('error', 'Неверный пароль.');
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Профиль удален. Мы будем скучать!');
    }
}
