<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RoleWiseDataController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->can('view_admin_data')) {
            return response()->json([
                'data' => 'This is ADMIN data'
            ]);
        }

        if ($user->can('view_user_data')) {
            return response()->json([
                'data' => 'This is USER data'
            ]);
        }

        return response()->json([
            'message' => 'Unauthorized'
        ], 403);
    }
}
