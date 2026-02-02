<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RolePermissionController extends Controller
{
    public function roles()
    {
        return response()->json(
            Role::with('permissions')->get()
        );
    }

    public function assignPermission(Request $request)
    {
        $request->validate([
            'role' => 'required',
            'permissions' => 'required|array'
        ]);

        $role = Role::findByName($request->role);
        $role->syncPermissions($request->permissions);

        return response()->json([
            'message' => 'Permissions assigned successfully'
        ]);
    }
}
