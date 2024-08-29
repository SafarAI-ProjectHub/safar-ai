<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use DataTables;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $users = User::whereHas('roles', function ($query) {
                $query->where('name', 'Teacher');
            });

            if ($request->has('user_id') && $request->user_id != '') {
                $users->where('id', $request->user_id);
            }

            return Datatables::of($users)
                ->addColumn('name', function ($user) {
                    return $user->full_name;
                })
                ->addColumn('permissions', function ($user) {
                    $permission = Permission::findByName('create courses');
                    return $user->hasPermissionTo($permission->name) ? '<span class="badge bg-success">create Unit</span>' : '<span class="badge bg-secondary">None</span>';
                })
                ->addColumn('actions', function ($user) {
                    $permission = Permission::findByName('create courses');
                    $action = $user->hasPermissionTo($permission->name) ? 'revoke' : 'grant';
                    $buttonClass = $user->hasPermissionTo($permission->name) ? 'btn-danger' : 'btn-success';
                    $buttonText = $user->hasPermissionTo($permission->name) ? 'Revoke' : 'Grant';

                    return '<button class="btn btn-sm ' . $buttonClass . '" onclick="updatePermission(' . $user->id . ', ' . $permission->id . ', \'' . $action . '\')">' . $buttonText . ' Permission</button>';
                })
                ->rawColumns(['permissions', 'actions'])
                ->make(true);
        }

        $users = User::whereHas('roles', function ($query) {
            $query->where('name', 'Teacher');
        })->get();
        $permission = Permission::findByName('create courses');

        return view('dashboard.admin.teacher_permission', compact('users', 'permission'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'permission_id' => 'required|exists:permissions,id',
            'action' => 'required|in:grant,revoke',
        ]);

        $user = User::findOrFail($request->user_id);
        $permission = Permission::findById($request->permission_id);

        if ($request->action == 'grant') {
            $user->givePermissionTo($permission);
        } else {
            $user->revokePermissionTo($permission);
        }

        return response()->json(['status' => 'Permission updated successfully!']);
    }
}