<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasProfilePhoto, Notifiable, TwoFactorAuthenticatable, HasRoles;

    // Removed automatic role assignment for "Usuario Correspondencia"

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'cedula',
        'telefono',
        'password',
        'oficina_id',
        'empleado_id',
        'activo',
    ];

    /**
     * Relationship with Oficina model.
     */
    public function oficina()
    {
        return $this->belongsTo(Oficina::class, 'oficina_id', 'oficina_id');
    }

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_id', 'empleado_id');
    }

    /**
     * Relationship with Envio model.
     */
    public function envios()
    {
        return $this->hasMany(Envio::class, 'envio_id');
    }

    /**
     * Relationship with UsuariosEstados model.
     * 
     * This establishes that a user may have an associated state,
     * enabling filtering based on user state for role-specific queries.
     */
    public function usuarioEstado()
    {
        return $this->hasOne(UsuarioEstado::class, 'id_user', 'id');
    }

    /**
     * Relationship with EnvioAlmacen model.
     * 
     * This establishes a many-to-many relationship between users and EnvioAlmacen,
     * allowing for the assignment of shipments to postal workers.
     */
    public function enviosAsignados()
    {
        return $this->belongsToMany(EnvioAlmacen::class, 'asignacion_envio_cartero', 'user_id', 'envio_almacen_id')
                    ->withPivot('estatus')
                    ->withTimestamps();
    }

    public function insumo()
    {
        return $this->hasMany(InsumoUsuario::class, 'id', 'usuario_id');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Obtener la cantidad de telegramas nuevos dirigidos a la oficina del usuario.
     */
    public function telegramasNuevosCount(): int
    {
        return \App\Models\TelegramaRecibido::where('recibido', false)
            ->whereHas('envios', function ($query) {
                $query->where('servicio_id', 3)
                      ->where('oficina_dest_id', $this->oficina_id);
            })
            ->count();
    }
}
