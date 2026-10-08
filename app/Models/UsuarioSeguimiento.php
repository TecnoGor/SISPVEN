<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsuarioSeguimiento extends Model
{
    use HasFactory;

    protected $table='usuario_seguimiento';
    protected $primaryKey='usuario_seguimiento_id';

    /*
        Las reglas para añadir un usuario seguimiento deben ser:
        1. solo CRUD's del usuario (C= Create, obviamos R, U= Update, D= Delete).
        2. el campo accion debe llevar solo alguna de las siguientes palabras: create, update, delete
        3. el campo descripcion debe seguir la siguiente nomenclatura de ejemplo: usuario (usuario_id) hizo un (crud) a/al/del/de (cosa que modifico) id

        Excepciones a la regla 2 (eventos de autenticación):
        - 'login'         para inicios de sesión exitosos
        - 'logout'        para cierres de sesión
        - 'login_failed'  para intentos de inicio de sesión fallidos
        En estos casos también se almacenan ip_address y user_agent.
    */

    protected $fillable = [
        'usuario_id',
        'accion',
        'descripcion',
        'ip_address',
        'user_agent',
    ];
}
