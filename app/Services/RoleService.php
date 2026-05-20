<?php

declare(strict_types=1);

namespace App\Services;

use App\Http\Requests\RoleRequest;
use App\Models\Role;

class RoleService
{
    public function create(RoleRequest $request, ...$roles): Role
    {
        $data = $request->validated();
        $role = new Role();
        $role->fill($data);
        $role->save();
        return $role;
    }

    public function update(RoleRequest $request, ...$roles)
    {
        $data = $request->validated();


    }

    public function delete(RoleRequest $request, ...$roles)
    {
        if (in_array($role->slug, Role::getSystemRoles())) {
            throw new Exception('Нельзя удалить системную роль');
        }

    }

}
