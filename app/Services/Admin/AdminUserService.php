<?php

declare(strict_types=1);

namespace App\Services\Admin;

use App\Models\User;

class AdminUserService
{
    public function createFromAdmin(array $data): User
    {
        return User::create([
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'email'      => $data['email'],
            'status'     => $data['status'],
            'role'    => $data['role'],
            'password'   => bcrypt(Str::random(10)),
        ]);
    }
    public function resetPassword(User $user, string $password): void
    {
        $user->update([
            'password' => bcrypt($password)
        ]);
    }

    // app/Services/Admin/AdminUserService.php
    public function updateUser(User $user, array $data): User
    {
        $user->update([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'status' => $data['status'],
        ]);

        // Обновляем роль
        $user->roles()->sync([$data['role_id']]);

        return $user;
    }

}
