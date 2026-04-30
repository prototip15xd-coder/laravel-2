<?php

namespace App\Services\Auth;

use App\DTOs\Auth\RegisterDto;
use App\DTOs\Auth\UpdateProfileDto;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class UserService
{
    public function register(RegisterDto $dto): User
    {
        $user = new User();
        $user->first_name = $dto->first_name;
        $user->last_name = $dto->last_name;
        $user->email = $dto->email;
        $user->password = Hash::make($dto->password);
        $user->save();

        // TODO: после изучения очередей добавить событие для отправки приветственного письма:
        // event(new Registered($user));

        return $user;
    }

    /**
     * @throws AuthenticationException
     */
    public function updateProfile(UpdateProfileDto $dto): void
    {
        $user = Auth::user();
        $user->fill($dto->toArray());
        $user->save();
    }

    /**
     * @throws ValidationException
     */
    public function updatePassword(
        User $user,
        string $currentPassword,
        string $newPassword
    ): void
    {
        if (!Hash::check($currentPassword, $user->password)) {
            throw ValidationException::withMessages(['current_password' => 'Invalid current password']);
        }

        $user->password = Hash::make($newPassword);
        $user->save();
    }
}
