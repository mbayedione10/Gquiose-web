<?php
namespace App\Http\Controllers\Api;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\PermissionCollection;

class RolePermissionsController extends Controller
{
    public function index(Request $request, Role $role): PermissionCollection
    {
        $this->authorize('view', $role);

        $search = $request->get('search', '');

        $permissions = $role
            ->permissions()
            ->search($search)
            ->latest()
            ->paginate();

        return new PermissionCollection($permissions);
    }

    public function store(
        Request $request,
        Role $role,
        Permission $permission
    ): Response {
        $this->authorize('update', $role);

        $role->permissions()->syncWithoutDetaching([$permission->id]);

        return response()->noContent();
    }

    public function destroy(
        Request $request,
        Role $role,
        Permission $permission
    ): Response {
        $this->authorize('update', $role);

        $role->permissions()->detach($permission);

        return response()->noContent();
    }
}
