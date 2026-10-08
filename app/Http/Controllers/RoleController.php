<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function permisos($role)
    {
        $role = Role::find($role);

        return view('roles.asignar-permisos', compact('role'));
    }
}
