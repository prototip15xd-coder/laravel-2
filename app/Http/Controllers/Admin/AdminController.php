<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserStoreRequest;
use App\Http\Requests\Admin\UserUpdateRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Services\Admin\AdminUserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function create(): View
    {
        $roles = \App\Models\Role::all();

        return view('admin.users.create', [
            'roles' => $roles
        ]);
    }

    public function createUser(
        RegisterRequest $request,
        AdminUserService $service,
    ): RedirectResponse {
        try {
            $service->createFromAdmin((array)$request);
            return redirect()
                ->route('admin.user.form') //перекидывает на страницу пользователя которого зарегали
                ->with('status', 'Регистрация пользователя прошла успешно');
        } catch (ValidationException $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($e->errors());
        }
    }

    public function show(User $user): View
    {
        return view(
            'admin.users.show',
            [
            'user' => $user,
            ]
        );
    }

    public function index(): View
    {
        $users = User::query()
            ->with('roles')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.users.index', [
            'users' => $users
        ]);
    }

    public function store(UserStoreRequest $request, AdminUserService $service)
    {
        $service->createFromAdmin($request->validated());

        return redirect()->route('admin.users.index');
    }

    public function resetPassword(UserStoreRequest $request, AdminUserService $service): void
    {
        $user = $service->createFromAdmin($request->validated());
        $password = $user->password;
        $service->resetPassword($user, $password);
    }

    public function edit(User $user): View
    {
        $roles = \App\Models\Role::all();

        return view('admin.users.edit', [
            'user' => $user,
            'roles' => $roles
        ]);
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->delete();
        return redirect()
            ->route('admin.users.index')
            ->with('success'. 'пользователь удален');
    }

    public function update(UserUpdateRequest $request, User $user, AdminUserService $service): RedirectResponse
    {
        $service->updateUser($user, $request->validated());

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Пользователь обновлён');
    }

}
