<?php

namespace Database\Seeders;

//use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Producto;
use Illuminate\Database\Seeder;
use Database\Seeders\SectoresSeeder;
use Database\Seeders\TarifasExportaFacil;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            DiasSemanaSeeder::class,
            ServiciosSeeder::class,
            TiposPagosSeeder::class,
            Medidas::class,
            ContinentesSeeder::class,
            Incidencia::class,
            PaisesSeeder::class,
            RegionesSeeder::class,
            EstadosSeeder::class,
            MunicipiosSeeder::class,
            ParroquiasSeeder::class,
            TiposOficinasSeeder::class,
            TelegramasCatalogosSeeder::class,
            CodigosPostalesSeeder::class,
            CiudadesSeeder::class,
            SectoresSeeder::class,
            ServiciosOperativosSeeder::class,
            ClientesSeeder::class,
            EstatusOficinasSeeder::class,
            Parametro::class,
            OficinasSeeder::class,
            SemaforoPostalSeeder::class,
            RoleTipoOficinaSeeder::class,
            TiposSacasSeeder::class,
            TarifaExportaFacil::class,
            TarifaIposplusSeeder::class,
            DocumentoSeeder::class,
            InsumoSeeder::class,
            ApartadosSeeder::class,
            TipoContratoSeeder::class,
            OficinasVehiculosSeeder::class,
        ]);

        $this->call([
            TarifasNacionalesConceptos::class,
            TarifasInternacionalRangos::class,
            ServiciosFlotaSeeder::class,
        ]);
        $this->call([
            TarifasNacionalesRangos::class,
        ]);
        $this->call([
            TarifaExpresoBolivariano::class,
            TarifasInternacionalConceptos::class,
            ProductoSeeder::class,
        ]);
        $this->call([
            Proveedor::class,
        ]);
        $this->call([
            DashboardTableSeeder::class,
        ]);
        $this->call([
            RoleSeeder::class,
            OficinaPersonalSeeder::class,
        ]);

        $this->call([
            UserSeeder::class,
            UserEstadoSeeder::class,
        ]);

        $this->call([
            EnviosEstatusSeeder::class,
            TipoVehiculoSeeder::class,
            // Vehiculos::class,
            // Ruta::class,
            // Viaje::class,
        ]);
        $this->call([
            // SacaSeeder::class,
            // EnviosSeeder::class,
            // EnvioAlmacenSeeder::class,
            DocumentoApostillaSeeder::class,
            ServicioPublicoSeeder::class,
            AlianzaRecaudacionSeeder::class,
            ZonaEconomicaOficinaSeeder::class,
            CarrerasEstudioSeeder::class,
            NivelEducativoSeeder::class,
            ParentescoSeeder::class,
            NacionalidadSeeder::class,
            EstadoCivilSeeder::class,
            TiposDiscapacidadesSeeder::class,
            InstitucionSeeder::class,
        ]);

        // Recolectas (app de clientes)
        $this->call([
            RecolectaEstatusSeeder::class,
            RecolectaParametroSeeder::class,
            RecolectaPermisoSeeder::class,
        ]);
    }
}
