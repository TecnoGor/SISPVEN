<?php

namespace Database\Seeders;

use App\Models\Oficina;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class OficinasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        $oficinas = [

            //CENTRALIZADORAS
            ["codigo" => "CO001", "oficina_relacionada_id" => 1, "nombre" => "Centralizadora Mérida", "tipo_oficina_id" =>	5, "jefe_oficina" => 'N/A', "correo" => "N/A",
            "direccion" => "Calle 21 Entre Avenida 4 Y 5, Edif. De Telecomunicaciones, Parroquia El Sagrario, Municipio Libertador, Mérida,  Estado Mérida.",
            "telefono" => 04245555555, "codigo_ubicacion" => 5101, "estado_id" => 5, "municipio_id" => 55, "parroquia_id" =>	226, "zona_economica_especial" => false,
            "latitud" => "N/A", "longitud" => "N/A", "estatus_id" => 1,  "operaciones" => true, "created_at" => now()],

            ["codigo" => "CO002", "oficina_relacionada_id" => 1, "nombre" => "Centralizadora Lara", "tipo_oficina_id" => 5, "jefe_oficina" => 'N/A', "correo" => "N/A",
            "direccion" => "Sector Centro Carrera 17 Entre Calles 24 Y 25 Edificio Nacional Planta Baja, Parroquia Catedral, Municipio Iribarren. Estado Lara.",
            "telefono" => 04245555555, "codigo_ubicacion" => 3001, "estado_id" => 9, "municipio_id" => 130, "parroquia_id" => 435,	 "zona_economica_especial" => false,
            "latitud" => "N/A",	"longitud" => "N/A", "estatus_id" => 1,  "operaciones" => true,	"created_at" => now()],

            ["codigo" => "CO003", "oficina_relacionada_id" => 1, "nombre" => "Centralizadora Carabobo", "tipo_oficina_id" => 5, "jefe_oficina" => 'N/A', "correo" => "N/A",
            "direccion" => "Centro De Valencia, Calle Colombia, Entre Díaz Moreno Y Montes De Oca, N° 101-33, Municipio Valencia, Estado Carabobo.",
            "telefono" => 04245555555, "codigo_ubicacion" => 2001, "estado_id" => 7, "municipio_id" => 120, "parroquia_id" => 409,	 "zona_economica_especial" => false,
            "latitud" => "N/A",	"longitud" => "N/A", "estatus_id" => 1,  "operaciones" => true,	"created_at" => now()],

            ["codigo" => "CO004", "oficina_relacionada_id" => 1, "nombre" => "Centralizadora DTTO Capital", "tipo_oficina_id" =>	5, "jefe_oficina" => 'N/A', "correo" => "N/A",
            "direccion" => "Av. José Ángel Lamas, San Martin, Centro Postal De Caracas, Parroquia San Juan, Municipio Libertador.",
            "telefono" => 04245555555, "codigo_ubicacion" => 1020, "estado_id" => 1, "municipio_id" => 1, "parroquia_id" => 12,  "zona_economica_especial" => false,
            "latitud" => "N/A",	"longitud" => "N/A", "estatus_id" => 1,  "operaciones" => true,	"created_at" => now()],

            ["codigo" => "CO005", "oficina_relacionada_id" => 1, "nombre" => "Centralizadora Maracaibo", "tipo_oficina_id" =>	5, "jefe_oficina" => 'N/A', "correo" => "N/A",
            "direccion" => "Calle 98 Con Av. 3, N° 2a-18, Edificio Los Gemelos, Antigua Guardia Nacional, Diagonal A Traki, Parroquia Bolívar, Maracaibo, Estado Zulia.",
            "telefono" => 04245555555, "codigo_ubicacion" => 4001, "estado_id" => 17, "municipio_id" => 	239, "parroquia_id" => 780,	 "zona_economica_especial" => false,
            "latitud" => "N/A",	"longitud" => "N/A", "estatus_id" => 1,  "operaciones" => true,	"created_at" => now()],


            //COP
            ["codigo" => "CP001", "oficina_relacionada_id" => 1, "nombre" => "COP Barinas", "tipo_oficina_id" => 4, "jefe_oficina" => 'N/A', "correo" => "N/A",
            "direccion" => "Ipostel, Calle Carvajal Entre Libertad Y  Montilla, Edificio Miguez, Nro.4-47, Planta Baja. Parroquia. Centro, Municipio.  Barinas, Barinas Edo. Barinas. Venezuela.",
            "telefono" => 04245555555, "codigo_ubicacion" => 5201, "estado_id" => 3, "municipio_id" => 23, "parroquia_id" => 77, "zona_economica_especial" => false,
            "latitud" => "N/A",	"longitud" => "N/A", "estatus_id" => 1,  "operaciones" => true,	"created_at" => now()],

            ["codigo" => "CP002", "oficina_relacionada_id" => 1, "nombre" => "COP Mérida ", "tipo_oficina_id" => 4, "jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Calle 21 Entre Avenida 4 Y 5, Edif. De Telecomunicaciones, Parroquia El Sagrario, Municipio Libertador, Mérida,  Estado Mérida.",
            "telefono" => 04245555555,	"codigo_ubicacion" => 5101, "estado_id" => 5,	"municipio_id" => 55, "parroquia_id" => 226, "zona_economica_especial" => false,
            "latitud" => "N/A", "longitud" => "N/A", "estatus_id" => 1,  "operaciones" => true, "created_at" => now()],

            ["codigo" => "CP003", "oficina_relacionada_id" => 1, "nombre" => "COP San Cristobal", "tipo_oficina_id" => 4, "jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Edificio Nacional Calle 5 Entre Carreras 2 Y 3, Sector Catedral,  Municipio San Cristóbal  Parroquia: San Sebastián Edo. Táchira.",
            "telefono" => 04245555555,	"codigo_ubicacion" => 5001, "estado_id" => 6,	"municipio_id" => 78, "parroquia_id" => 311, "zona_economica_especial" => false,
            "latitud" => "N/A", "longitud" => "N/A", "estatus_id" => 1,  "operaciones" => true, "created_at" => now()],

            ["codigo" => "CP004", "oficina_relacionada_id" => 1, "nombre" => "COP Valera",	"tipo_oficina_id" => 4,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Avenida 11 Esquina Calle 7, Edificio  Telecomunicaciones, Planta Baja, Diagonal Plaza Bolívar. Parroquia Mercedes Díaz. Municipio Valera Estado Trujillo",
            "telefono" => 04245555555,	"codigo_ubicacion" => 3101, "estado_id" => 4,	"municipio_id" => 36, "parroquia_id" => 138, "zona_economica_especial" => false,
            "latitud" => "N/A", "longitud" => "N/A", "estatus_id" => 1,  "operaciones" => true, "created_at" => now()],

            ["codigo" => "CP005", "oficina_relacionada_id" => 1, "nombre" => "Centro Postal Caracas",	"tipo_oficina_id" => 4,	"jefe_oficina" => "N/A", "correo" => "N/A", "direccion" => "Av. José Ángel Lamas, San Martin, Centro Postal De Caracas, Parroquia San Juan, Municipio Libertador.",
            "telefono" => 04245555555,	"codigo_ubicacion" => 1020, "estado_id" => 1,	"municipio_id" => 1, "parroquia_id" => 12, "zona_economica_especial" => false,
            "latitud" => "N/A", "longitud" => "N/A", "estatus_id" => 1,  "operaciones" => true, "created_at" => now()],

            ["codigo" => "CP006", "oficina_relacionada_id" => 1, "nombre"=> "COP San Carlos", "tipo_oficina_id" => 4,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Calle Salias Entre Calle Carabobo Y Calle Miranda, Al Lado De Electrónica Macapo, Sector Banco Obrero.",
            "telefono" => 04245555555, "codigo_ubicacion" => 2201,	"estado_id" => 8, "municipio_id" => 128, "parroquia_id" => 426,	"zona_economica_especial" => false,
            "latitud" => "N/A", "longitud" => "N/A", "estatus_id" => 1,  "operaciones" => true,	"created_at" => now()],

            ["codigo" => "CP007", "oficina_relacionada_id" => 1, "nombre"=> "COP Acarigua", "tipo_oficina_id" => 4,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Calle 31 / Av. 38 Y 39 Frente Al C.C. Acarigua Municipio Páez Parroquia Ramon Peraza Estado Portuguesa.",
            "telefono" => 04245555555, "codigo_ubicacion" => 2201, "estado_id" => 10,	"municipio_id" => 146,	"parroquia_id" => 513,	"zona_economica_especial" => false,
            "latitud" => "N/A", "longitud" => "N/A", "estatus_id" => 1,  "operaciones" => true,	"created_at" => now()],

            ["codigo" => "CP008", "oficina_relacionada_id" => 1, "nombre"=> "COP Barquisimeto", "tipo_oficina_id" => 4,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Sector Centro Carrera 17 Entre Calles 24 Y 25 Edificio Nacional Planta Baja, Parroquia Catedral, Municipio Iribarren. Estado Lara.",
            "telefono" => 04245555555, "codigo_ubicacion" => 3001,	"estado_id" => 9 , "municipio_id" => 130,	"parroquia_id" => 431,	"zona_economica_especial" => false,
            "latitud" => "N/A", "longitud" => "N/A", "estatus_id" => 1,  "operaciones" => true,	"created_at" => now()],

            ["codigo" => "CP009", "oficina_relacionada_id" => 1, "nombre"=> "COP San Felipe", "tipo_oficina_id" => 4,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Av. 7 Entre Calles 11 Y 12 Edif Rental Planta Baja, Estado Yaracuy.",
            "telefono" => 04245555555, "codigo_ubicacion" => 3201,	"estado_id" => 11,	"municipio_id" => 163,	"parroquia_id" => 550,	"zona_economica_especial" => false,
            "latitud" => "N/A", "longitud" => "N/A", "estatus_id" => 1,  "operaciones" => true,	"created_at" => now()],

            ["codigo" => "CP010", "oficina_relacionada_id" => 1, "nombre"=> "COP Valencia", "tipo_oficina_id" => 4,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Centro De Valencia, Calle Colombia, Entre Díaz Moreno Y Montes De Oca, N° 101-33, Municipio Valencia, Estado Carabobo.",
            "telefono" => 04245555555, "codigo_ubicacion" => 2001, "estado_id" => 7, "municipio_id" => 120, "parroquia_id" => 405,	"zona_economica_especial" => false,
            "latitud" => "N/A", "longitud" => "N/A", "estatus_id" => 1,  "operaciones" => true,	"created_at" => now()],

            ["codigo" => "CP011", "oficina_relacionada_id" => 1, "nombre"=> "COP San Fernando de Apure", "tipo_oficina_id" => 4, "jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Calle 24 De Julio Entre Calle Sucre Y Bolívar Edificio Lara Planta Baja. Parroquia San Fernando De Apure.",
            "telefono" => 04245555555, "codigo_ubicacion" => 7001,	"estado_id" => 13,	"municipio_id" => 174,	"parroquia_id" => 579,	"zona_economica_especial" => false,
            "latitud" => "N/A", "longitud" => "N/A", "estatus_id" => 1,  "operaciones" => true,	"created_at" => now()],

            ["codigo" => "CP012", "oficina_relacionada_id" => 1, "nombre"=> "COP Puerto Ayacucho", "tipo_oficina_id" => 4,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Av. Aeropuerto Sector Los Lirios, Galpones Nudes, 1er Piso Arriba De La Escuela De La Chocolatería.",
            "telefono" => 04245555555, "codigo_ubicacion" => 7101,	"estado_id" => 12,	"municipio_id" => 167,	"parroquia_id" => 557,	"zona_economica_especial" => false,
            "latitud" => "N/A", "longitud" => "N/A", "estatus_id" => 1,  "operaciones" => true,	"created_at" => now()],

            ["codigo" => "CP013", "oficina_relacionada_id" => 1, "nombre"=> "COP Maracay", "tipo_oficina_id" => 4,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "AV. 19 DE ABRIL CALLE SOUBLETTE C/C BOYACÁ C. C. 19 DE ABRIL LOCAL C-28 MCPIO. GIRARDOT PARROQ. MADRE MARIA DE SAN JOSÉ MARACAY EDO. ARAGUA",
            "telefono" => 04245555555,	"codigo_ubicacion" => 2101,	"estado_id" => 14,	"municipio_id" => 181,	"parroquia_id" => 609,	"zona_economica_especial" => false,
            "latitud" => "N/A", "longitud" => "N/A", "estatus_id" => 1,  "operaciones" => true,	"created_at" => now()],

            ["codigo" => "CP014", "oficina_relacionada_id" => 1, "nombre"=> "COP San Juan de los Morros", "tipo_oficina_id" => 4, "jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Avenida Bolívar, N° 92 Esquina De Los Bancos, Frente Al Banco Caroní, Municipio Juan German Roció, Parroquia San Juan",
            "telefono" => 04245555555, "codigo_ubicacion" => 2301,	"estado_id" => 15,	"municipio_id" => 199,	"parroquia_id" => 654,	"zona_economica_especial" => false,
            "latitud" => "N/A", "longitud" => "N/A", "estatus_id" => 1,  "operaciones" => true,	"created_at" => now()],

            ["codigo" => "CP015", "oficina_relacionada_id" => 1, "nombre"=> "COP Coro", "tipo_oficina_id" => 4,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Calle Ampies, Edif. Santa Rosa, Casa De Las Cien Ventanas.",
            "telefono" => 04245555555, "codigo_ubicacion" => 4101,	"estado_id" => 16,	"municipio_id" => 214,	"parroquia_id" => 695,	"zona_economica_especial" => false,
            "latitud" => "N/A", "longitud" => "N/A", "estatus_id" => 1,  "operaciones" => true,	"created_at" => now()],

            ["codigo" => "CP016", "oficina_relacionada_id" => 1, "nombre"=> "COP Maracaibo", "tipo_oficina_id" => 4,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Calle 98 Con Av. 3, N° 2a-18, Edificio Los Gemelos, Antigua Guardia Nacional, Diagonal A Traki, Parroquia Bolívar, Maracaibo, Estado Zulia.",
            "telefono" => 04245555555, "codigo_ubicacion" => 4001,	"estado_id" => 17,	"municipio_id" => 239, "parroquia_id" => 776,	"zona_economica_especial" => false,
            "latitud" => "N/A", "longitud" => "N/A", "estatus_id" => 1,  "operaciones" => true,	"created_at" => now()],

            ["codigo" => "CP017", "oficina_relacionada_id" => 1, "nombre" => "COP Barcelona", "tipo_oficina_id" => 4,	"jefe_oficina" =>"N/A", "correo" => "N/A",
            "direccion" => "Boulevard Bolívar Chavez. Edificio Telecomunicaciones, Al Lado De Cantv. Municipio, Bolívar. Barcelona, Estado Anzoátegui.",
            "telefono" => 04245555555, "codigo_ubicacion" => "6001", "estado_id" => 18,	"municipio_id" => 260, "parroquia_id" => 880, "zona_economica_especial" => false,
            "latitud" => "N/A",	"longitud" => "N/A",	"estatus_id" => 1,  "operaciones" => true,	"created_at" => now()],

            ["codigo" => "CP018", "oficina_relacionada_id" => 1, "nombre" => "COP Puerto Ordaz", "tipo_oficina_id" => 4, "jefe_oficina" =>"N/A", "correo" => "N/A",
            "direccion" => "Av.  Bolivia, Urb. Villa Colombia, Edif. Nazareth, Al Lado De La Policlínica Guayana, Puerto Ordaz, Edo. Bolívar.",
            "telefono" => 04245555555, "codigo_ubicacion" => "8050", "estado_id" => 19,	"municipio_id" => 287, "parroquia_id" => 961, "zona_economica_especial" => false,
            "latitud" => "N/A",	"longitud" => "N/A",	"estatus_id" => 1,  "operaciones" => true,	"created_at" => now()],

            ["codigo" => "CP019", "oficina_relacionada_id" => 1, "nombre" => "COP Tucupita", "tipo_oficina_id" => 4, "jefe_oficina" =>"N/A", "correo" => "N/A",
            "direccion" => "Calle Patevilca Al Lado Del Consejo Legislativo, Tucupita Edo. Delta Amacuro.",
            "telefono" => 04245555555, "codigo_ubicacion" => "6401", "estado_id" => 20,	"municipio_id" => 295, "parroquia_id" => 992, "zona_economica_especial" => false,
            "latitud" => "N/A",	"longitud" => "N/A",	"estatus_id" => 1,  "operaciones" => true,	"created_at" => now()],

            ["codigo" => "CP020", "oficina_relacionada_id" => 1, "nombre" => "COP Maturin", "tipo_oficina_id" => 4,	"jefe_oficina" =>"N/A", "correo" => "N/A",
            "direccion" => "Av Bolivar Entre Calle Azcue, Rojas Y Miranda, Edificio De Telecomunicaciones (Cantv) Pb, Parroquia San Simon.",
            "telefono" => 04245555555, "codigo_ubicacion" => "6201", "estado_id" => 21,	"municipio_id" => 303, "parroquia_id" => 1024, "zona_economica_especial" => false,
            "latitud" => "N/A",	"longitud" => "N/A",	"estatus_id" => 1,  "operaciones" => true,	"created_at" => now()],

        	["codigo" => "CP021", "oficina_relacionada_id" => 1, "nombre" => "COP Porlamar", "tipo_oficina_id" => 4, "jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Edificio Postal Calle Maneiro Entre Boulevar Gómez Y Calle Fraternidad, Edificio Ipostel.",
            "telefono" => 04245555555, "codigo_ubicacion" => "6301", "estado_id" => 22, "municipio_id" => 313, "parroquia_id" => 1050, "zona_economica_especial" => false,
            "latitud" => "N/A",	"longitud" => "N/A", "estatus_id" => 1,  "operaciones" => true,	"created_at" => now()],

            ["codigo" => "CP022", "oficina_relacionada_id" => 1, "nombre" => "COP Cumaná", "tipo_oficina_id" => 4, "jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Calle Paraíso, Edificio Telecomunicaciones PB., Diagonal al Teatro Luis Mariano Rivera, Parroquia Santa Inés, Municipio Sucre, Cumana Estado Sucre, ZP 6101.",
            "telefono" => 04245555555, "codigo_ubicacion" => "6101", "estado_id" => 23, "municipio_id" => 320, "parroquia_id" => 1068, "zona_economica_especial" => false,
            "latitud" => "N/A",	"longitud" => "N/A", "estatus_id" => 1,  "operaciones" => true,	"created_at" => now()],


            //OPT
            ["codigo" => 'OP001', "oficina_relacionada_id" => 6, "nombre"=> 'PUERTO AYACUCHO', "tipo_oficina_id" => 3, "jefe_oficina" => 'N/A', "correo" => 'oficina1@gmail.com',
             "direccion" => 'N/A', "telefono" => 04245555555, "codigo_ubicacion" => 7101,
             "estado_id" => 12, "municipio_id" => 167, "parroquia_id" => '562', "zona_economica_especial" => true, "latitud" => 5.668128, "longitud" => -67.627693 , "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => 'OP002', "oficina_relacionada_id" => 6, "nombre"=> 'EL VALLE', "tipo_oficina_id" => 3, "jefe_oficina" => 'N/A', "correo" => 'oficina2@gmail.com',
            "direccion" => 'N/A', "telefono" => 04245555555, "codigo_ubicacion" => 1090,
            "estado_id" => 1, "municipio_id" => 1, "parroquia_id" => '19', "zona_economica_especial" => true, "latitud" => 10.457916, "longitud" => -66.917793 , "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP003", "oficina_relacionada_id" => 10, "nombre" => "Modulo U.C.V", "tipo_oficina_id" => 	3, "jefe_oficina" => "N/A", "correo" => 'N/A',
            "direccion" => "Facultad De Ingeniería Sala De Lectura Sociales U.C.V. Parroquia San Pedro Municipio Libertador, Distrito Capital.", "telefono" => 04245555555, "codigo_ubicacion" =>	1053,
            "estado_id" =>	1, "municipio_id" => 1, "parroquia_id" => 20, "zona_economica_especial" => true, "latitud" => "10.490776", "longitud" => "-66.88898",	"estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

        	["codigo" => "OP004", "oficina_relacionada_id" => 10, "nombre" => "OPT Caricuao", "tipo_oficina_id" => 	3, "jefe_oficina" => "N/A", "correo" => 'N/A',
            "direccion" => "Av. Principal De Ruiz Pineda, Redoma Ruiz Pineda, Al Lado De La Panadería Coripan, Parroquia Caricuao, Distrito Capital.", "telefono" => 04245555555, "codigo_ubicacion" =>	1000,
            "estado_id" =>	1, "municipio_id" => 1, "parroquia_id" => 2, "zona_economica_especial" => true, "latitud" => "10.434138", "longitud" => "-67.000388",	"estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

         	["codigo" => "OP005", "oficina_relacionada_id" => 10, "nombre" => "OPT Carmelitas", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => 'N/A',
            "direccion" => "Av. Urdaneta, Esquina De Carmelitas.", "telefono" => 04245555555, "codigo_ubicacion" =>	1012,
            "estado_id" =>	1, "municipio_id" => 1, "parroquia_id" => 9, "zona_economica_especial" => true, "latitud" => "10.507851", "longitud" => "-66.916076",	"estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

        	["codigo" => "OP006", "oficina_relacionada_id" => 10, "nombre" => "OPT El Valle", "tipo_oficina_id" => 	3, "jefe_oficina" => "N/A", "correo" => 'N/A',
            "direccion" => "Av. Intercomunal, Entre Calle 11 Y Calle 14, Edif. 28 Gury, Parroquia El Valle, Distrito Capital.", "telefono" => 04245555555, "codigo_ubicacion" =>	1090,
            "estado_id" =>	1, "municipio_id" => 1, "parroquia_id" => 19, "zona_economica_especial" => true, "latitud" => "10.457916", "longitud" => "-66.917793",	"estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP007", "oficina_relacionada_id" => 10, "nombre" => "OPT La Candelaria", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => 'N/A',
            "direccion" => "Av. Urdaneta, Centro Cívico, Pb, Plaza La Candelaria, Parroquia. La Candelaria.", "telefono" => 04245555555, "codigo_ubicacion" =>	1011,
            "estado_id" =>	1, "municipio_id" => 1, "parroquia_id" => 7, "zona_economica_especial" => true, "latitud" => "10.505746", "longitud" => "-66.904253",	"estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

         	["codigo" => "OP008", "oficina_relacionada_id" => 10, "nombre" => "OPT Propatria", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => 'N/A',
            "direccion" => "Av. Principal De Pro patria C.C. Pro patria, Nivel 04,Local 04-10, Local 04-11 y Local 04-17, Parroquia Sucre, Municipio Libertador.", "telefono" => 04245555555, "codigo_ubicacion" =>	1030,
            "estado_id" =>	1, "municipio_id" => 1, "parroquia_id" => 16, "zona_economica_especial" => true, "latitud" => "10.503973", "longitud" => "-66.952272",	"estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

         	["codigo" => "OP009", "oficina_relacionada_id" => 10, "nombre" => "OPT Sabana Grande", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => 'N/A',
            "direccion" => "C.C. Cediaz, Av. Casanova; Parroquia El Recreo, Municipio Libertador, Distrito Capital.", "telefono" => 04245555555, "codigo_ubicacion" =>	1050,
            "estado_id" =>	1, "municipio_id" => 1, "parroquia_id" => 21, "zona_economica_especial" => true, "latitud" => "10.492971", "longitud" => "-66.878381",	"estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

         	["codigo" => "OP010", "oficina_relacionada_id" => 10, "nombre" => "OPT San Martin", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => 'N/A',
            "direccion" => "Av. José Ángel Lamas, San Martin, Centro Postal De Caracas, Parroquia San Juan, Municipio Libertador.", "telefono" => 04245555555, "codigo_ubicacion" =>	1020,
            "estado_id" =>	1, "municipio_id" => 1, "parroquia_id" => 12, "zona_economica_especial" => true, "latitud" => "10.497408", "longitud" => "-66.933288",	"estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP011", "oficina_relacionada_id" => 10, "nombre" => "OPT Chacao", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => 'N/A',
            "direccion" => "Av. Blandin La Castellana Antiguo. Banco de Maracaibo, La Castellana, al lado de Centro Comercial San Ignacio. Municipio Chacao Estado Miranda.", "telefono" => "04245555555",	"codigo_ubicacion" => 1060,
            "estado_id" => 2, "municipio_id" =>	2,	"parroquia_id" => 23, "zona_economica_especial" => true, "latitud" =>	"10.497802", "longitud" => "-66.854185", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP013", "oficina_relacionada_id" => 10, "nombre" => "OPT Charallave", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => 'N/A',
            "direccion" => "Calle José Ramón Figuera, Centro Comercial Tamanaco Tuy, Pb. Local 067, Charallave. Municipio Cristóbal Rojas, Estado Miranda.", "telefono" => "04245555555",	"codigo_ubicacion" => 1210,
            "estado_id" => 2, "municipio_id" =>	10,	"parroquia_id" => 45, "zona_economica_especial" => true, "latitud" =>	"10.241777", "longitud" => "-66.858039", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP014", "oficina_relacionada_id" => 10, "nombre" => "OPT Carrizal", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => 'N/A',
            "direccion" => "C.C. Don Pedro, Pb, Local B, Carretera Panamericana, Km 20, Sector Corralito. Municipio Carrizal, Estado Miranda.", "telefono" => "04245555555",	"codigo_ubicacion" => 1203,
            "estado_id" => 2, "municipio_id" =>	7,	"parroquia_id" => 39, "zona_economica_especial" => true, "latitud" =>	"10.353632", "longitud" => "-67.004062", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP015", "oficina_relacionada_id" => 10, "nombre" => "OPT Caucagua", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => 'N/A',
            "direccion" => "Boulevard Ángel González Con Calle Comercio, Local 4 Y 5, Frente A Biblioteca Adolfo Castillo, Estado Miranda.", "telefono" => "04245555555",	"codigo_ubicacion" => 1246,
            "estado_id" => 2, "municipio_id" =>	22,	"parroquia_id" => 69, "zona_economica_especial" => true, "latitud" =>	"10.280434", "longitud" => "-66.373499", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP016", "oficina_relacionada_id" => 10, "nombre" => "OPT Cua", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => 'N/A',
            "direccion" => "Calle San Rafael, Res El Progreso, Local 3 Cua. Municipio Urdaneta, Estado Miranda.", "telefono" => "04245555555",	"codigo_ubicacion" => 1211,
            "estado_id" => 2, "municipio_id" =>	11,	"parroquia_id" => 47, "zona_economica_especial" => true, "latitud" =>	"10.160798", "longitud" => "-66.887346", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP017", "oficina_relacionada_id" => 10, "nombre" => "OPT Centro Comercial Ciudad Tamanaco", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => 'N/A',
            "direccion" => "C.C.C. Tamanaco Nivel C2 Sector Yarey Chuao.", "telefono" => "04245555555",	"codigo_ubicacion" => 1064,
            "estado_id" => 2, "municipio_id" =>	2,	"parroquia_id" => 23, "zona_economica_especial" => true, "latitud" =>	"10.485819", "longitud" => "-66.854923", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP018", "oficina_relacionada_id" => 10, "nombre" => "OPT Guarenas", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => 'N/A',
            "direccion" => "Av. 4 Sector 1, Urb. Trapichito Al Lado Del Seguro Social De Guarenas, Estado. Miranda.", "telefono" => "04245555555",	"codigo_ubicacion" => 1220,
            "estado_id" => 2, "municipio_id" =>	15,	"parroquia_id" => 54, "zona_economica_especial" => true, "latitud" =>	"10.469316", "longitud" => "-66.608049", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP019", "oficina_relacionada_id" => 10, "nombre" => "OPT Guatire", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => 'N/A',
            "direccion" => "Centro De Guatire Calle Rivas N° 16, Subiendo Por La Casa De Acción Democrática, Guatire Estado Miranda.", "telefono" => "04245555555",	"codigo_ubicacion" => 1221,
            "estado_id" => 2, "municipio_id" =>	16,	"parroquia_id" => 55, "zona_economica_especial" => true, "latitud" =>	"10.472691", "longitud" => "-66.541574", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP020", "oficina_relacionada_id" => 10, "nombre" => "OPT Higuerote", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => 'N/A',
            "direccion" => "Calle Los Leones, con tercera calle, Centro Comercial Centinela. Pb Local Nro 8. Municipio Brion.", "telefono" => "04245555555",	"codigo_ubicacion" => 1231,
            "estado_id" => 2, "municipio_id" =>	18,	"parroquia_id" => 58, "zona_economica_especial" => true, "latitud" =>	"10.476613", "longitud" => "-66.10028", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP021", "oficina_relacionada_id" => 10, "nombre" => "OPT Los Ruices", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => 'N/A',
            "direccion" => "Av. Francisco de Miranda, Edf. Los Ruices, Local A1 y A2, Frente al elevado Los Ruices, Municipio Sucre, Estado Miranda.", "telefono" => "04245555555",	"codigo_ubicacion" => 1071,
            "estado_id" => 2, "municipio_id" =>	4,	"parroquia_id" => 28, "zona_economica_especial" => true, "latitud" =>	"10.491496", "longitud" => "-66.828459", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP022", "oficina_relacionada_id" => 10, "nombre" => "OPT Los Teques", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => 'N/A',
            "direccion" => "Calle Paéz N° 15 Antigua Sede Seguros Progreso, Los Teques. Estado Miranda.", "telefono" => "04245555555",	"codigo_ubicacion" => 1201,
            "estado_id" => 2, "municipio_id" =>	6,	"parroquia_id" => 33, "zona_economica_especial" => true, "latitud" =>	"10.34838", "longitud" => "-67.044305", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP023", "oficina_relacionada_id" => 10, "nombre" => "OPT Ocumare del Tuy", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => 'N/A',
            "direccion" => "Calle Urdaneta, Residencia. Parque Central, Locales 16 Y 17, Municipio Tomas Lander, Estado Miranda.", "telefono" => "04245555555",	"codigo_ubicacion" => 1209,
            "estado_id" => 2, "municipio_id" =>	9,	"parroquia_id" => 42, "zona_economica_especial" => true, "latitud" =>	"10.120169", "longitud" => "-66.774642", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP024", "oficina_relacionada_id" => 10, "nombre" => "OPT Petare", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => 'N/A',
            "direccion" => "Calle Capitolio, Centro Empresarial Giorgio, Oficina Anexa, Primer Nivel, Estado Miranda.", "telefono" => "04245555555",	"codigo_ubicacion" => 1073,
            "estado_id" => 2, "municipio_id" =>	4,	"parroquia_id" => 26, "zona_economica_especial" => true, "latitud" =>	"10.478017", "longitud" => "-66.808188", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

        	["codigo" => "OP025", "oficina_relacionada_id" => 10, "nombre" => "OPT Carayaca", "tipo_oficina_id" => 3,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "N/A", "telefono" => "04245555555", "codigo_ubicacion" => 1167,
            "estado_id" => 24, "municipio_id" => 335, "parroquia_id" => 1131, "zona_economica_especial" => true, "latitud" => "10.530678", "longitud" => "-67.120511", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP026", "oficina_relacionada_id" => 10, "nombre" => "OPT Catia la Mar", "tipo_oficina_id" => 3,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "N/A", "telefono" => "04245555555", "codigo_ubicacion" => 1162,
            "estado_id" => 24, "municipio_id" => 335, "parroquia_id" => 1127, "zona_economica_especial" => true, "latitud" => "10.601451", "longitud" => "-67.032427", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP027", "oficina_relacionada_id" => 10, "nombre" => "OPT La Guaira", "tipo_oficina_id" => 3,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "N/A", "telefono" => "04245555555", "codigo_ubicacion" => 1160,
            "estado_id" => 24, "municipio_id" => 335, "parroquia_id" => 1123, "zona_economica_especial" => true, "latitud" => "10.598891", "longitud" => "-66.934569", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP028", "oficina_relacionada_id" => 10, "nombre" => "OPT Maiquetia", "tipo_oficina_id" => 3,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "N/A", "telefono" => "04245555555", "codigo_ubicacion" => 1161,
            "estado_id" => 24, "municipio_id" => 335, "parroquia_id" => 1124, "zona_economica_especial" => true, "latitud" => "10.595425", "longitud" => "-66.950167", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

        	["codigo" => "OP029", "oficina_relacionada_id" => 17, "nombre" => "OPT Puerto Ayacucho", "tipo_oficina_id" =>	3, "jefe_oficina" => "N/A", "correo" => 'N/A',
            "direccion" => "Av. Aeropuerto Sector Los Lirios, Galpones Nudes, 1er Piso Arriba De La Escuela De La Chocolatería.", "telefono" => "04245555555", "codigo_ubicacion" => "7101",
            "estado_id" => 12, "municipio_id" => 167, "parroquia_id" => 558, "zona_economica_especial" => true, "latitud" => "5.668128", "longitud" => "-67.627693", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP030", "oficina_relacionada_id" => 22, "nombre" => "OPT Barcelona", "tipo_oficina_id" =>	3, "jefe_oficina" => "N/A", "correo" => 'N/A',
            "direccion" => "Boulevard Bolívar Chavez. Edificio Telecomunicaciones, Al Lado De Cantv. Municipio, Bolívar. Barcelona, Estado Anzoátegui.", "telefono" => "04245555555", "codigo_ubicacion" => "6001",
            "estado_id" => 18, "municipio_id" => 260, "parroquia_id" => 879, "zona_economica_especial" => true, "latitud" => "9.4545430", "longitud" => "-64.8301950", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP031", "oficina_relacionada_id" => 22, "nombre" => "OPT Boca de Uchire", "tipo_oficina_id" =>	3, "jefe_oficina" => "N/A", "correo" => 'N/A',
            "direccion" => "Avenida Principal, Nro. 4-59, A Una Cuadra Del Ateneo, Parroquia Boca De Uchire. Municipio San Juan De Capistrano. Edo. Anzoategui / Avenida Principal Con Calle  Peñalver, Nro. 2-30 , Parroquia Boca De Uchire.", "telefono" => "04245555555", "codigo_ubicacion" => "6005",
            "estado_id" => 18, "municipio_id" => 263, "parroquia_id" => 889, "zona_economica_especial" => true, "latitud" => "10.126734", "longitud" => "-65.427755", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP032", "oficina_relacionada_id" => 22, "nombre" => "OPT Cantaura", "tipo_oficina_id" =>	3, "jefe_oficina" => "N/A", "correo" => 'N/A',
            "direccion" => "Calle Bolívar. Municipio Freites. Cantaura, Estado Anzoátegui /  Avenida Bolivar, Con Calle Santander Nro. 101, Parroquia Cantaura.", "telefono" => "04245555555", "codigo_ubicacion" => "6007",
            "estado_id" => 18, "municipio_id" => 264, "parroquia_id" => 890, "zona_economica_especial" => true, "latitud" => "9.307392", "longitud" => "-64.361372", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP033", "oficina_relacionada_id" => 22, "nombre" => "OPT Clarines", "tipo_oficina_id" =>	3, "jefe_oficina" => "N/A", "correo" => 'N/A',
            "direccion" => "Avenida Principal Rafael Fernández Padilla, Municipio Manuel Bruzual. Clarines, Estado Anzoátegui / Final Avenida Fernandez Padilla,  Parroquia Bruzual.", "telefono" => "04245555555", "codigo_ubicacion" => "6008",
            "estado_id" => 18, "municipio_id" => 265, "parroquia_id" => 893, "zona_economica_especial" => true, "latitud" => "9.937718", "longitud" => "-65.164956", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP034", "oficina_relacionada_id" => 22, "nombre" => "OPT Pariaguan", "tipo_oficina_id" =>	3, "jefe_oficina" => "N/A", "correo" => 'N/A',
            "direccion" => "Sede Del Concejo Municipal De Pariaguán, Municipio Francisco De Miranda. Estado Anzoátegui. / Calle Arismendi, Sede Concejo Municipal, Parroquia Pariaguan.", "telefono" => "04245555555", "codigo_ubicacion" => "6052",
            "estado_id" => 18, "municipio_id" => 278, "parroquia_id" => 922, "zona_economica_especial" => true, "latitud" => "8.8371240", "longitud" => "-64.7231670", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP035", "oficina_relacionada_id" => 22, "nombre" => "OPT Puerto la Cruz", "tipo_oficina_id" =>	3, "jefe_oficina" => "N/A", "correo" => 'N/A',
            "direccion" => "CALLE FREITES CON CALLE HONDURAS, EDIFICIO DALPA, PLANTA BAJA, PUERTO LA CRUZ, ANZOATEGUI.", "telefono" => "04245555555", "codigo_ubicacion" => "6023",
            "estado_id" => 18, "municipio_id" => 273, "parroquia_id" => 910, "zona_economica_especial" => true, "latitud" => "10.223464", "longitud" => "-64.634809", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP036", "oficina_relacionada_id" => 16, "nombre" => "OPT Achaguas", "tipo_oficina_id" =>	3, "jefe_oficina" => "N/A", "correo" => 'N/A',
            "direccion" => "Calle José Angel Montenegro,  C/calle Junin, en la Sede de la Prefectura, Achaguas, Edo. Apure.", "telefono" => "04245555555", "codigo_ubicacion" => "7002",
            "estado_id" => 13, "municipio_id" => 175, "parroquia_id" => 583, "zona_economica_especial" => true, "latitud" => "7.7762370", "longitud" => "-68.2264930", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP037", "oficina_relacionada_id" => 16, "nombre" => "OPT Guasdualito", "tipo_oficina_id" =>	3, "jefe_oficina" => "N/A", "correo" => 'N/A',
            "direccion" => "Calle Sucre, frente al grupo escolar Aramendi Edif. Consejo Municipal.", "telefono" => "04245555555", "codigo_ubicacion" => "5063",
            "estado_id" => 13, "municipio_id" => 179, "parroquia_id" => 597, "zona_economica_especial" => true, "latitud" => "7.2442460", "longitud" => "-70.7295580", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP038", "oficina_relacionada_id" => 16, "nombre" => "OPT Mantecal", "tipo_oficina_id" =>	3, "jefe_oficina" => "N/A", "correo" => 'N/A',
            "direccion" => "Carrera N° 3, con calle N° 5 Mantecal Edo. Apure.", "telefono" => "04245555555", "codigo_ubicacion" => "7010",
            "estado_id" => 13, "municipio_id" => 177, "parroquia_id" => 594, "zona_economica_especial" => true, "latitud" => "7.5763470", "longitud" => "-69.1521780", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP039", "oficina_relacionada_id" => 16, "nombre" => "OPT San Fernando de Apure", "tipo_oficina_id" =>	3, "jefe_oficina" => "N/A", "correo" => 'N/A',
            "direccion" => "Calle 24 De Julio Entre Calle Sucre Y Bolívar Edificio Lara Planta Baja. Parroquia San Fernando De Apure.", "telefono" => "04245555555", "codigo_ubicacion" => "7001",
            "estado_id" => 13, "municipio_id" => 174, "parroquia_id" => 579, "zona_economica_especial" => true, "latitud" => "7.8928790", "longitud" => "-67.4710520", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP040", "oficina_relacionada_id" => 18, "nombre" => "OPT Barrio 19 de Abril", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => 'N/A',
            "direccion" => "CALLE LIBERTADOR C/C RÓMULO GALLEGOS SECTOR SOROCAIMA 2 (AMBULATORIO SOROCAIMA MÓDULO DE SERVICIO) PARROQ. SAMÁN DE GUERE MCPIO. SANTIAGO MARIÑO TURMERO ", "telefono" => "04245555555", "codigo_ubicacion" => 2107,
            "estado_id" => 14, "municipio_id" => 184, "parroquia_id" =>	621, "zona_economica_especial" => true, "latitud" => "10.2253850", "longitud" => "-67.5138250", "estatus_id" =>	1, "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP041", "oficina_relacionada_id" => 18, "nombre" => "OPT Cagua", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => 'N/A',
            "direccion" => "CALLE SAN JUAN ENTRE FROILÁN CORREA Y RONDON (AL LADO DE TRAKI) PARROQ. SUCRE MCPIO. SUCRE CAGUA EDO. ARAGUA", "telefono" => "04245555555", "codigo_ubicacion" => 2122,
            "estado_id" => 14, "municipio_id" => 191, "parroquia_id" =>	636, "zona_economica_especial" => true, "latitud" => "10.1863840", "longitud" => "-67.4614600", "estatus_id" =>	1, "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP042", "oficina_relacionada_id" => 18, "nombre" => "OPT Choroni", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => 'N/A',
            "direccion" => "N/A", "telefono" => "04245555555", "codigo_ubicacion" => 2110,
            "estado_id" => 14, "municipio_id" => 181, "parroquia_id" =>	611, "zona_economica_especial" => true, "latitud" => "10.4949820", "longitud" => "-67.6126650", "estatus_id" =>	1, "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP043", "oficina_relacionada_id" => 18, "nombre" => "OPT El Limon", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => 'N/A',
            "direccion" => "AV. 10 SECTOR JOSÉ FELIX RIVAS URB. JOSÉ FELIX RIVAS C.C. MELBUMAR NAVE A LOCAL 62764166 PARROQ. CAÑA DE AZÚCAR MCPIO. MARIO BRICEÑO IRAGORRI EL LIMÓN EDO. ARAGUA", "telefono" => "04245555555", "codigo_ubicacion" => 2105,
            "estado_id" => 14, "municipio_id" => 183, "parroquia_id" =>	616, "zona_economica_especial" => true, "latitud" => "10.2970890", "longitud" => "-67.6315590", "estatus_id" =>	1, "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP044", "oficina_relacionada_id" => 18, "nombre" => "OPT La Morita", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => 'N/A',
            "direccion" => "AV. FCO. DE MIRANDA, EDF. SEDE ALCALDIA  MCPIO. FCO. LINARES ALCANTARA PARROQ. STA. RITA SECTOR 18 DE MAYO EDO. ARAGUA", "telefono" => "04245555555", "codigo_ubicacion" => 2106,
            "estado_id" => 14, "municipio_id" => 182, "parroquia_id" =>	612, "zona_economica_especial" => true, "latitud" => "10.2142490", "longitud" => "-67.5531790", "estatus_id" =>	1, "operaciones" => false, "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP045", "oficina_relacionada_id" => 18, "nombre" => "OPT Las Acacias", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => 'N/A',
            "direccion" => "AV. PPAL. DE LAS ACACIAS CALLE B C. C. DE LAS ACACIAS, FRENTE AL BLOQUE 45, PARROQ. JOAQUÍN CRESPO MCPIO. GIRARDOT MARACAY EDO. ARAGUA", "telefono" => "04245555555", "codigo_ubicacion" => 2103,
            "estado_id" => 14, "municipio_id" => 181, "parroquia_id" =>	606, "zona_economica_especial" => true, "latitud" => "10.2428580", "longitud" => "-67.5841440", "estatus_id" =>	1, "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP046", "oficina_relacionada_id" => 18, "nombre" => "OPT Las Tejerias", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => 'N/A',
            "direccion" => "AV. PPAL. DE LAS ACACIAS CALLE B C. C. DE LAS ACACIAS, FRENTE AL BLOQUE 45, PARROQ. JOAQUÍN CRESPO MCPIO. GIRARDOT MARACAY EDO. ARAGUA", "telefono" => "04245555555", "codigo_ubicacion" => 2119,
            "estado_id" => 14, "municipio_id" => 198, "parroquia_id" =>	652, "zona_economica_especial" => true, "latitud" => "10.2547960", "longitud" => "-67.1731780", "estatus_id" =>	1, "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP047", "oficina_relacionada_id" => 18, "nombre" => "OPT Maracay", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => 'N/A',
            "direccion" => "AV. 19 DE ABRIL CALLE SOUBLETTE C/C BOYACÁ C. C. 19 DE ABRIL LOCAL C-28 MCPIO. GIRARDOT PARROQ. MADRE MARIA DE SAN JOSÉ MARACAY EDO. ARAGUA", "telefono" => "04245555555", "codigo_ubicacion" => 2101,
            "estado_id" => 14, "municipio_id" => 181, "parroquia_id" =>	605, "zona_economica_especial" => true, "latitud" => "10.2322690", "longitud" => "-67.5546360", "estatus_id" =>	1, "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP048", "oficina_relacionada_id" => 18, "nombre" => "OPT Palo Negro", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => 'N/A',
            "direccion" => "CALLE GRAN DEMOCRATA, C/C SUCRE, SECTOR CENTRO 1, Nº 7-1, INSTALACIONES DEL REGISTRO CIVIL, ANTIGUA SEDE DE LA ALCALDIA DEL MUNICIPIO LIBERTADOR.", "telefono" => "04245555555", "codigo_ubicacion" => 2117,
            "estado_id" => 14, "municipio_id" => 193, "parroquia_id" =>	640, "zona_economica_especial" => true, "latitud" => "10.1713500", "longitud" => "-67.5464030", "estatus_id" =>	1, "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP049", "oficina_relacionada_id" => 18, "nombre" => "OPT Turmero", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => 'N/A',
            "direccion" => "CALLE MIRANDA C/C CAILO TORRES # 22 ESQ. BCO. BICENTENARIO (DIAGONAL A LA PLAZA BOLÍVAR, FTE. AL INSTITUTO ESCUELA T.U.F.) PARROQ. SANTIAGO MARIÑO MCPIO. SANTIAGO ", "telefono" => "04245555555", "codigo_ubicacion" => 2115,
            "estado_id" => 14, "municipio_id" => 184, "parroquia_id" =>	617, "zona_economica_especial" => true, "latitud" => "10.2266270", "longitud" => "-67.4717610", "estatus_id" =>	1, "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP050", "oficina_relacionada_id" => 18, "nombre" => "OPT Villa de Cura", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => 'N/A',
            "direccion" => "CALLE COMERCIO C/C DR. RANGEL SECTOR CENTRO LOCAL # 4 MCPIO. ZAMORA PARROQ. SAN LUIS REY VILLA DE CURA EDO. ARAGUA", "telefono" => "04245555555", "codigo_ubicacion" => 2126,
            "estado_id" => 14, "municipio_id" => 190, "parroquia_id" =>	631, "zona_economica_especial" => true, "latitud" => "10.0337520", "longitud" => "-67.4814050", "estatus_id" =>	1, "operaciones" => false,  "created_at" => now()],

            ["codigo" => "OP051", "oficina_relacionada_id" => 6,   "nombre" => "OPT Barinas", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => 'N/A',
            "direccion" => "Ipostel, Calle Carvajal Entre Libertad Y  Montilla, Edificio Miguez, Nro.4-47, Planta Baja. Parroquia. Centro, Municipio.  Barinas, Barinas Edo. Barinas. Venezuela.", "telefono" => "04245555555", "codigo_ubicacion" => "5201",
            "estado_id" => 3,	"municipio_id" => 23,	"parroquia_id" => 77,	"zona_economica_especial" => true, "latitud" =>	"8.6317000", "longitud" => "-70.2142760", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP052", "oficina_relacionada_id" => 6,   "nombre" => "OPT Barinitas", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => 'N/A',
            "direccion" => "Ipostel, Carrera 7, Con Calle 6, Local Biblioteca Pública, Barinitas Mun. Bolívar Edo Barinas.", "telefono" => "04245555555", "codigo_ubicacion" => "5206",
            "estado_id" => 3,	"municipio_id" => 29,	"parroquia_id" => 108,	"zona_economica_especial" => true, "latitud" =>	"8.7602390", "longitud" => "-70.4115610", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP053", "oficina_relacionada_id" => 6,   "nombre" => "OPT Libertad de Barinas", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => 'N/A',
            "direccion" => "Ipostel Calle Guzmán Blanco, Barrio El Cementerio.  Parroquia. Libertad, Municipio Rojas, Libertad De Barinas,  Estado Barinas.", "telefono" => "04245555555", "codigo_ubicacion" => "5220",
            "estado_id" => 3,	"municipio_id" => 28,	"parroquia_id" => 104,	"zona_economica_especial" => true, "latitud" =>	"8.3271680", "longitud" => "-69.6309840", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP054", "oficina_relacionada_id" => 6,   "nombre" => "OPT Sabaneta", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => 'N/A',
            "direccion" => "Ipostel, Calle Libertador,  Centro Comercial El Cine Parroquia. Sabaneta,  Municipio. Alberto Arvelo Torrealba,   Sabaneta Edo Barinas.", "telefono" => "04245555555", "codigo_ubicacion" => "5224",
            "estado_id" => 3,	"municipio_id" => 24,	"parroquia_id" => 91,	"zona_economica_especial" => true, "latitud" =>	"8.7566690", "longitud" => "-69.9372880", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP055", "oficina_relacionada_id" => 6,   "nombre" => "OPT Socopo", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => 'N/A',
            "direccion" => "Ipostel, Sede Terminal De Pasajeros, Local 01. Parroquia. Socopo.  Municipio, Antonio José De Sucre, Socopo Estado Barinas.", "telefono" => "04245555555", "codigo_ubicacion" => "5216",
            "estado_id" => 3,	"municipio_id" => 26,	"parroquia_id" => 97,	"zona_economica_especial" => true, "latitud" =>	"8.2366030", "longitud" => "-70.8190910", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP056", "oficina_relacionada_id" => 23,  "nombre" => "OPT Ciudad Bolivar", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => 'N/A',
            "direccion" => "Av. Táchira, Casa Nº 10, Al Lado De La Cámara Municipal, Ciudad Bolívar, Edo. Bolívar.", "telefono" => "04245555555", "codigo_ubicacion" => "8001",
            "estado_id" => 19,	"municipio_id" => 281,	"parroquia_id" => 934,	"zona_economica_especial" => true, "latitud" =>	"8.1326010", "longitud" => "-63.5433300", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP057", "oficina_relacionada_id" => 23,  "nombre" => "OPT Puerto Ordaz", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => 'N/A',
            "direccion" => "Av.  Bolivia, Urb. Villa Colombia, Edif. Nazareth, Al Lado De La Policlinica Guayana, Puerto Ordaz, Edo. Bolívar.", "telefono" => "04245555555", "codigo_ubicacion" => "8050",
            "estado_id" => 19,	"municipio_id" => 287,	"parroquia_id" => 961,	"zona_economica_especial" => true, "latitud" =>	"8.3071740", "longitud" => "-62.7162520", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP058", "oficina_relacionada_id" => 23,  "nombre" => "OPT San Felix", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => 'N/A',
            "direccion" => "La Unidad Carrera Mariño Mariño, Sector La Unidad, Antigua Sede Del Mtc, San Felix, Edo. Bolívar.", "telefono" => "04245555555", "codigo_ubicacion" => "8051",
            "estado_id" => 19,	"municipio_id" => 287,	"parroquia_id" => 964,	"zona_economica_especial" => true, "latitud" =>	"8.3643980", "longitud" => "-62.6540430", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

        	["codigo" => "OP060", "oficina_relacionada_id" => 15, "nombre" => "OPT Bejuma", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => 'N/A',
            "direccion" => " Edificio Municipal, Calle Sucre, Frente A La Plaza Bolívar, Edificio Municipal, Municipio Bejuma, Estado Carabobo.", "telefono" =>	"04245555555", "codigo_ubicacion" => 2040,
            "estado_id" => 7, "municipio_id" => 107, "parroquia_id" => 373, "zona_economica_especial" => true, "latitud" =>	"10.1734380", "longitud" => "-68.2610800", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP061", "oficina_relacionada_id" => 15, "nombre" => "OPT Chirgua", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => 'N/A',
            "direccion" => "Calle Dinamarca Cruce Con Carabobo, Casa Nº 10, Municipio Chirgua, Estado Carabobo.", "telefono" =>	"04245555555", "codigo_ubicacion" => 2044,
            "estado_id" => 7, "municipio_id" => 107, "parroquia_id" => 375, "zona_economica_especial" => true, "latitud" =>	"10.2064900", "longitud" => "-68.1843420", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP062", "oficina_relacionada_id" => 15, "nombre" => "OPT El Morro", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => 'N/A',
            "direccion" => "Av. 79-A El Morro Ii, Segunda Etapa Diagonal Al Centro Comercial El Morro. Plaza Valencia. Edo. Carabobo.", "telefono" =>	"04245555555", "codigo_ubicacion" => 2006,
            "estado_id" => 7, "municipio_id" => 118, "parroquia_id" => 401, "zona_economica_especial" => true, "latitud" =>	"10.2191750", "longitud" => "-67.9706600", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP063", "oficina_relacionada_id" => 15, "nombre" => "OPT El Trigal", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => 'N/A',
            "direccion" => "N/A", "telefono" =>	"04245555555", "codigo_ubicacion" => 2001,
            "estado_id" => 7, "municipio_id" => 120, "parroquia_id" => 409, "zona_economica_especial" => true, "latitud" =>	"10.2123130", "longitud" => "-68.0002320", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP064", "oficina_relacionada_id" => 15, "nombre" => "OPT Guacara", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => 'N/A',
            "direccion" => "Centro De Guacara, Calle Márquez Del Toro, Entre Urdaneta Y Mariño, N° 108, Municipio Guacara, Estado Carabobo.", "telefono" =>	"04245555555", "codigo_ubicacion" => 2015,
            "estado_id" => 7, "municipio_id" => 110, "parroquia_id" => 382, "zona_economica_especial" => true, "latitud" =>	"10.2294980", "longitud" => "-67.8773080", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP065", "oficina_relacionada_id" => 15, "nombre" => "OPT Guigue", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => 'N/A',
            "direccion" => "Calle Sucre, Entre Ávila Y Pichincha, Frente A La Comandancia De La Policía De Carabobo, Municipio Guigue, Guigue, Estado Carabobo. ", "telefono" =>	"04245555555", "codigo_ubicacion" => 2010,
            "estado_id" => 7, "municipio_id" => 108, "parroquia_id" => 376, "zona_economica_especial" => true, "latitud" =>	"10.0849090", "longitud" => "-67.7846950", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP066", "oficina_relacionada_id" => 15, "nombre" => "OPT Los Guayos", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => 'N/A',
            "direccion" => " Calle Sucre, Entre Zamora Y Bruzual, N° 17, Sede Del Consejo Municipal, Municipio Los Guayos, Los Guayos, Estado Carabobo.", "telefono" =>	"04245555555", "codigo_ubicacion" => 2011,
            "estado_id" => 7, "municipio_id" => 113, "parroquia_id" => 389, "zona_economica_especial" => true, "latitud" =>	"10.1887410", "longitud" => "-67.9378680", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP067", "oficina_relacionada_id" => 15, "nombre" => "OPT Montalban", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => 'N/A',
            "direccion" => "Avenida Pérez Carreño, Cruce Con Avenida Bolívar, Frente A La Plaza Bolívar, Sede De La Alcaldía, Municipio Montalbán, Estado Carabobo.", "telefono" =>	"04245555555", "codigo_ubicacion" => 2042,
            "estado_id" => 7, "municipio_id" => 115, "parroquia_id" => 391, "zona_economica_especial" => true, "latitud" =>	"10.2151340", "longitud" => "-68.3295200", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP068", "oficina_relacionada_id" => 15, "nombre" => "OPT Morón ", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => 'N/A',
            "direccion" => "Calle Miranda, Frente A La Plaza Bolívar Y Al Lado De La Contraloría, Morón, Municipio Juan José Mora, Estado Carabobo.", "telefono" =>	"04245555555", "codigo_ubicacion" => 2051,
            "estado_id" => 7, "municipio_id" => 111, "parroquia_id" => 385, "zona_economica_especial" => true, "latitud" =>	"10.4890960", "longitud" => "-68.1998690", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP069", "oficina_relacionada_id" => 15, "nombre" => "OPT Puerto Cabello ", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => 'N/A',
            "direccion" => " Zona Colonial, Paso El Malecon, Callejón De Jesús, N° 01, Municipio Puerto Cabello, Estado Carabobo.", "telefono" =>	"04245555555", "codigo_ubicacion" => 2050,
            "estado_id" => 7, "municipio_id" => 117, "parroquia_id" => 400, "zona_economica_especial" => true, "latitud" =>	"10.4801090", "longitud" => "-68.0099620", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP070", "oficina_relacionada_id" => 15, "nombre" => "OPT San Joaquín ", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => 'N/A',
            "direccion" => "Barrio Palo Negro, Calle Zulia, Entre Miranda Y Briceño Mendez, Casa De La Cultura, Municipio San Joaquín, Estado Carabobo.", "telefono" =>	"04245555555", "codigo_ubicacion" => 2018,
            "estado_id" => 7, "municipio_id" => 119, "parroquia_id" => 402, "zona_economica_especial" => true, "latitud" =>	"10.2642530", "longitud" => "-68.7992990", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP071", "oficina_relacionada_id" => 15, "nombre" => "OPT Valencia", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => 'N/A',
            "direccion" => "Centro De Valencia, Calle Colombia, Entre Díaz Moreno Y Montes De Oca, N° 101-33, Municipio Valencia, Estado Carabobo.", "telefono" =>	"04245555555", "codigo_ubicacion" => 2001,
            "estado_id" => 7, "municipio_id" => 120, "parroquia_id" => 405, "zona_economica_especial" => true, "latitud" =>	"10.1819350", "longitud" => "-68.0050690", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

        	["codigo" => "OP072", "oficina_relacionada_id" => 11, "nombre" => "OPT San Carlos", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Calle Salias Entre Calle Carabobo Y Calle Miranda, Al Lado De Electrónica Macapo, Sector Banco Obrero.", "telefono" => "04245555555", "codigo_ubicacion" => "2201",
            "estado_id" => 8,	"municipio_id" => 128, "parroquia_id" => 426, "zona_economica_especial" => true, "latitud" =>"9.6607950", "longitud" => "-68.5875750", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP073", "oficina_relacionada_id" => 11, "nombre" => "OPT Tinaquillo", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "N/A", "telefono" => "04245555555", "codigo_ubicacion" => "2209",
            "estado_id" => 8,	"municipio_id" => 122, "parroquia_id" => 415, "zona_economica_especial" => true, "latitud" =>"9.9133140", "longitud" => "-68.3094520", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP074", "oficina_relacionada_id" => 20, "nombre" => "OPT Borojo", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Calle Federación, Casa De La Junta Parroquial, instalaciones de la Alcaldía del Municipio.", "telefono" => "04245555555", "codigo_ubicacion" => "4121",
            "estado_id" => 16,	"municipio_id" => 236, "parroquia_id" => 769, "zona_economica_especial" => true, "latitud" =>"11.0663070", "longitud" => "-70.8067030", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP075", "oficina_relacionada_id" => 20, "nombre" => "OPT Capatarida", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Calle Principal, Sector Centro.", "telefono" => "04245555555", "codigo_ubicacion" => "4117",
            "estado_id" => 16,	"municipio_id" => 236, "parroquia_id" => 767, "zona_economica_especial" => true, "latitud" =>"11.1714650", "longitud" => "-70.6188830", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP076", "oficina_relacionada_id" => 20, "nombre" => "OPT Churuguara", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Sector El Cerrito calle San Rafael entre calle Mariño y calle Sucre, Centro de Coordinación Policial N° 11", "telefono" => "04245555555", "codigo_ubicacion" => "4152",
            "estado_id" => 16,	"municipio_id" => 229, "parroquia_id" => 743, "zona_economica_especial" => true, "latitud" =>"10.8123050", "longitud" => "-69.5416740", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP077", "oficina_relacionada_id" => 20, "nombre" => "OPT Dabajuro", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Calle Buchivacoa, Al Lado De La Antigua Policia en las instalaciones de la Alcaldía.", "telefono" => "04245555555", "codigo_ubicacion" => "4118",
            "estado_id" => 16,	"municipio_id" => 237, "parroquia_id" => 773, "zona_economica_especial" => true, "latitud" =>"11.0156390", "longitud" => "-70.6736150", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP078", "oficina_relacionada_id" => 20, "nombre" => "OPT La Vela", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Calle Briceño Sede de la Alcaldía del Municipio Edif. Del Registro Civil, La Vela de Coro", "telefono" => "04245555555", "codigo_ubicacion" => "4132",
            "estado_id" => 16,	"municipio_id" => 217, "parroquia_id" => 713, "zona_economica_especial" => true, "latitud" =>"11.4593010", "longitud" => "-69.5690080", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP079", "oficina_relacionada_id" => 20, "nombre" => "OPT Mirimire", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Av. Bolivar en las instalaciones del Registro Municipal.", "telefono" => "04245555555", "codigo_ubicacion" => "4110",
            "estado_id" => 16,	"municipio_id" => 223, "parroquia_id" => 731, "zona_economica_especial" => true, "latitud" =>"11.1575480", "longitud" => "-68.7251010", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP080", "oficina_relacionada_id" => 20, "nombre" => "OPT Pedregal", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Calle Max De Leon Sector Centro en las instalaciones de la Alcaldía del Municipio.", "telefono" => "04245555555", "codigo_ubicacion" => "4137",
            "estado_id" => 16,	"municipio_id" => 232, "parroquia_id" => 754, "zona_economica_especial" => true, "latitud" =>"11.0190420", "longitud" => "-70.1208880", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP081", "oficina_relacionada_id" => 20, "nombre" => "OPT Pueblo Nuevo de Paraguana", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Municipio Falcon, Calle Falcón N 39, Frente A La Plaza Bolivar.", "telefono" => "04245555555", "codigo_ubicacion" => "4150",
            "estado_id" => 16,	"municipio_id" => 216, "parroquia_id" => 704, "zona_economica_especial" => true, "latitud" =>"11.9496670", "longitud" => "-69.9235270", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP082", "oficina_relacionada_id" => 20, "nombre" => "OPT Puerto Cumarebo", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Calle Industrial Al Lado De La Casa De La Cultura.", "telefono" => "04245555555", "codigo_ubicacion" => "4167",
            "estado_id" => 16,	"municipio_id" => 218, "parroquia_id" => 718, "zona_economica_especial" => true, "latitud" =>"11.4882780", "longitud" => "-69.3528670", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP083", "oficina_relacionada_id" => 20, "nombre" => "OPT Punto Fijo", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Calle Páez Con Panama N F11-19.", "telefono" => "04245555555", "codigo_ubicacion" => "4102",
            "estado_id" => 16,	"municipio_id" => 215, "parroquia_id" => 700, "zona_economica_especial" => true, "latitud" =>"11.6951800", "longitud" => "-70.2099680", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP084", "oficina_relacionada_id" => 20, "nombre" => "OPT Tucacas", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Municipio José Laurencio Silva, Calle Gil Casa N° 21.", "telefono" => "04245555555", "codigo_ubicacion" => "2055",
            "estado_id" => 16,	"municipio_id" => 221, "parroquia_id" => 726, "zona_economica_especial" => true, "latitud" =>"10.7918270", "longitud" => "-68.3205250", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP085", "oficina_relacionada_id" => 19, "nombre" => "OPT Altagracia de Orituco", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Av. Santa Teresa,Sector Guaqueries Sector Saladillo Al Lado De Setras, Diagonal A La Oficina De Obras Publicas De La Alcaldía, Parroquia Altagracia De Orituco.", "telefono" => "04245555555", "codigo_ubicacion" => "2320",
            "estado_id" => 15,	"municipio_id" => 207, "parroquia_id" => 672, "zona_economica_especial" => true, "latitud" =>"9.8666080", "longitud" => "-66.3820140", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP086", "oficina_relacionada_id" => 19, "nombre" => "OPT Calabozo", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Calle 6, con Esquina Carrera 14. Diagonal A La Clínica Pérez Guillen , Parroquia Calabozo, Municipio, Francisco De Miranda", "telefono" => "04245555555", "codigo_ubicacion" => "2312",
            "estado_id" => 15,	"municipio_id" => 200, "parroquia_id" => 657, "zona_economica_especial" => true, "latitud" =>"8.9330100", "longitud" => "-67.4292470", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP087", "oficina_relacionada_id" => 19, "nombre" => "OPT Camatagua", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Calle Comercio Alcaldía Camatagua", "telefono" => "04245555555", "codigo_ubicacion" => "2313",
            "estado_id" => 15,	"municipio_id" => 211, "parroquia_id" => 686, "zona_economica_especial" => true, "latitud" =>"8.2438360", "longitud" => "-67.6217370", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP088", "oficina_relacionada_id" => 19, "nombre" => "OPT Las Mercedes del Llano", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "CALLE PRICIPAL LASMERCEDES SEDE DE APRAMER, Las Mercedes del Llano Guarico", "telefono" => "04245555555", "codigo_ubicacion" => "2356",
            "estado_id" => 15,	"municipio_id" => 204, "parroquia_id" => 665, "zona_economica_especial" => true, "latitud" =>"9.1092460", "longitud" => "-66.3959430", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP089", "oficina_relacionada_id" => 19, "nombre" => "OPT San Juan de los Morros", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Avenida Bolívar, N° 92 Esquina De Los Bancos, Frente Al Banco Caroní, Municipio Juan German Roció, Parroquia San Juan", "telefono" => "04245555555", "codigo_ubicacion" => "2301",
            "estado_id" => 15,	"municipio_id" => 199, "parroquia_id" => 654, "zona_economica_especial" => true, "latitud" =>"9.9120410", "longitud" => "-67.3572320", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP090", "oficina_relacionada_id" => 19, "nombre" => "OPT Tucupido", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Final Calle Granate Casa Sin Numero, entre SAIME Y SENIFA, Tucupido.", "telefono" => "04245555555", "codigo_ubicacion" => "2330",
            "estado_id" => 15,	"municipio_id" => 205, "parroquia_id" => 668, "zona_economica_especial" => true, "latitud" =>"9.2717590", "longitud" => "-65.7722130", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP091", "oficina_relacionada_id" => 19, "nombre" => "OPT Zaraza", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Calle Troconis Con Calle Higuerote Centro Comercial Tarragona, Local N° 7", "telefono" => "04245555555", "codigo_ubicacion" => "2332",
            "estado_id" => 15,	"municipio_id" => 209, "parroquia_id" => 680, "zona_economica_especial" => true, "latitud" =>"9.3496010", "longitud" => "-65.3221310", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP092", "oficina_relacionada_id" => 13, "nombre" => "OPT Barquisimeto", "tipo_oficina_id" => 3,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Sector Centro Carrera 17 Entre Calles 24 Y 25 Edificio Nacional Planta Baja, Parroquia Catedral, Municipio Iribarren. Estado Lara.", "telefono" => "04245555555", "codigo_ubicacion" => "3001",
            "estado_id" => 9, "municipio_id" => 130, "parroquia_id" => 431,	"zona_economica_especial" => true, "latitud" => "10.0464670",   "longitud" => "-69.3583700", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP093", "oficina_relacionada_id" => 13, "nombre" => "OPT Cabudare", "tipo_oficina_id" => 3,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Calle Juan De Dios Ponte Con Juan De Dios Melean Y Domingo Méndez, Cabudare, Municipio Palavecino, Parroquia Cabudare, Estado. Lara. ", "telefono" => "04245555555", "codigo_ubicacion" => "3023",
            "estado_id" => 9, "municipio_id" => 133, "parroquia_id" => 455,	"zona_economica_especial" => true, "latitud" => "10.0319200",   "longitud" => "-69.2635020", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP094", "oficina_relacionada_id" => 13, "nombre" => "OPT Carora", "tipo_oficina_id" => 3,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Av. Bolívar, Entre 10  Y 11, Centro Comercial Don Cherra Pb, Carora, Estado. Lara, Municipio Torres, Parroquia Trinidad Samuel. ", "telefono" => "04245555555", "codigo_ubicacion" => "3050",
            "estado_id" => 9, "municipio_id" => 137, "parroquia_id" => 470,	"zona_economica_especial" => true, "latitud" => "10.1801850",   "longitud" => "-70.0864390", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP095", "oficina_relacionada_id" => 13, "nombre" => "OPT Duaca", "tipo_oficina_id" => 3,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Carrera 5 Entre Calles 9 Y 10, Edificio Poderes Públicos Parroquia José María Blanco, Duaca, Municipio Crespo Estado. Lara. ", "telefono" => "04245555555", "codigo_ubicacion" => "3025",
            "estado_id" => 9, "municipio_id" => 134, "parroquia_id" => 460,	"zona_economica_especial" => true, "latitud" => "10.2830670",   "longitud" => "-70.1623110", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP096", "oficina_relacionada_id" => 13, "nombre" => "OPT Humocaro Alto", "tipo_oficina_id" => 3,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Calle Comercio, Nro. 15, Humocaro Alto, Municipio Moran Parroquia Humocaro Alto Estado. Lara. ", "telefono" => "04245555555", "codigo_ubicacion" => "3058",
            "estado_id" => 9, "municipio_id" => 132, "parroquia_id" => 453,	"zona_economica_especial" => true, "latitud" => "9.6046860",    "longitud" => "-69.9850330", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP097", "oficina_relacionada_id" => 13, "nombre" => "OPT Humocaro Bajo", "tipo_oficina_id" => 3,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Calle Bolívar, Esquina Av. Principal, Junta Comunal, Humocaro Bajo, Municipio Moran Parroquia Humocaro Bajo Estado. Lara. ", "telefono" => "04245555555", "codigo_ubicacion" => "3058",
            "estado_id" => 9, "municipio_id" => 132, "parroquia_id" => 454,	"zona_economica_especial" => true, "latitud" => "9.6778990",    "longitud" => "-69.9713260", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP098", "oficina_relacionada_id" => 13, "nombre" => "MOD Aeropuerto", "tipo_oficina_id" => 3,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Avenida La Salle Con Avenida Jose Gil Fortoul, Aeropuerto Internacional, Barquisimeto, Estado Lara Municipio Iribarren, Parroquia Concepción. ", "telefono" => "04245555555", "codigo_ubicacion" => "3001",
            "estado_id" => 9, "municipio_id" => 130, "parroquia_id" => 434,	"zona_economica_especial" => true, "latitud" => "10.0464670",   "longitud" => "-69.3583700", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP099", "oficina_relacionada_id" => 13, "nombre" => "OPT Sanare", "tipo_oficina_id" => 3,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Calle Antonio José De Sucre Con Avs. Simón Bolivar Y Francisco De Miranda Edificio Municipal De La Alcaldia Del Municipio Andres Eloy Blanco Sanare, Parroquia Pio Tamayo Estado Lara.", "telefono" => "04245555555", "codigo_ubicacion" => "3028",
            "estado_id" => 9, "municipio_id" => 135, "parroquia_id" => 463,	"zona_economica_especial" => true, "latitud" => "9.7441290",    "longitud" => "-69.6599010", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP100", "oficina_relacionada_id" => 7, "nombre" => "OPT Ejido", "tipo_oficina_id" => 3,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Centro Comercial Matriz Plaza, Piso 1 Local 23, Calle Industria, Frente A La Plaza Bolívar Parroquia Matriz, Municipio Campo Elías, Ejido Mérida.", "telefono" => "04245555555", "codigo_ubicacion" => "5111",
            "estado_id" => 5, "municipio_id" => 70,	 "parroquia_id" => 283,	"zona_economica_especial" => true, "latitud" => "8.5414090",    "longitud" => "-71.2365010", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP101", "oficina_relacionada_id" => 7, "nombre" => "OPT La Vigía de Mérida ", "tipo_oficina_id" => 3,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Av. 16 Barrio San Isidro Edif. Don Jacinto Piso 2 S/N, Parroquia Rómulo Betancourt, Municipio Alberto Adriani, Mérida Estado Mérida.", "telefono" => "04245555555", "codigo_ubicacion" => "5145",
            "estado_id" => 5, "municipio_id" => 56,	 "parroquia_id" => 238,	"zona_economica_especial" => true, "latitud" => "8.6193940",    "longitud" => "-71.6507070", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP102", "oficina_relacionada_id" => 7, "nombre" => "OPT La Parroquia", "tipo_oficina_id" => 3,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Av. Bolívar Sede De La Prefectura Fte A La Plaza Bolívar, Parroquia Juan Rodríguez Suarez, Municipio Libertador, Mérida Estado Mérida.", "telefono" => "04245555555", "codigo_ubicacion" => "5115",
            "estado_id" => 5, "municipio_id" => 55,	 "parroquia_id" => 234,	"zona_economica_especial" => true, "latitud" => "8.559670"	,   "longitud" => "-71.2003740", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP103", "oficina_relacionada_id" => 7, "nombre" => "OPT Lagunillas de Merida", "tipo_oficina_id" => 3,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Calle La Puerta, Edif. Municipal Pb S/N Frente A La Plaza Bolívar. San Juan De Lagunillas, Parroquia San Juan, Municipio Sucre, Estado Mérida.", "telefono" => "04245555555", "codigo_ubicacion" => "5138",
            "estado_id" => 5, "municipio_id" => 65,	 "parroquia_id" => 262,	"zona_economica_especial" => true, "latitud" => "8.506033"	,   "longitud" => "-71.3882670", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP104", "oficina_relacionada_id" => 7, "nombre" => "OPT Libertad", "tipo_oficina_id" => 3,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Calle Cristóbal Colon, al lado del comando policial.", "telefono" => "04245555555", "codigo_ubicacion" => "5103",
            "estado_id" => 5, "municipio_id" => 69,	 "parroquia_id" => 277,	"zona_economica_especial" => true, "latitud" => "8.1801700", "longitud" => "-71.3916480", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP105", "oficina_relacionada_id" => 7, "nombre" => "MOD El Terminal", "tipo_oficina_id" => 3,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Terminal De Pasajeros Sur José Antonio Paredes. Av. Las Américas Parroquia Caracciolo Parra Municipio Libertador, Mérida Estado Mérida.", "telefono" => "04245555555", "codigo_ubicacion" => "5101",
            "estado_id" => 5, "municipio_id" => 55,	 "parroquia_id" => 225,	"zona_economica_especial" => true, "latitud" => "8.5856660", "longitud" => "-71.1689660", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP106", "oficina_relacionada_id" => 7, "nombre" => "OPT Mucuchies", "tipo_oficina_id" => 3,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Calle Miranda, Entre Avenida Independencia Y Bolívar Sede De La Biblioteca,  Dr. Raúl Giménez  A Media Cuadra De La Plaza Bolivar,Municipio Rangel Mucuchies Estado Mérida.", "telefono" => "04245555555", "codigo_ubicacion" => "5130",
            "estado_id" => 5, "municipio_id" => 60,	 "parroquia_id" => 248,	"zona_economica_especial" => true, "latitud" => "8.7491860", "longitud" => "-70.9224690", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP107", "oficina_relacionada_id" => 7, "nombre" => "OPT Pueblo Llano", "tipo_oficina_id" => 3,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Calle Providencia Entre Av. Campo Elías Y Sucre Casa N° 3-68 Al Lado De Distribuidora Santiago Loc.1, Municipio Pueblo Llano, Mérida Estado Mérida.", "telefono" => "04245555555", "codigo_ubicacion" => "5124",
            "estado_id" => 5, "municipio_id" => 59,	 "parroquia_id" => 247,	"zona_economica_especial" => true, "latitud" => "8.9152830", "longitud" => "-70.6589930", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP108", "oficina_relacionada_id" => 7, "nombre" => "OPT Santa Cruz de Mora", "tipo_oficina_id" => 3,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Calle Ayacucho Esq. Plaza Bolívar Edif. Municipal Sede De La Alcaldía Parroquia Capital Antonio Pinto Salinas, Municipio Antonio Pinto Salinas, Mérida Estado Mérida.", "telefono" => "04245555555", "codigo_ubicacion" => "5142",
            "estado_id" => 5, "municipio_id" => 67,	 "parroquia_id" => 270,	"zona_economica_especial" => true, "latitud" => "8.3887440", "longitud" => "-71.6396250", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP109", "oficina_relacionada_id" => 7, "nombre" => "OPT Santa Elena Arenales", "tipo_oficina_id" => 3,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Calle 3 Al Lado De Fontur,Parroquia Santa Elena De Arenales, Municipio Obispo Ramos De Lora, Mérida Estado Mérida.", "telefono" => "04245555555", "codigo_ubicacion" => "5146",
            "estado_id" => 5, "municipio_id" => 71,	 "parroquia_id" => 289,	"zona_economica_especial" => true, "latitud" => "8.3582800", "longitud" => "-71.4531390", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP110", "oficina_relacionada_id" => 7, "nombre" => "OPT Tabay", "tipo_oficina_id" => 3,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Calle Bolívar  Frente A La Plaza Bolívar . Casa N° 5-5,  Tabay,Municipio Santos Marquina, Mérida.", "telefono" => "04245555555", "codigo_ubicacion" => "5116",
            "estado_id" => 5, "municipio_id" => 62,	 "parroquia_id" => 255,	"zona_economica_especial" => true, "latitud" => "8.6315860", "longitud" => "-71.0785260", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP111", "oficina_relacionada_id" => 7, "nombre" => "OPT Tovar", "tipo_oficina_id" => 3,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Calle 7 Entre Carreras 4 Y 5 N° 4-30 A Media Cuadra De La Plaza Bolívar Parroquia Tovar, Municipio Tovar.", "telefono" => "04245555555", "codigo_ubicacion" => "5143",
            "estado_id" => 5, "municipio_id" => 63,	 "parroquia_id" => 256,	"zona_economica_especial" => true, "latitud" => "8.3310770", "longitud" => "-71.7542800", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP112", "oficina_relacionada_id" => 7, "nombre" => "OPT Zea", "tipo_oficina_id" => 3,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Calle Bolívar Edificio Municipal Planta Baja, Parroquia Capital Zea, Municipio Zea, Estado  Mérida.", "telefono" => "04245555555", "codigo_ubicacion" => "5144",
            "estado_id" => 5, "municipio_id" => 66,	 "parroquia_id" => 268,	"zona_economica_especial" => true, "latitud" => "8.3756220", "longitud" => "-71.7824790", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP113", "oficina_relacionada_id" => 7, "nombre" => "OPT Merida", "tipo_oficina_id" => 3,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Calle 21 Entre Avenida 4 Y 5, Edif. De Telecomunicaciones, Parroquia El Sagrario, Municipio Libertador, Mérida,  Estado Mérida.", "telefono" => "04245555555", "codigo_ubicacion" => "5101",
            "estado_id" => 5, "municipio_id" => 55,	 "parroquia_id" => 222,	"zona_economica_especial" => true, "latitud" => "8.5973330", "longitud" => "-71.1428400", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP114", "oficina_relacionada_id" => 25, "nombre" => "OPT Maturin", "tipo_oficina_id" =>	3,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Av Bolivar Entre Calle Azcue, Rojas Y Miranda, Edificio De Telecomunicaciones (Cantv) Pb, Parroquia San Simon.", "telefono" => "04245555555", "codigo_ubicacion" => "6201",
            "estado_id" => 21, "municipio_id" => 303, "parroquia_id" => 1024,	"zona_economica_especial" => true,	"latitud" => "9.7462830",   "longitud" => "-63.1813960", "estatus_id" => 1,  "operaciones" => false,	"created_at" => now()],

            ["codigo" => "OP115", "oficina_relacionada_id" => 25, "nombre" => "OPT Punta de Mata", "tipo_oficina_id" =>	3,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Calle Balmore Rodríguez, S/N, Detrás Del Terminal, Punta De Mata, Edo. Monagas.", "telefono" => "04245555555", "codigo_ubicacion" => "6217",
            "estado_id" => 21, "municipio_id" => 301, "parroquia_id" => 1014,	"zona_economica_especial" => true,	"latitud" => "9.6877110",   "longitud" => "-63.6044250", "estatus_id" => 1,  "operaciones" => false,	"created_at" => now()],

            ["codigo" => "OP116", "oficina_relacionada_id" => 26, "nombre" => "OPT Boca de Rio", "tipo_oficina_id" =>	3,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Edif. Centro Cívico, Calle Bolívar Con Miranda, Urb. Augusto Malave Villalba. Boca De Río", "telefono" => "04245555555", "codigo_ubicacion" => "6304",
            "estado_id" => 22, "municipio_id" => 317, "parroquia_id" => 1060,	"zona_economica_especial" => true,	"latitud" => "10.9682170",	"longitud" => "-64.1809780", "estatus_id" => 1,  "operaciones" => false,	"created_at" => now()],

            ["codigo" => "OP117", "oficina_relacionada_id" => 26, "nombre" => "OPT Juan Griego", "tipo_oficina_id" =>	3,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Av. Bolívar, Frente A La Antígua Cantv.", "telefono" => "04245555555", "codigo_ubicacion" => "6309",
            "estado_id" => 22, "municipio_id" => 316, "parroquia_id" => 1058,	"zona_economica_especial" => true,	"latitud" => "11.0795650",	"longitud" => "-63.9684410", "estatus_id" => 1,  "operaciones" => false,	"created_at" => now()],

            ["codigo" => "OP118", "oficina_relacionada_id" => 26, "nombre" => "OPT La Asuncion", "tipo_oficina_id" =>	3,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Calle Virgen Del Carmen, Frente Al Banco Bicentenario.", "telefono" => "04245555555", "codigo_ubicacion" => "6311",
            "estado_id" => 22, "municipio_id" => 310, "parroquia_id" => 1045,	"zona_economica_especial" => true,	"latitud" => "11.0282990",	"longitud" => "-63.8629780", "estatus_id" => 1,  "operaciones" => false,	"created_at" => now()],

            ["codigo" => "OP119", "oficina_relacionada_id" => 26, "nombre" => "OPT Pampatar", "tipo_oficina_id" =>	3,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Calle Joaquín Maneiro, Frente A Materiales La Rosa.", "telefono" => "04245555555", "codigo_ubicacion" => "6316",
            "estado_id" => 22, "municipio_id" => 315, "parroquia_id" => 1056,	"zona_economica_especial" => true,	"latitud" => "10.9965590",	"longitud" => "-63.8007710", "estatus_id" => 1,  "operaciones" => false,	"created_at" => now()],

            ["codigo" => "OP120", "oficina_relacionada_id" => 26, "nombre" => "OPT Porlamar", "tipo_oficina_id" =>	3,	 "jefe_oficina" =>"N/A", "correo" => "N/A",
            "direccion" => "Calle Joaquín Maneiro, Frente A Materiales La Rosa.", "telefono" => "04245555555", "codigo_ubicacion" => "6301",
            "estado_id" => 22, "municipio_id" => 313, "parroquia_id" => 1050,	"zona_economica_especial" => true,	"latitud" => "10.9526790",	"longitud" => "-63.8488650", "estatus_id" => 1,  "operaciones" => false,	"created_at" => now()],

            ["codigo" => "OP121", "oficina_relacionada_id" => 12, "nombre" => "OPT Chabasquen", "tipo_oficina_id" =>	3,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Avenida 17 de diciembre Con Plaza Bolívar Casa Nº 04 Municipio Unda Estado Portuguesa.", "telefono" => "04245555555", "codigo_ubicacion" => "3357",
            "estado_id" => 10, "municipio_id" => 144, "parroquia_id" => 508,   "zona_economica_especial" => true,	"latitud" => "1",	"longitud" => "1", "estatus_id" => 1,  "operaciones" => false,	"created_at" => now()],

            ["codigo" => "OP122", "oficina_relacionada_id" => 12, "nombre" => "OPT Santa Rosalia", "tipo_oficina_id" =>	3,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Av. Bolívar Entre Calles 1 Y 2 Frente A La Plaza Bolívar Local Alcaldía Del Municipio Santa Rosalia, Parroquia El Playon Estado Portuguesa.", "telefono" => "04245555555", "codigo_ubicacion" => "3307",
            "estado_id" => 10, "municipio_id" => 150, "parroquia_id" => 524,   "zona_economica_especial" => true,	"latitud" => "9.1000680",   "longitud" => "-69.0431710", "estatus_id" => 1,  "operaciones" => false,	"created_at" => now()],

            ["codigo" => "OP123", "oficina_relacionada_id" => 12, "nombre" => "OPT Billa Bruzual", "tipo_oficina_id" =>	3,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Av. 3 Con Calle 4 Parroquia Villa Bruzual Sede Antiguo Escuela Angel Rivas Buldeys Municipio Turen Estado Portuguesa.", "telefono" => "04245555555", "codigo_ubicacion" => "3309",
            "estado_id" => 10, "municipio_id" => 152, "parroquia_id" => 532,   "zona_economica_especial" => true,	"latitud" => "9.3314690",   "longitud" => "-69.1174770", "estatus_id" => 1,  "operaciones" => false,	"created_at" => now()],

            ["codigo" => "OP124", "oficina_relacionada_id" => 27, "nombre" => "OPT Carupano", "tipo_oficina_id" =>	3,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Calle Carabobo, Con Quebrada Honda, Edif. Telecomunicaciones, PB., Nro. 6, Parroquia Santa Rosa, Municipio Bermúdez, Carúpano, Cumana, Estado Sucre, Z. P. 6150.", "telefono" => "04245555555", "codigo_ubicacion" => "6150",
            "estado_id" => 23, "municipio_id" => 326, "parroquia_id" => 1092,	"zona_economica_especial" => true,	"latitud" => "10.6674400",	"longitud" => "-63.2461560", "estatus_id" => 1,  "operaciones" => false,	"created_at" => now()],

            ["codigo" => "OP125", "oficina_relacionada_id" => 27, "nombre" => "OPT Casanay", "tipo_oficina_id" =>	3,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Calle Perú, Nro. 14, Parroquia Mariño, Municipio Andrés Eloy Blanco, Casanay, Cumana, Estado Sucre, Z.P. 6168.", "telefono" => "04245555555", "codigo_ubicacion" => "6168",
            "estado_id" => 23, "municipio_id" => 334, "parroquia_id" => 1121,	"zona_economica_especial" => true,	"latitud" => "10.5022300",	"longitud" => "-63.4143520", "estatus_id" => 1,  "operaciones" => false,	"created_at" => now()],

            ["codigo" => "OP126", "oficina_relacionada_id" => 27, "nombre" => "OPT Cumana", "tipo_oficina_id" =>	3,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Calle Paraíso, Edificio Telecomunicaciones PB., Diagonal al Teatro Luis Mariano Rivera, Parroquia Santa Inés, Municipio Sucre, Cumana Estado Sucre, ZP 6101.", "telefono" => "04245555555", "codigo_ubicacion" => "6101",
            "estado_id" => 23, "municipio_id" => 320, "parroquia_id" => 1068,	"zona_economica_especial" => true,	"latitud" => "10.4640520",	"longitud" => "-63.1743040", "estatus_id" => 1,  "operaciones" => false,	"created_at" => now()],

            ["codigo" => "OP127", "oficina_relacionada_id" => 27, "nombre" => "OPT El Pilar", "tipo_oficina_id" =>	3,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Calle Beapertuy, Al Lado de los Bomberos, Parroquia El Pilar, Municipio Benítez, El Pilar, Cumana, Estado Sucre, Z.P. 6152. ", "telefono" => "04245555555", "codigo_ubicacion" => "6152",
            "estado_id" => 23, "municipio_id" => 327, "parroquia_id" => 1094,	"zona_economica_especial" => true,	"latitud" => "10.5483380",	"longitud" => "-63.1533450", "estatus_id" => 1,  "operaciones" => false,	"created_at" => now()],

            ["codigo" => "OP128", "oficina_relacionada_id" => 27, "nombre" => "OPT Mariguitar", "tipo_oficina_id" =>	3,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Calle Miranda N° 18, Parroquia Mariguita, Municipio Bolívar, Mariguitar, Cumana, Estado Sucre, Z.P. 6107.", "telefono" => "04245555555", "codigo_ubicacion" => "6107",
            "estado_id" => 23, "municipio_id" => 323, "parroquia_id" => 1082,	"zona_economica_especial" => true,	"latitud" => "10.4478960",	"longitud" => "-63.9007980", "estatus_id" => 1,  "operaciones" => false,	"created_at" => now()],

            ["codigo" => "OP129", "oficina_relacionada_id" => 27, "nombre" => "OPT Rio Caribe", "tipo_oficina_id" =>	3,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Calle Rivero, al lado del Registro Civil y Prefectura, Sin N°, Parroquia Rio Caribe, Municipio Arismendi, Rio Caribe, Cumana; Estado Sucre, Z.P. 6164.", "telefono" => "04245555555", "codigo_ubicacion" => "6164",
            "estado_id" => 23, "municipio_id" => 332, "parroquia_id" => 1114,	"zona_economica_especial" => true,	"latitud" => "10.6965970",	"longitud" => "-63.1110210", "estatus_id" => 1,  "operaciones" => false,	"created_at" => now()],

            ["codigo" => "OP130", "oficina_relacionada_id" => 27, "nombre" => "OPT San Antonio del Golfo", "tipo_oficina_id" =>	3,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Avenida la Marina, Frente a los Kioscos Gastronómicos, Carretera Nacional, Parroquia Mejia, Municipio Mejías, San Antonio del Golfo, Cumana, Estado Sucre, Z.P. 6110.", "telefono" => "04245555555", "codigo_ubicacion" => "6110",
            "estado_id" => 23, "municipio_id" => 324, "parroquia_id" => 1083,	"zona_economica_especial" => true,	"latitud" => "10.4413520",	"longitud" => "-63.7932250", "estatus_id" => 1,  "operaciones" => false,	"created_at" => now()],

            ["codigo" => "OP131", "oficina_relacionada_id" => 27, "nombre" => "OPT San Jose de Aerocuar", "tipo_oficina_id" =>	3,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Calle Sucre N° 10, Parroquia San José De Aerocuar, Municipio Andrés Mata, San José De Aerocuar, Cumana, Estado Sucre, Z.P. 6167.", "telefono" => "04245555555", "codigo_ubicacion" => "6167",
            "estado_id" => 23, "municipio_id" => 333, "parroquia_id" => 1119,	"zona_economica_especial" => true,	"latitud" => "10.5999420",	"longitud" => "-63.3280640", "estatus_id" => 1,  "operaciones" => false,	"created_at" => now()],

            ["codigo" => "OP132", "oficina_relacionada_id" => 27, "nombre" => "OPT Yaguaraparo", "tipo_oficina_id" =>	3,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Calle Padilla, al dado de la oficina del Distrito Escolar, a pocos metros del Banco de Venezuela y Plaza Bolívar, Parroquia Yaguaraparo, Municipio Cajigal, Yaguaraparo, Cumana, Estado Sucre, Z.P. 6155.", "telefono" => "04245555555", "codigo_ubicacion" => "6155",
            "estado_id" => 23, "municipio_id" => 329, "parroquia_id" => 1102,	"zona_economica_especial" => true,	"latitud" => "10.5661170",	"longitud" => "-62.8257410", "estatus_id" => 1,  "operaciones" => false,	"created_at" => now()],

        	["codigo" => "OP133", "oficina_relacionada_id" => 8, "nombre" => "OPT Abejales", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Carrera 4 Esq. Calle 4 N°4-7 Abejales Municipio Libertador Estado Táchira.", "telefono" => "04245555555",	"codigo_ubicacion" => "5002",
            "estado_id" => 6, "municipio_id" => 97, "parroquia_id" => 348,	"zona_economica_especial" => true, "latitud" => "7.6243720", "longitud" => "-71.5095440", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP134", "oficina_relacionada_id" => 8, "nombre" => "OPT Colon", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "TERMINAL DE PASAJEROS DEL MUNICIPIO PARTE ALTA", "telefono" => "04245555555",	"codigo_ubicacion" => "5003",
            "estado_id" => 6, "municipio_id" => 90, "parroquia_id" => 332,	"zona_economica_especial" => true, "latitud" => "8.0307040", "longitud" => "-72.2610130", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP135", "oficina_relacionada_id" => 8, "nombre" => "OPT Coloncito", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "CALLE 8 BIS.S/N PARTE ALTA.SECTOR BELLA VISTA AL LADO DE LA PARTE DE ARRIBA DEL CDI", "telefono" => "04245555555",	"codigo_ubicacion" => "5038",
            "estado_id" => 6, "municipio_id" => 103, "parroquia_id" => 366,	"zona_economica_especial" => true, "latitud" => "8.3248940", "longitud" => "-72.0893930", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP136", "oficina_relacionada_id" => 8, "nombre" => "OPT Cordero", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Av. Cristóbal Mendoza Calle 11 al Lado De La Biblioteca Cordero Municipio: Andrés Bello Parroquia: Andrés Bello Estado Táchira.", "telefono" => "04245555555",	"codigo_ubicacion" => "5012",
            "estado_id" => 6, "municipio_id" => 80, "parroquia_id" => 315,	"zona_economica_especial" => true, "latitud" => "7.8551260", "longitud" => "-72.1809280", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP137", "oficina_relacionada_id" => 8, "nombre" => "OPT La Fria", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "CALLE 2 CON CARRETERA PANAMERICANA CASCO CENTRAL AL LADO DE LA PREFECTURA DEL MUN. GARCIA DE HEVIA", "telefono" => "04245555555",	"codigo_ubicacion" => "5020",
            "estado_id" => 6, "municipio_id" => 101, "parroquia_id" => 362,	"zona_economica_especial" => true, "latitud" => "8.2174220", "longitud" => "-72.24499080", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP138", "oficina_relacionada_id" => 8, "nombre" => "OPT La Grita", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "CARRERA 6 CON ESQUINA DE CALLE 1.DIAGONAL AL CEMENTERIO MUNICIPAL", "telefono" => "04245555555",	"codigo_ubicacion" => "5022",
            "estado_id" => 6, "municipio_id" => 92, "parroquia_id" => 339,	"zona_economica_especial" => true, "latitud" => "8.1355190", "longitud" => "-71.98379230", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP139", "oficina_relacionada_id" => 8, "nombre" => "OPT Las Delicias", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Centro Cívico San José De Delicias, Calle 3 Con Carrera 3 Oficina No. 1 Municipio: Rafael Urdaneta Estado Táchira", "telefono" => "04245555555",	"codigo_ubicacion" => "5028",
            "estado_id" => 6, "municipio_id" => 89, "parroquia_id" => 331,	"zona_economica_especial" => true, "latitud" => "7.5646940", "longitud" => "-72.4486570", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP140", "oficina_relacionada_id" => 8, "nombre" => "OPT Rubio", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "CC. Pontalida P/B Av. 11 Con Carrera 14 Y 15 N° 14-35 Rubio Municipio: Junín Parroquia: Junín Estado: Táchira.Municipio: Rafael Urdaneta Estado Táchira", "telefono" => "04245555555",	"codigo_ubicacion" => "5030",
            "estado_id" => 6, "municipio_id" => 88, "parroquia_id" => 327,	"zona_economica_especial" => true, "latitud" => "7.7028370", "longitud" => "-72.3594370", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP141", "oficina_relacionada_id" => 8, "nombre" => "OPT San Antonio de Tachira", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Calle 2 Con Carrera 10 Esquina Barrio Curazao)  Municipio: Bolívar  Parroquia: Bolívar Estado: Táchira.", "telefono" => "04245555555",	"codigo_ubicacion" => "5007",
            "estado_id" => 6, "municipio_id" => 91, "parroquia_id" => 335,	"zona_economica_especial" => true, "latitud" => "7.8131670", "longitud" => "-72.4470030", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP142", "oficina_relacionada_id" => 8, "nombre" => "OPT San Cristobal ", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Edificio Nacional Calle 5 Entre Carreras 2 Y 3, Sector Catedral,  Municipio San Cristóbal  Parroquia: San Sebastián Edo. Táchira.", "telefono" => "04245555555",	"codigo_ubicacion" => "5001",
            "estado_id" => 6, "municipio_id" => 78, "parroquia_id" => 307,	"zona_economica_especial" => true, "latitud" => "7.7657410", "longitud" => "-72.2356870", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP143", "oficina_relacionada_id" => 8, "nombre" => "OPT San Rafael del Pinal", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "CARRERA 3 ENTRE CALLES 2 Y 3. SEDE ALCALDIA.EN FRENTE AL PARQUE RENATO LA PORTA", "telefono" => "04245555555",	"codigo_ubicacion" => "5032",
            "estado_id" => 6, "municipio_id" => 96, "parroquia_id" => 345,	"zona_economica_especial" => true, "latitud" => "7.5315360", "longitud" => "-71.9589420", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP144", "oficina_relacionada_id" => 8, "nombre" => "OPT Santa Ana", "tipo_oficina_id" => 3, "jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "N/A", "telefono" => "04245555555",	"codigo_ubicacion" => "5051",
            "estado_id" => 6, "municipio_id" => 95, "parroquia_id" => 344,	"zona_economica_especial" => true, "latitud" => "7.6448200", "longitud" => "-72.2768630", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

        	["codigo" => "OP145", "oficina_relacionada_id" => 9, "nombre" => "OPT Betijoque",	"tipo_oficina_id" => 3,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Avenida 5 Con Calle 28  Terminal De Pasajeros, Terminal Betijoque Local S/Parroquia La Pueblita. Municipio Rafael Rangel Estado Trujillo", "telefono" => "04245555555", "codigo_ubicacion" => "3104",
            "estado_id" => 4, "municipio_id" => 40,	"parroquia_id" => 153,	"zona_economica_especial" => true, "latitud" => "9.3730390", "longitud" => "-70.7333190", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP146", "oficina_relacionada_id" => 9, "nombre" => "OPT Bocono",	"tipo_oficina_id" => 3,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Calle Bolívar Cruce Con Avenida Sucre, Edificio Telecomunicaciones, Planta Baja, Bocono, Parroquia San Alejo. Municipio Bocono Estado Trujillo", "telefono" => "04245555555", "codigo_ubicacion" => "3103",
            "estado_id" => 4, "municipio_id" => 42,	"parroquia_id" => 160,	"zona_economica_especial" => true, "latitud" => "9.2442010", "longitud" => "-70.2712540", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP147", "oficina_relacionada_id" => 9, "nombre" => "OPT Campo Elías",	"tipo_oficina_id" => 3,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Calle Independencia Local S/N Alcaldía Del Municipio Campo Elías, Frente A La Plaza Bolívar, Parroquia Campo Elías Municipio Bocono. Estado Trujillo", "telefono" => "04245555555", "codigo_ubicacion" => "3116",
            "estado_id" => 4, "municipio_id" => 43,	"parroquia_id" => 172,	"zona_economica_especial" => true, "latitud" => "9.3907250", "longitud" => "-70.0600390", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP148", "oficina_relacionada_id" => 9, "nombre" => "OPT Carache",	"tipo_oficina_id" => 3,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Calle Sucre Entre, Avenida Carabobo Y Libertad, Local S/N, Parroquia Carache Municipio Carache Estado Trujillo", "telefono" => "04245555555", "codigo_ubicacion" => "3123",
            "estado_id" => 4, "municipio_id" => 47,	"parroquia_id" => 188,	"zona_economica_especial" => true, "latitud" => "9.6273240", "longitud" => "-70.2269870", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP149", "oficina_relacionada_id" => 9, "nombre" => "OPT Chejende ",	"tipo_oficina_id" => 3,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Calle Magariaga, Local S/N, Parroquia Chejende Municipio Candelaria, Estado Trujillo", "telefono" => "04245555555", "codigo_ubicacion" => "3124",
            "estado_id" => 4, "municipio_id" => 46,	"parroquia_id" => 181,	"zona_economica_especial" => true, "latitud" => "9.6186970", "longitud" => "-70.3595360", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP150", "oficina_relacionada_id" => 9, "nombre" => "OPT Escuque",	"tipo_oficina_id" => 3,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Calle Mismote, Local Municipal Mas Abajo De La Alcaldía, Parroquia Escuque, Municipio Escuque Estado Trujillo", "telefono" => "04245555555", "codigo_ubicacion" => "3105",
            "estado_id" => 4, "municipio_id" => 37,	"parroquia_id" => 142,	"zona_economica_especial" => true, "latitud" => "9.2981340", "longitud" => "-70.6719340", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP151", "oficina_relacionada_id" => 9, "nombre" => "MOD. TERMINAL PASAJEROS VALERA",	"tipo_oficina_id" => 3,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Sector La Plata, Terminal De Pasajeros Local S/N Segundo Pasillo, Parroquia Juan Ignacio, Montilla. Municipio Valera Estado Trujillo.", "telefono" => "04245555555", "codigo_ubicacion" => "3101",
            "estado_id" => 4, "municipio_id" => 36,	"parroquia_id" => 136,	"zona_economica_especial" => true, "latitud" => "9.3196820", "longitud" => "-70.5974260", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP152", "oficina_relacionada_id" => 9, "nombre" => "OPT Pampam",	"tipo_oficina_id" => 3,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Sector La Plata, Terminal De Pasajeros Local S/N Segundo Pasillo, Parroquia Juan Ignacio, Montilla. Municipio Valera Estado Trujillo.", "telefono" => "04245555555", "codigo_ubicacion" => "3151",
            "estado_id" => 4, "municipio_id" => 44,	"parroquia_id" => 174,	"zona_economica_especial" => true, "latitud" => "9.4446080", "longitud" => "-70.4691280", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP153", "oficina_relacionada_id" => 9, "nombre" => "OPT Santa Apolonia de Trujillo",	"tipo_oficina_id" => 3,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Calle Principal, Local Municipal Modulo De Servicios,  Diagonal A La Plaza Bolívar, Parroquia Santa Apolonia Municipio La Ceiba Estado Trujillo.", "telefono" => "04245555555", "codigo_ubicacion" => "3127",
            "estado_id" => 4, "municipio_id" => 54,	"parroquia_id" => 218,	"zona_economica_especial" => true, "latitud" => "9.4673900", "longitud" => "-71.0115300", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP154", "oficina_relacionada_id" => 9, "nombre" => "OPT Trujillo",	"tipo_oficina_id" => 3,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Avenida Independencia, Edificio Telecomunicaciones, Planta Baja,  Diagonal Plaza Bolívar. Parroquia Matriz. Municipio Trujillo Estado Trujillo", "telefono" => "04245555555", "codigo_ubicacion" => "3150",
            "estado_id" => 4, "municipio_id" => 35,	"parroquia_id" => 129,	"zona_economica_especial" => true, "latitud" => "9.4673900", "longitud" => "-71.0115300", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP155", "oficina_relacionada_id" => 9, "nombre" => "OPT Valera",	"tipo_oficina_id" => 3,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Avenida 11 Esquina Calle 7, Edificio  Telecomunicaciones, Planta Baja, Diagonal Plaza Bolívar. Parroquia Mercedes Díaz. Municipio Valera Estado Trujillo", "telefono" => "04245555555", "codigo_ubicacion" => "3101",
            "estado_id" => 4, "municipio_id" => 36,	"parroquia_id" => 138,	"zona_economica_especial" => true, "latitud" => "9.3196820", "longitud" => "-70.5974260", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

        	["codigo" => "OP160", "oficina_relacionada_id" => 14,	"nombre" => "OPT Aroa",  "tipo_oficina_id" => 3,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Negro Primero Detrás De Ceian Sector Carampapa Capital Aroa Mcpio Bolivar.", "telefono" => "04245555555", "codigo_ubicacion" => "3210",
            "estado_id" => 11,	"municipio_id" => 154, "parroquia_id" => 537, "zona_economica_especial" => true,	"latitud" => "10.4379850", "longitud" => "-68.8968360", "estatus_id" => 1,  "operaciones" => false,	"created_at" => now()],

            ["codigo" => "OP161", "oficina_relacionada_id" => 14,	"nombre" => "OPT Chivacoa",  "tipo_oficina_id" => 3,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Calle 14 Con Avenida 7, Estado Yaracuy.", "telefono" => "04245555555", "codigo_ubicacion" => "3202",
            "estado_id" => 11,	"municipio_id" => 155, "parroquia_id" => 538, "zona_economica_especial" => true,	"latitud" => "10.1592610", "longitud" => "-68.8973700", "estatus_id" => 1,  "operaciones" => false,	"created_at" => now()],

            ["codigo" => "OP162", "oficina_relacionada_id" => 14,	"nombre" => "OPT Nirgua",  "tipo_oficina_id" => 3,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Av. Bolivar Entre Calles 6 Y 7 Edf. Rental Planta baja Estado Yaracuy.", "telefono" => "04245555555", "codigo_ubicacion" => "3205",
            "estado_id" => 11,	"municipio_id" => 161, "parroquia_id" => 545, "zona_economica_especial" => true,	"latitud" => "10.1528780", "longitud" => "-68.5703040", "estatus_id" => 1,  "operaciones" => false,	"created_at" => now()],

            ["codigo" => "OP163", "oficina_relacionada_id" => 14,	"nombre" => "OPT San Felipe",  "tipo_oficina_id" => 3,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Av. 7 Entre Calles 11 Y 12 Edif Rental Planta Baja, Estado Yaracuy.", "telefono" => "04245555555", "codigo_ubicacion" => "3201",
            "estado_id" => 11,	"municipio_id" => 163, "parroquia_id" => 550, "zona_economica_especial" => true,	"latitud" => "1", "longitud" => "1", "estatus_id" => 1,  "operaciones" => false,	"created_at" => now()],

            ["codigo" => "OP164", "oficina_relacionada_id" => 14,	"nombre" => "OPT Yaritagua",  "tipo_oficina_id" => 3,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Carrera 7 Entre Calles 11 Y 12 Estado Yaracuy.", "telefono" => "04245555555", "codigo_ubicacion" => "3203",
            "estado_id" => 11,	"municipio_id" => 162, "parroquia_id" => 548, "zona_economica_especial" => true,	"latitud" => "10.0768250", "longitud" => "-69.1256970", "estatus_id" => 1,  "operaciones" => false,	"created_at" => now()],

            ["codigo" => "OP165", "oficina_relacionada_id" => 21,	"nombre" => "OPT Cabimas", "tipo_oficina_id" => 3,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Calle Rosario Con Av. Miranda, Detrás De La Catedral, Casco Central De Cabimas, Parroquia Carmen Herrera, Estado Zulia.", "telefono" => "04245555555", "codigo_ubicacion" => "4013",
            "estado_id" => 17, "municipio_id" => 241, "parroquia_id" => 800, "zona_economica_especial" => true,	"latitud" => "10.4118540", "longitud" => "-71.4626460", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP166", "oficina_relacionada_id" => 21,	"nombre" => "OPT Ciudad Ojeda", "tipo_oficina_id" => 3,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Calle Bermúdez N° 178, Parroquia Alonso De Ojeda, Lagunillas Estado Zulia.", "telefono" => "04245555555", "codigo_ubicacion" => "4019",
            "estado_id" => 17, "municipio_id" => 243, "parroquia_id" => 813, "zona_economica_especial" => true,	"latitud" => "10.2012470", "longitud" => "-71.3152930", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP167", "oficina_relacionada_id" => 21,	"nombre" => "OPT Maracaibo", "tipo_oficina_id" => 3,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Calle 98 Con Av. 3, N° 2a-18, Edificio Los Gemelos, Antigua Guardia Nacional, Diagonal A Traki, Parroquia Bolívar, Maracaibo, Estado Zulia.", "telefono" => "04245555555", "codigo_ubicacion" => "4001",
            "estado_id" => 17, "municipio_id" => 239, "parroquia_id" => 776, "zona_economica_especial" => true,	"latitud" => "10.6389300", "longitud" => "-71.6072310", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP168", "oficina_relacionada_id" => 21,	"nombre" => "OPT Mene Grande", "tipo_oficina_id" => 3,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Calle Comercio, C.C. Las Vegas, Local N° 6, Parroquia Libertador, Mene Grande, Estado Zulia.", "telefono" => "04245555555", "codigo_ubicacion" => "4015",
            "estado_id" => 17, "municipio_id" => 257, "parroquia_id" => 871, "zona_economica_especial" => true,	"latitud" => "9.8474360", "longitud" => "-70.9269740", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP169", "oficina_relacionada_id" => 21,	"nombre" => "OPT Palaima", "tipo_oficina_id" => 3,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Av. 16 Guajira, C.C. Palaima Locales 1 Y 2, Parroquia Idelfonso Vasquez, Palaima, Estado Zulia.", "telefono" => "04245555555", "codigo_ubicacion" => "4005",
            "estado_id" => 17, "municipio_id" => 239, "parroquia_id" => 789, "zona_economica_especial" => true,	"latitud" => "10.6900430", "longitud" => "-71.6348460", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],

            ["codigo" => "OP170", "oficina_relacionada_id" => 21,	"nombre" => "OPT San Francisco", "tipo_oficina_id" => 3,	"jefe_oficina" => "N/A", "correo" => "N/A",
            "direccion" => "Avenida Pcpal. San Francisco Calle 158,  Modelo De Servicio Nueva Bolivia,  C.C. Chacin Locales 1 Y 2, Diagonal A Cantv, Parroquia San Francisco, Estado Zulia.", "telefono" => "04245555555", "codigo_ubicacion" => "4004",
            "estado_id" => 17, "municipio_id" => 240, "parroquia_id" => 794, "zona_economica_especial" => true,	"latitud" => "10.5688560", "longitud" => "-71.6218000", "estatus_id" => 1,  "operaciones" => false, "created_at" => now()],



            //EXTERNAS
            // ["codigo" => 'EX001', "oficina_relacionada_id" => 10, "nombre"=> 'MRW', "tipo_oficina_id" => 7, "jefe_oficina" => 'N/A', "correo" => 'MRW@email.com',
            // "direccion" => 'Chacao', "telefono" => 04245555555, "codigo_ubicacion" => 1020, "estado_id" => 1, "municipio_id" => 1,
            // "parroquia_id" => 12, "zona_economica_especial" => true, "latitud" => 10.457916, "longitud" => -66.917793 , "estatus_id" => 1,  "operaciones" => true, "created_at" => now()],

            //CPI
            ["codigo" => "CI001", "oficina_relacionada_id" => null, "nombre" => "CPI", "tipo_oficina_id" => 6, "jefe_oficina" => 'N/A', "correo" => "N/A",
            "direccion" => "CPI DE PRUEBA",
            "telefono" => 04245555555, "codigo_ubicacion" => 5101, "estado_id" => 5, "municipio_id" => 55, "parroquia_id" => 176,	226, "zona_economica_especial" => false,
            "latitud" => "N/A", "longitud" => "N/A", "estatus_id" => 1,  "operaciones" => true, "created_at" => now()],
        ];

        foreach ($oficinas as $oficina) {
            Oficina::firstOrCreate(
                ['codigo' => $oficina['codigo'], 'oficina_relacionada_id' => $oficina['oficina_relacionada_id'] ?? null, 'nombre' => $oficina['nombre']],
                ['tipo_oficina_id' => $oficina['tipo_oficina_id'], 'jefe_oficina' => $oficina['jefe_oficina'], 'correo' => $oficina['correo'], 'direccion' => $oficina['direccion'],  
                'telefono' => $oficina['telefono'], 'codigo_ubicacion' => $oficina['codigo_ubicacion'], 'estado_id' => $oficina['estado_id'],  
                'municipio_id' => $oficina['municipio_id'], 'parroquia_id' => $oficina['parroquia_id'], 'zona_economica_especial' => $oficina['zona_economica_especial'], 
                'latitud' => $oficina['latitud'], 'longitud' => $oficina['longitud'], 'estatus_id' => $oficina['estatus_id'], 'operaciones' => $oficina['operaciones'], 
                'created_at' => now()]
            );
        }
    }
}
