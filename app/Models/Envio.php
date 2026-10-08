<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Envio extends Model
{
    protected $primaryKey = 'envio_id';
    protected $fillable = [
        'servicio_id',
        'tipo_envio',
        'oficina_id',
        'usuario_id',
        'nombre_rem',
        'apellido_rem',
        'tipo_documento_rem',
        'documento_rem',
        'codigo_postal_rem',
        'estado_rem',
        'municipio_rem',
        'parroquia_rem',
        'ciudad_rem',
        'direccion_rem',
        'correo_rem',
        'telefono_rem',
        'nombre_dest',
        'apellido_dest',
        'tipo_documento_dest',
        'documento_dest',
        'codigo_postal_dest',
        'continente_dest',
        'pais_dest',
        'estado_dest',
        'municipio_dest',
        'parroquia_dest',
        'ciudad_dest',
        'oficina_dest_id',
        'direccion_dest',
        'tlf_dest',
        'correo_dest',
        'servicio',
        'servicio_expreso',
        'peso',
        'coste',
        'contenido',
        'apartado_postal',
        'devolucion',
        'descubierto',
        'tipo_saca_id',
        'codigo_envio',
        'carga_masiva',
        'coste_sin_iva',
        'contrato_corporativo_id',
        'cliente_corporativo_autorizado_id',
        'certificado',
        'tasa_bs',
    ];


    public function iposplus()
    {
        return $this->hasMany(EnvioIposplus::class, 'envio_id', 'envio_id');
    }

    public function unidad_analisis_actual()
    {
        return $this->hasOne(UnidadAnalisisDevolucion::class, 'envio_id', 'envio_id')
            ->where('estatus', true);
    }

    public function rezago_actual()
    {
        return $this->hasOne(EnvioRezago::class, 'envio_id', 'envio_id')
            ->where('estatus', true);
    }

    public function envio_encaminamientos()
    {
        return $this->hasMany(EnvioEncaminamiento::class, 'envio_id', 'envio_id');
    }

    public function encaminamiento_actual()
    {
        return $this->hasOne(EnvioEncaminamiento::class, 'envio_id', 'envio_id')
            ->latestOfMany('envios_encaminamiento_id');
    }

    public function factura_envio()
    {
        return $this->hasOne(FacturacionEnvio::class, 'envio_id', 'envio_id');
    }

    public function servicio()
    {
        return $this->belongsTo(Servicio::class, 'servicio_id', 'servicio_id');
    }

    public function facturacion_envios()
    {
        return $this->hasMany(FacturacionEnvio::class, 'envio_id', 'envio_id');
    }

    public function oficinas()
    {
        return $this->belongsTo(Oficina::class, 'oficina_id', 'oficina_id');
    }

    public function oficinas_destino()
    {
        return $this->belongsTo(Oficina::class, 'oficina_dest_id', 'oficina_id');
    }

    public function estadoDestino()
    {
        return $this->belongsTo(Estado::class, 'estado_dest', 'estado_id');
    }

    public function sacas()
    {
        return $this->belongsToMany(Saca::class, 'envio_saca', 'envio_id', 'saca_id')
            ->withPivot('activo')
            ->withTimestamps();
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'usuario_id', 'id');
    }

    public function almacen()
    {
        return $this->hasMany(EnvioAlmacen::class, 'envio_id', 'envio_id');
    }

    public function apartado()
    {
        return $this->hasOne(CodigoApartadoPostal::class, 'apartado_postal');
    }

    public function tipoSaca()
    {
        return $this->belongsTo(TipoSaca::class, 'tipo_saca_id', 'tipo_saca_id');
    }

    public function telegrama_recibido()
    {
        return $this->hasMany(TelegramaRecibido::class, 'envio_id', 'envio_id');
    }

    public function aviso()
    {
        return $this->hasMany(AlmacenAviso::class, 'envio_id', 'envio_id');
    }

    public function facturacion_destino()
    {
        return $this->hasMany(FacturacionDestinatario::class, 'envio_id', 'envio_id');
    }

    public function envio_internacional()
    {
        return $this->hasMany(EnvioInternacional::class, 'envio_id', 'envio_id');
    }

    public function incidencia()
    {
        return $this->hasMany(EnvioIncidencia::class, 'envio_id', 'envio_id');
    }

    public function exporta_facil()
    {
        return $this->hasOne(EnvioExportaFacil::class, 'envio_id');
    }

    public function oficinaOrigen()
    {
        return $this->belongsTo(Oficina::class, 'oficina_id', 'oficina_id');
    }

    public function oficinaDestino()
    {
        return $this->belongsTo(Oficina::class, 'oficina_dest_id', 'oficina_id');
    }

    public function almacenAduana()
    {
        return $this->hasOne(AlmacenAduana::class, 'envio_id');
    }

    public function avisos_telegrama()
    {
        return $this->hasMany(AvisoTelegrama::class, 'envio_id', 'envio_id');
    }

    public function envio_insumo()
    {
        return $this->hasMany(EnvioInsumo::class, 'envio_id', 'envio_id');
    }

    public function contrato()
    {
        return $this->belongsTo(ContratoCorporativo::class, 'contrato_corporativo_id', 'contrato_corporativo_id');
    }

    public function autorizado()
    {
        return $this->belongsTo(ClienteCorporativoAutorizado::class, 'cliente_corporativo_autorizado_id', 'cliente_corporativo_autorizado_id');
    }

    public function registros_entregas()
    {
        return $this->hasOne(RegistroEntrega::class, 'envio_id', 'envio_id');
    }

    public function aduana_actual()
    {
        return $this->hasOne(AlmacenAduana::class, 'envio_id', 'envio_id')
            ->where('estatus', true);
    }

    public function almacen_actual()
    {
        return $this->hasOne(EnvioAlmacen::class, 'envio_id', 'envio_id')
            ->where('estatus', true);
    }

    public function expedicion_actual()
    {
        return $this->hasOne(EnvioExpedicion::class, 'envio_id', 'envio_id')
            ->where('estatus', true);
    }

    public function distribucion_actual()
    {
        return $this->hasOne(EnvioDistribucionPaqueteMuestra::class, 'envio_id', 'envio_id')
            ->where('estatus', true);
    }
}
