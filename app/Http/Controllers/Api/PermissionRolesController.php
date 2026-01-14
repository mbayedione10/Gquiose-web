<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RoleCollection;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PermissionRolesController extends Controller
{
    public function index(
        Request $request,
        Permission $permission
    ): RoleCollection {
        $this->authorize('view', $permission);

        $search = $request->get('search', '');

        $roles = $permission
            ->roles()
            ->search($search)
            ->latest()
            ->paginate();

        return new RoleCollection($roles);
    }

    public function store(
        Request $request,
        Permission $permission,
        Role $role
    ): Response {
        $this->authorize('update', $permission);

        $permission->roles()->syncWithoutDetaching([$role->id]);

        return response()->noContent();
    }

    public function destroy(
        Request $request,
        Permission $permission,
        Role $role
    ): Response {
        $this->authorize('update', $permission);

        $permission->roles()->detach($role);

        return response()->noContent();
    }
}
