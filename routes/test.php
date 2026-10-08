<?php

use Illuminate\Support\Facades\Route;
use App\Models\UsuarioAppMovil;

Route::get('/test-mutator', function () {
    $usuario = new UsuarioAppMovil();
    $usuario->contraseña = 'test123';
    
    $hash = $usuario->getAttributes()['contraseña'] ?? 'null';
    $isHashed = str_starts_with($hash, '$2y$');
    
    return response()->json([
        'hash' => $hash,
        'is_hashed' => $isHashed,
        'auth_password' => $usuario->getAuthPassword()
    ]);
});
