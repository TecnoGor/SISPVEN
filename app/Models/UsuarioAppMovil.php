<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;

class UsuarioAppMovil extends Authenticatable
{
    use HasFactory, HasApiTokens;

    protected $table = 'sispven_app.usuarios';
    protected $primaryKey = 'usuario_id';

    protected $fillable = [
        'nombre',
        'apellido',
        'correo',
        'telefono',
        'direccion',
        'contraseña',
        'fecha_nacimiento',
        'cedula',
        'tipo_documento',
        'rol',
        'activo',
        'estado_id',
        'correo_verificado',
        'otp',
        'imagen_perfil',
        'imagen_documento',
        'identidad_verificada'
    ];

    protected $hidden = [
        'contraseña',
        'otp'
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'activo' => 'boolean',
        'correo_verificado' => 'date',
        'identidad_verificada' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relación con el modelo Estado
     */
    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estado_id', 'estado_id');
    }

    /**
     * Indicar a Laravel/Sanctum qué columna contiene la contraseña.
     * Por defecto busca 'password', pero nuestra columna es 'contraseña'.
     */
    public function getAuthPassword()
    {
        return $this->attributes['contraseña'];
    }

    /**
     * Mutator para encriptar la contraseña automáticamente.
     * Se usa override de setAttribute() porque el carácter ñ en
     * setContraseñaAttribute() rompe la resolución de mutators de Eloquent.
     *
     * IMPORTANTE: Se verifica si el valor ya es un hash bcrypt para evitar
     * el doble-hash durante la hidratación del modelo desde la base de datos.
     */
    public function setAttribute($key, $value)
    {
        if ($key === 'contraseña') {
            // Si ya es un hash bcrypt ($2y$) o argon2 ($argon), almacenar tal cual.
            // Esto evita el doble-hash cuando Eloquent hidrata el modelo desde BD.
            if ($value !== null && (str_starts_with($value, '$2y$') || str_starts_with($value, '$argon'))) {
                $this->attributes['contraseña'] = $value;
            } else {
                $this->attributes['contraseña'] = Hash::make($value ?? '');
            }
            return $this;
        }
        return parent::setAttribute($key, $value);
    }

    /**
     * Obtiene la cédula completa con el tipo de documento
     */
    public function getCedulaCompletaAttribute()
    {
        return $this->tipo_documento . '-' . $this->cedula;
    }
}
