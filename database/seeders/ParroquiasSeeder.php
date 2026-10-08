<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ParroquiasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('parroquias')->insert([

            ["municipio_id" => 1, "nombre" => 'Antimano', "activo" => true, "created_at" => now()],
            ["municipio_id" => 1, "nombre" => 'Caricuao', "activo" => true, "created_at" => now()],
            ["municipio_id" => 1, "nombre" => 'Macarao', "activo" => true, "created_at" => now()],
            ["municipio_id" => 1, "nombre" => 'Altagracia', "activo" => true, "created_at" => now()],
            ["municipio_id" => 1, "nombre" => 'La Pastora', "activo" => true, "created_at" => now()],
            ["municipio_id" => 1, "nombre" => 'San José', "activo" => true, "created_at" => now()],
            ["municipio_id" => 1, "nombre" => 'La Candelaria', "activo" => true, "created_at" => now()],
            ["municipio_id" => 1, "nombre" => 'San Bernardino', "activo" => true, "created_at" => now()],
            ["municipio_id" => 1, "nombre" => 'Catedral', "activo" => true, "created_at" => now()],
            ["municipio_id" => 1, "nombre" => 'Santa Teresa', "activo" => true, "created_at" => now()],
            ["municipio_id" => 1, "nombre" => 'Santa Rosalía', "activo" => true, "created_at" => now()],
            ["municipio_id" => 1, "nombre" => 'San Juan', "activo" => true, "created_at" => now()],
            ["municipio_id" => 1, "nombre" => 'San Agustín', "activo" => true, "created_at" => now()],
            ["municipio_id" => 1, "nombre" => 'El Paraíso', "activo" => true, "created_at" => now()],
            ["municipio_id" => 1, "nombre" => 'La Vega', "activo" => true, "created_at" => now()],
            ["municipio_id" => 1, "nombre" => 'Sucre', "activo" => true, "created_at" => now()],
            ["municipio_id" => 1, "nombre" => 'El Junquito', "activo" => true, "created_at" => now()],
            ["municipio_id" => 1, "nombre" => '23 de Enero', "activo" => true, "created_at" => now()],
            ["municipio_id" => 1, "nombre" => 'El Valle', "activo" => true, "created_at" => now()],
            ["municipio_id" => 1, "nombre" => 'San Pedro', "activo" => true, "created_at" => now()],
            ["municipio_id" => 1, "nombre" => 'El Recreo', "activo" => true, "created_at" => now()],
            ["municipio_id" => 1, "nombre" => 'Coche', "activo" => true, "created_at" => now()],

            ["municipio_id" => 2, "nombre" => 'Chacao', "activo" => true, "created_at" => now()],

            ["municipio_id" => 3, "nombre" => 'Baruta', "activo" => true, "created_at" => now()],
            ["municipio_id" => 3, "nombre" => 'Las Minas de Baruta', "activo" => true, "created_at" => now()],

            ["municipio_id" => 4, "nombre" => 'Petare', "activo" => true, "created_at" => now()],
            ["municipio_id" => 4, "nombre" => 'Fila de Mariches', "activo" => true, "created_at" => now()],
            ["municipio_id" => 4, "nombre" => 'Leoncio Martínez', "activo" => true, "created_at" => now()],
            ["municipio_id" => 4, "nombre" => 'Caucaguita', "activo" => true, "created_at" => now()],
            ["municipio_id" => 4, "nombre" => 'La Dolorita', "activo" => true, "created_at" => now()],

            ["municipio_id" => 5, "nombre" => 'La Dolorita', "activo" => true, "created_at" => now()],
            ["municipio_id" => 5, "nombre" => 'El Hatillo', "activo" => true, "created_at" => now()],

            ["municipio_id" => 6, "nombre" => 'Los Teques', "activo" => true, "created_at" => now()],
            ["municipio_id" => 6, "nombre" => 'El Jarillo', "activo" => true, "created_at" => now()],
            ["municipio_id" => 6, "nombre" => 'Paracotos', "activo" => true, "created_at" => now()],
            ["municipio_id" => 6, "nombre" => 'San Pedro', "activo" => true, "created_at" => now()],
            ["municipio_id" => 6, "nombre" => 'Altagracia de La Montaña', "activo" => true, "created_at" => now()],
            ["municipio_id" => 6, "nombre" => 'Tácata', "activo" => true, "created_at" => now()],

            ["municipio_id" => 7, "nombre" => 'Carrizal', "activo" => true, "created_at" => now()],
            ["municipio_id" => 7, "nombre" => 'Cecilio Acosta', "activo" => true, "created_at" => now()],

            ["municipio_id" => 8, "nombre" => 'San Antonio de Los Altos', "activo" => true, "created_at" => now()],

            ["municipio_id" => 9, "nombre" => 'Ocumare del Tuy', "activo" => true, "created_at" => now()],
            ["municipio_id" => 9, "nombre" => 'La Democracia', "activo" => true, "created_at" => now()],
            ["municipio_id" => 9, "nombre" => 'Santa Bárbara', "activo" => true, "created_at" => now()],

            ["municipio_id" => 10, "nombre" => 'Charallave', "activo" => true, "created_at" => now()],
            ["municipio_id" => 10, "nombre" => 'Las Brisas', "activo" => true, "created_at" => now()],

            ["municipio_id" => 11, "nombre" => 'Cúa', "activo" => true, "created_at" => now()],
            ["municipio_id" => 11, "nombre" => 'Nueva Cúa', "activo" => true, "created_at" => now()],

            ["municipio_id" => 12, "nombre" => 'San Francisco de Yare', "activo" => true, "created_at" => now()],
            ["municipio_id" => 12, "nombre" => 'San Antonio de Yare', "activo" => true, "created_at" => now()],

            ["municipio_id" => 13, "nombre" => 'Santa Lucía', "activo" => true, "created_at" => now()],

            ["municipio_id" => 14, "nombre" => 'Santa Teresa del Tuy', "activo" => true, "created_at" => now()],
            ["municipio_id" => 14, "nombre" => 'El Cartanal', "activo" => true, "created_at" => now()],

            ["municipio_id" => 15, "nombre" => 'Guarenas', "activo" => true, "created_at" => now()],

            ["municipio_id" => 16, "nombre" => 'Guatire', "activo" => true, "created_at" => now()],
            ["municipio_id" => 16, "nombre" => 'Bolívar', "activo" => true, "created_at" => now()],

            ["municipio_id" => 17, "nombre" => 'Mamporal', "activo" => true, "created_at" => now()],

            ["municipio_id" => 18, "nombre" => 'Higuerote', "activo" => true, "created_at" => now()],
            ["municipio_id" => 18, "nombre" => 'Curiepe', "activo" => true, "created_at" => now()],
            ["municipio_id" => 18, "nombre" => 'Tacarigua', "activo" => true, "created_at" => now()],

            ["municipio_id" => 19, "nombre" => 'San José de Barlovento', "activo" => true, "created_at" => now()],
            ["municipio_id" => 19, "nombre" => 'Cumbo', "activo" => true, "created_at" => now()],

            ["municipio_id" => 20, "nombre" => 'Río Chico', "activo" => true, "created_at" => now()],
            ["municipio_id" => 20, "nombre" => 'Tacarigua de La Laguna', "activo" => true, "created_at" => now()],
            ["municipio_id" => 20, "nombre" => 'Paparo', "activo" => true, "created_at" => now()],
            ["municipio_id" => 20, "nombre" => 'El Guapo', "activo" => true, "created_at" => now()],
            ["municipio_id" => 20, "nombre" => 'San Fernando del Guapo', "activo" => true, "created_at" => now()],

            ["municipio_id" => 21, "nombre" => 'Cúpira', "activo" => true, "created_at" => now()],

            ["municipio_id" => 22, "nombre" => 'Caucagua', "activo" => true, "created_at" => now()],
            ["municipio_id" => 22, "nombre" => 'Marizapa', "activo" => true, "created_at" => now()],
            ["municipio_id" => 22, "nombre" => 'Aragüita', "activo" => true, "created_at" => now()],
            ["municipio_id" => 22, "nombre" => 'Ribas', "activo" => true, "created_at" => now()],
            ["municipio_id" => 22, "nombre" => 'Capaya', "activo" => true, "created_at" => now()],
            ["municipio_id" => 22, "nombre" => 'El Café', "activo" => true, "created_at" => now()],
            ["municipio_id" => 22, "nombre" => 'Panaquire', "activo" => true, "created_at" => now()],
            ["municipio_id" => 22, "nombre" => 'Arévalo González', "activo" => true, "created_at" => now()],

            ["municipio_id" => 23, "nombre" => 'Barinas', "activo" => true, "created_at" => now()],
            ["municipio_id" => 23, "nombre" => 'Alfredo Arvelo Larriva', "activo" => true, "created_at" => now()],
            ["municipio_id" => 23, "nombre" => 'Santa Inés', "activo" => true, "created_at" => now()],
            ["municipio_id" => 23, "nombre" => 'Santa Lucía', "activo" => true, "created_at" => now()],
            ["municipio_id" => 23, "nombre" => 'Torunos', "activo" => true, "created_at" => now()],
            ["municipio_id" => 23, "nombre" => 'El Carmen', "activo" => true, "created_at" => now()],
            ["municipio_id" => 23, "nombre" => 'Rómulo Betancourt', "activo" => true, "created_at" => now()],
            ["municipio_id" => 23, "nombre" => 'Corazón de Jesús', "activo" => true, "created_at" => now()],
            ["municipio_id" => 23, "nombre" => 'Ramón Ignacio Méndez', "activo" => true, "created_at" => now()],
            ["municipio_id" => 23, "nombre" => 'Alto Barinas', "activo" => true, "created_at" => now()],
            ["municipio_id" => 23, "nombre" => 'Manuel Palacio Fajardo', "activo" => true, "created_at" => now()],
            ["municipio_id" => 23, "nombre" => 'Juan Antonio Rodríguez Domínguez', "activo" => true, "created_at" => now()],
            ["municipio_id" => 23, "nombre" => 'Dominga Ortíz de Páez', "activo" => true, "created_at" => now()],
            ["municipio_id" => 23, "nombre" => 'San Silvestre', "activo" => true, "created_at" => now()],

            ["municipio_id" => 24, "nombre" => 'Sabaneta', "activo" => true, "created_at" => now()],
            ["municipio_id" => 24, "nombre" => 'Rodríguez Domínguez', "activo" => true, "created_at" => now()],

            ["municipio_id" => 25, "nombre" => 'Ciudad de Nutrias', "activo" => true, "created_at" => now()],
            ["municipio_id" => 25, "nombre" => 'El Regalo', "activo" => true, "created_at" => now()],
            ["municipio_id" => 25, "nombre" => 'Puerto de Nutrias', "activo" => true, "created_at" => now()],
            ["municipio_id" => 25, "nombre" => 'Santa Catalina', "activo" => true, "created_at" => now()],

            ["municipio_id" => 26, "nombre" => 'Ticoporo', "activo" => true, "created_at" => now()],
            ["municipio_id" => 26, "nombre" => 'Andrés Bello', "activo" => true, "created_at" => now()],
            ["municipio_id" => 26, "nombre" => 'Nicolás Pulido', "activo" => true, "created_at" => now()],

            ["municipio_id" => 27, "nombre" => 'Arismendi', "activo" => true, "created_at" => now()],
            ["municipio_id" => 27, "nombre" => 'Guadarrama', "activo" => true, "created_at" => now()],
            ["municipio_id" => 27, "nombre" => 'La Unión', "activo" => true, "created_at" => now()],
            ["municipio_id" => 27, "nombre" => 'San Antonio', "activo" => true, "created_at" => now()],

            ["municipio_id" => 28, "nombre" => 'Libertad', "activo" => true, "created_at" => now()],
            ["municipio_id" => 28, "nombre" => 'Dolores', "activo" => true, "created_at" => now()],
            ["municipio_id" => 28, "nombre" => 'Palacios Fajardo', "activo" => true, "created_at" => now()],
            ["municipio_id" => 28, "nombre" => 'Santa Rosa', "activo" => true, "created_at" => now()],

            ["municipio_id" => 29, "nombre" => 'Barinitas', "activo" => true, "created_at" => now()],
            ["municipio_id" => 29, "nombre" => 'Altamira', "activo" => true, "created_at" => now()],
            ["municipio_id" => 29, "nombre" => 'Calderas', "activo" => true, "created_at" => now()],

            ["municipio_id" => 30, "nombre" => 'Barrancas', "activo" => true, "created_at" => now()],
            ["municipio_id" => 30, "nombre" => 'El Socorro', "activo" => true, "created_at" => now()],
            ["municipio_id" => 30, "nombre" => 'Masparrito', "activo" => true, "created_at" => now()],

            ["municipio_id" => 31, "nombre" => 'Santa Bárbara', "activo" => true, "created_at" => now()],
            ["municipio_id" => 31, "nombre" => 'José Ignacio del Pumar', "activo" => true, "created_at" => now()],
            ["municipio_id" => 31, "nombre" => 'Pedro Briceño Méndez', "activo" => true, "created_at" => now()],
            ["municipio_id" => 31, "nombre" => 'Ramón Ignacio Méndez', "activo" => true, "created_at" => now()],

            ["municipio_id" => 32, "nombre" => 'El Cantón', "activo" => true, "created_at" => now()],
            ["municipio_id" => 32, "nombre" => 'Santa Cruz de Guacas', "activo" => true, "created_at" => now()],
            ["municipio_id" => 32, "nombre" => 'Puerto Vivas', "activo" => true, "created_at" => now()],

            ["municipio_id" => 33, "nombre" => 'Obispos', "activo" => true, "created_at" => now()],
            ["municipio_id" => 33, "nombre" => 'El Real', "activo" => true, "created_at" => now()],
            ["municipio_id" => 33, "nombre" => 'La Luz', "activo" => true, "created_at" => now()],
            ["municipio_id" => 33, "nombre" => 'Los Guasimitos', "activo" => true, "created_at" => now()],

            ["municipio_id" => 34, "nombre" => 'Ciudad Bolivia', "activo" => true, "created_at" => now()],
            ["municipio_id" => 34, "nombre" => 'Ignacio Briceño', "activo" => true, "created_at" => now()],
            ["municipio_id" => 34, "nombre" => 'José Félix Ribas', "activo" => true, "created_at" => now()],
            ["municipio_id" => 34, "nombre" => 'Páez', "activo" => true, "created_at" => now()],

            ["municipio_id" => 35, "nombre" => 'Matríz', "activo" => true, "created_at" => now()],
            ["municipio_id" => 35, "nombre" => 'Andrés Linares', "activo" => true, "created_at" => now()],
            ["municipio_id" => 35, "nombre" => 'Chiquinquirá', "activo" => true, "created_at" => now()],
            ["municipio_id" => 35, "nombre" => 'Cristóbal Mendoza', "activo" => true, "created_at" => now()],
            ["municipio_id" => 35, "nombre" => 'Cruz Carrillo', "activo" => true, "created_at" => now()],
            ["municipio_id" => 35, "nombre" => 'Monseñor Carrillo', "activo" => true, "created_at" => now()],
            ["municipio_id" => 35, "nombre" => 'Tres Esquinas', "activo" => true, "created_at" => now()],

            ["municipio_id" => 36, "nombre" => 'Juan Ignacio Montilla', "activo" => true, "created_at" => now()],
            ["municipio_id" => 36, "nombre" => 'La Beatríz', "activo" => true, "created_at" => now()],
            ["municipio_id" => 36, "nombre" => 'Mercedes Díaz', "activo" => true, "created_at" => now()],
            ["municipio_id" => 36, "nombre" => 'San Luis', "activo" => true, "created_at" => now()],
            ["municipio_id" => 36, "nombre" => 'La Puerta', "activo" => true, "created_at" => now()],
            ["municipio_id" => 36, "nombre" => 'Mendoza', "activo" => true, "created_at" => now()],

            ["municipio_id" => 37, "nombre" => 'Escuque', "activo" => true, "created_at" => now()],
            ["municipio_id" => 37, "nombre" => 'La Unión', "activo" => true, "created_at" => now()],
            ["municipio_id" => 37, "nombre" => 'Sabana Libre', "activo" => true, "created_at" => now()],
            ["municipio_id" => 37, "nombre" => 'Santa Rita', "activo" => true, "created_at" => now()],

            ["municipio_id" => 38, "nombre" => 'Motatán', "activo" => true, "created_at" => now()],
            ["municipio_id" => 38, "nombre" => 'El Baño', "activo" => true, "created_at" => now()],
            ["municipio_id" => 38, "nombre" => 'Jalisco', "activo" => true, "created_at" => now()],

            ["municipio_id" => 39, "nombre" => 'Pampanito', "activo" => true, "created_at" => now()],
            ["municipio_id" => 39, "nombre" => 'La Concepción', "activo" => true, "created_at" => now()],
            ["municipio_id" => 39, "nombre" => 'Pampanito ll', "activo" => true, "created_at" => now()],

            ["municipio_id" => 40, "nombre" => 'Betijoque', "activo" => true, "created_at" => now()],
            ["municipio_id" => 40, "nombre" => 'La Pueblita', "activo" => true, "created_at" => now()],
            ["municipio_id" => 40, "nombre" => 'Los Cedros', "activo" => true, "created_at" => now()],
            ["municipio_id" => 40, "nombre" => 'José Gregorio Hernández', "activo" => true, "created_at" => now()],

            ["municipio_id" => 41, "nombre" => 'Carvajal', "activo" => true, "created_at" => now()],
            ["municipio_id" => 41, "nombre" => 'Antonio Nicolás Briceño', "activo" => true, "created_at" => now()],
            ["municipio_id" => 41, "nombre" => 'Campo Alegre', "activo" => true, "created_at" => now()],
            ["municipio_id" => 41, "nombre" => 'José Leonardo Suárez', "activo" => true, "created_at" => now()],

            ["municipio_id" => 42, "nombre" => 'Boconó', "activo" => true, "created_at" => now()],
            ["municipio_id" => 42, "nombre" => 'El Carmen', "activo" => true, "created_at" => now()],
            ["municipio_id" => 42, "nombre" => 'Mosquey', "activo" => true, "created_at" => now()],
            ["municipio_id" => 42, "nombre" => 'Ayacucho', "activo" => true, "created_at" => now()],
            ["municipio_id" => 42, "nombre" => 'Burbusay', "activo" => true, "created_at" => now()],
            ["municipio_id" => 42, "nombre" => 'General Rivas', "activo" => true, "created_at" => now()],
            ["municipio_id" => 42, "nombre" => 'Guaramacal', "activo" => true, "created_at" => now()],
            ["municipio_id" => 42, "nombre" => 'Vega de Guaramacal', "activo" => true, "created_at" => now()],
            ["municipio_id" => 42, "nombre" => 'Monseñor Jáuregui', "activo" => true, "created_at" => now()],
            ["municipio_id" => 42, "nombre" => 'Rafael Rangel', "activo" => true, "created_at" => now()],
            ["municipio_id" => 42, "nombre" => 'San Miguel', "activo" => true, "created_at" => now()],
            ["municipio_id" => 42, "nombre" => 'San José', "activo" => true, "created_at" => now()],

            ["municipio_id" => 43, "nombre" => 'Campo Elías', "activo" => true, "created_at" => now()],
            ["municipio_id" => 43, "nombre" => 'Arnoldo Gabaldón', "activo" => true, "created_at" => now()],

            ["municipio_id" => 44, "nombre" => 'Pampán', "activo" => true, "created_at" => now()],
            ["municipio_id" => 44, "nombre" => 'Flor de Patria', "activo" => true, "created_at" => now()],
            ["municipio_id" => 44, "nombre" => 'La Paz', "activo" => true, "created_at" => now()],
            ["municipio_id" => 44, "nombre" => 'Santa Ana', "activo" => true, "created_at" => now()],

            ["municipio_id" => 45, "nombre" => 'El Socorro', "activo" => true, "created_at" => now()],
            ["municipio_id" => 45, "nombre" => 'Antonio José de Sucre', "activo" => true, "created_at" => now()],
            ["municipio_id" => 45, "nombre" => 'Los Caprichos', "activo" => true, "created_at" => now()],

            ["municipio_id" => 46, "nombre" => 'Chejendé', "activo" => true, "created_at" => now()],
            ["municipio_id" => 46, "nombre" => 'Arnoldo Gabaldón', "activo" => true, "created_at" => now()],
            ["municipio_id" => 46, "nombre" => 'Bolivia', "activo" => true, "created_at" => now()],
            ["municipio_id" => 46, "nombre" => 'Carrillo', "activo" => true, "created_at" => now()],
            ["municipio_id" => 46, "nombre" => 'Cegarra', "activo" => true, "created_at" => now()],
            ["municipio_id" => 46, "nombre" => 'Manuel Salvador Ulloa', "activo" => true, "created_at" => now()],
            ["municipio_id" => 46, "nombre" => 'San José', "activo" => true, "created_at" => now()],

            ["municipio_id" => 47, "nombre" => 'Carache', "activo" => true, "created_at" => now()],
            ["municipio_id" => 47, "nombre" => 'Cuicas', "activo" => true, "created_at" => now()],
            ["municipio_id" => 47, "nombre" => 'La Concepción', "activo" => true, "created_at" => now()],
            ["municipio_id" => 47, "nombre" => 'Panamericana', "activo" => true, "created_at" => now()],
            ["municipio_id" => 47, "nombre" => 'Santa Cruz', "activo" => true, "created_at" => now()],

            ["municipio_id" => 48, "nombre" => 'Sabana de Mendoza', "activo" => true, "created_at" => now()],
            ["municipio_id" => 48, "nombre" => 'El Paraíso', "activo" => true, "created_at" => now()],
            ["municipio_id" => 48, "nombre" => 'Junín', "activo" => true, "created_at" => now()],
            ["municipio_id" => 48, "nombre" => 'Valmore Rodríguez', "activo" => true, "created_at" => now()],

            ["municipio_id" => 49, "nombre" => 'Sabana Grande', "activo" => true, "created_at" => now()],
            ["municipio_id" => 49, "nombre" => 'Cheregüé', "activo" => true, "created_at" => now()],
            ["municipio_id" => 49, "nombre" => 'Granados', "activo" => true, "created_at" => now()],

            ["municipio_id" => 50, "nombre" => 'El Dividive', "activo" => true, "created_at" => now()],
            ["municipio_id" => 50, "nombre" => 'Agua Santa', "activo" => true, "created_at" => now()],
            ["municipio_id" => 50, "nombre" => 'Agua Caliente', "activo" => true, "created_at" => now()],
            ["municipio_id" => 50, "nombre" => 'El Cenizo', "activo" => true, "created_at" => now()],
            ["municipio_id" => 50, "nombre" => 'Valerita', "activo" => true, "created_at" => now()],

            ["municipio_id" => 51, "nombre" => 'Santa Isabel', "activo" => true, "created_at" => now()],
            ["municipio_id" => 51, "nombre" => 'Araguaney', "activo" => true, "created_at" => now()],
            ["municipio_id" => 51, "nombre" => 'El Jagüito', "activo" => true, "created_at" => now()],
            ["municipio_id" => 51, "nombre" => 'La Esperanza', "activo" => true, "created_at" => now()],

            ["municipio_id" => 52, "nombre" => 'Monte Carmelo', "activo" => true, "created_at" => now()],
            ["municipio_id" => 52, "nombre" => 'Buena Vista', "activo" => true, "created_at" => now()],
            ["municipio_id" => 52, "nombre" => 'Santa María del Horcón', "activo" => true, "created_at" => now()],

            ["municipio_id" => 53, "nombre" => 'La Quebrada', "activo" => true, "created_at" => now()],
            ["municipio_id" => 53, "nombre" => 'Cabimbú', "activo" => true, "created_at" => now()],
            ["municipio_id" => 53, "nombre" => 'Jajó', "activo" => true, "created_at" => now()],
            ["municipio_id" => 53, "nombre" => 'La Mesa', "activo" => true, "created_at" => now()],
            ["municipio_id" => 53, "nombre" => 'Santiago', "activo" => true, "created_at" => now()],
            ["municipio_id" => 53, "nombre" => 'Tuñame', "activo" => true, "created_at" => now()],

            ["municipio_id" => 54, "nombre" => 'Santa Apolonia', "activo" => true, "created_at" => now()],
            ["municipio_id" => 54, "nombre" => 'El Progreso', "activo" => true, "created_at" => now()],
            ["municipio_id" => 54, "nombre" => 'Tres de Febrero', "activo" => true, "created_at" => now()],
            ["municipio_id" => 54, "nombre" => 'La Ceiba', "activo" => true, "created_at" => now()],

            ["municipio_id" => 55, "nombre" => 'Sagrario', "activo" => true, "created_at" => now()],
            ["municipio_id" => 55, "nombre" => 'Antonio Spinetti Dini', "activo" => true, "created_at" => now()],
            ["municipio_id" => 55, "nombre" => 'Arías', "activo" => true, "created_at" => now()],
            ["municipio_id" => 55, "nombre" => 'Caracciolo Parra Pérez', "activo" => true, "created_at" => now()],
            ["municipio_id" => 55, "nombre" => 'Domingo Peña', "activo" => true, "created_at" => now()],
            ["municipio_id" => 55, "nombre" => 'El Llano', "activo" => true, "created_at" => now()],
            ["municipio_id" => 55, "nombre" => 'Gonzalo Picón Febres', "activo" => true, "created_at" => now()],
            ["municipio_id" => 55, "nombre" => 'Jacinto Plaza', "activo" => true, "created_at" => now()],
            ["municipio_id" => 55, "nombre" => 'Lasso de La Vega', "activo" => true, "created_at" => now()],
            ["municipio_id" => 55, "nombre" => 'Mariano Picón Salas', "activo" => true, "created_at" => now()],
            ["municipio_id" => 55, "nombre" => 'Milla', "activo" => true, "created_at" => now()],
            ["municipio_id" => 55, "nombre" => 'Osuna Rodríguez', "activo" => true, "created_at" => now()],
            ["municipio_id" => 55, "nombre" => 'Juan Rodríguez Suárez', "activo" => true, "created_at" => now()],
            ["municipio_id" => 55, "nombre" => 'El Morro', "activo" => true, "created_at" => now()],
            ["municipio_id" => 55, "nombre" => 'Los Nevados', "activo" => true, "created_at" => now()],

            ["municipio_id" => 56, "nombre" => 'Los Nevados', "activo" => true, "created_at" => now()],
            ["municipio_id" => 56, "nombre" => 'Presidente Betancourt', "activo" => true, "created_at" => now()],
            ["municipio_id" => 56, "nombre" => 'Presidente Páez', "activo" => true, "created_at" => now()],
            ["municipio_id" => 56, "nombre" => 'Presidente Rómulo Gallegos', "activo" => true, "created_at" => now()],
            ["municipio_id" => 56, "nombre" => 'Gabriel Picón González', "activo" => true, "created_at" => now()],
            ["municipio_id" => 56, "nombre" => 'Héctor Amable Mora', "activo" => true, "created_at" => now()],
            ["municipio_id" => 56, "nombre" => 'José Nucete Sardi', "activo" => true, "created_at" => now()],
            ["municipio_id" => 56, "nombre" => 'Pulido Méndez', "activo" => true, "created_at" => now()],

            ["municipio_id" => 57, "nombre" => 'Pulido Méndez', "activo" => true, "created_at" => now()],

            ["municipio_id" => 58, "nombre" => 'Pulido Méndez', "activo" => true, "created_at" => now()],

            ["municipio_id" => 59, "nombre" => 'Pulido Méndez', "activo" => true, "created_at" => now()],

            ["municipio_id" => 60, "nombre" => 'Capital Rangel', "activo" => true, "created_at" => now()],
            ["municipio_id" => 60, "nombre" => 'La Toma', "activo" => true, "created_at" => now()],
            ["municipio_id" => 60, "nombre" => 'San Rafael', "activo" => true, "created_at" => now()],
            ["municipio_id" => 60, "nombre" => 'Cacute', "activo" => true, "created_at" => now()],
            ["municipio_id" => 60, "nombre" => 'Macurubá', "activo" => true, "created_at" => now()],

            ["municipio_id" => 61, "nombre" => 'Capital Rivas Dávila', "activo" => true, "created_at" => now()],
            ["municipio_id" => 61, "nombre" => 'Gerónimo Maldonado', "activo" => true, "created_at" => now()],

            ["municipio_id" => 62, "nombre" => 'Gerónimo Maldonado', "activo" => true, "created_at" => now()],

            ["municipio_id" => 63, "nombre" => 'Tovar', "activo" => true, "created_at" => now()],
            ["municipio_id" => 63, "nombre" => 'El Amparo', "activo" => true, "created_at" => now()],
            ["municipio_id" => 63, "nombre" => 'El Llano', "activo" => true, "created_at" => now()],
            ["municipio_id" => 63, "nombre" => 'San Francisco', "activo" => true, "created_at" => now()],

            ["municipio_id" => 64, "nombre" => 'Mesa de Quintero', "activo" => true, "created_at" => now()],
            ["municipio_id" => 64, "nombre" => 'Río Negro', "activo" => true, "created_at" => now()],

            ["municipio_id" => 65, "nombre" => 'Capital Sucre', "activo" => true, "created_at" => now()],
            ["municipio_id" => 65, "nombre" => 'Chiguará', "activo" => true, "created_at" => now()],
            ["municipio_id" => 65, "nombre" => 'Estánquez', "activo" => true, "created_at" => now()],
            ["municipio_id" => 65, "nombre" => 'La Trampa', "activo" => true, "created_at" => now()],
            ["municipio_id" => 65, "nombre" => 'Pueblo Nuevo del Sur', "activo" => true, "created_at" => now()],
            ["municipio_id" => 65, "nombre" => 'San Juan', "activo" => true, "created_at" => now()],

            ["municipio_id" => 66, "nombre" => 'Capital Zea', "activo" => true, "created_at" => now()],
            ["municipio_id" => 66, "nombre" => 'Caño El Tigre', "activo" => true, "created_at" => now()],

            ["municipio_id" => 67, "nombre" => 'Capital Antonio Pinto Salinas', "activo" => true, "created_at" => now()],
            ["municipio_id" => 67, "nombre" => 'Mesa Bolívar', "activo" => true, "created_at" => now()],
            ["municipio_id" => 67, "nombre" => 'Mesa de Las Palmas', "activo" => true, "created_at" => now()],

            ["municipio_id" => 68, "nombre" => 'Capital Aricagua', "activo" => true, "created_at" => now()],
            ["municipio_id" => 68, "nombre" => 'San Antonio', "activo" => true, "created_at" => now()],

            ["municipio_id" => 69, "nombre" => 'Capital Arzobispo Chacón', "activo" => true, "created_at" => now()],
            ["municipio_id" => 69, "nombre" => 'Capurí', "activo" => true, "created_at" => now()],
            ["municipio_id" => 69, "nombre" => 'Chacantá', "activo" => true, "created_at" => now()],
            ["municipio_id" => 69, "nombre" => 'El Molino', "activo" => true, "created_at" => now()],
            ["municipio_id" => 69, "nombre" => 'Guaimaral', "activo" => true, "created_at" => now()],
            ["municipio_id" => 69, "nombre" => 'Mucuchachí', "activo" => true, "created_at" => now()],
            ["municipio_id" => 69, "nombre" => 'Mucutuy', "activo" => true, "created_at" => now()],

            ["municipio_id" => 70, "nombre" => 'Fernández Peña', "activo" => true, "created_at" => now()],
            ["municipio_id" => 70, "nombre" => 'Matriz', "activo" => true, "created_at" => now()],
            ["municipio_id" => 70, "nombre" => 'Montalbán', "activo" => true, "created_at" => now()],
            ["municipio_id" => 70, "nombre" => 'Acequias', "activo" => true, "created_at" => now()],
            ["municipio_id" => 70, "nombre" => 'Jají', "activo" => true, "created_at" => now()],
            ["municipio_id" => 70, "nombre" => 'La Mesa', "activo" => true, "created_at" => now()],
            ["municipio_id" => 70, "nombre" => 'San José del Sur', "activo" => true, "created_at" => now()],

            ["municipio_id" => 71, "nombre" => 'Obispo Ramos de Lora', "activo" => true, "created_at" => now()],
            ["municipio_id" => 71, "nombre" => 'Eloy Paredes', "activo" => true, "created_at" => now()],
            ["municipio_id" => 71, "nombre" => 'San Rafael de Alcázar', "activo" => true, "created_at" => now()],

            ["municipio_id" => 72, "nombre" => 'Capital Caracciolo Parra Olmedo', "activo" => true, "created_at" => now()],
            ["municipio_id" => 72, "nombre" => 'Florencio Ramírez', "activo" => true, "created_at" => now()],

            ["municipio_id" => 73, "nombre" => 'Capital Cardenal Quintero', "activo" => true, "created_at" => now()],
            ["municipio_id" => 73, "nombre" => 'Las Piedras', "activo" => true, "created_at" => now()],

            ["municipio_id" => 74, "nombre" => 'Capital Julio César Salas', "activo" => true, "created_at" => now()],
            ["municipio_id" => 74, "nombre" => 'Palmira', "activo" => true, "created_at" => now()],

            ["municipio_id" => 75, "nombre" => 'Palmira', "activo" => true, "created_at" => now()],
            ["municipio_id" => 75, "nombre" => 'Capital Justo Briceño', "activo" => true, "created_at" => now()],

            ["municipio_id" => 76, "nombre" => 'Tulio Febres Cordero', "activo" => true, "created_at" => now()],
            ["municipio_id" => 76, "nombre" => 'Independencia', "activo" => true, "created_at" => now()],
            ["municipio_id" => 76, "nombre" => 'María de la Concepción Palacios Blanco', "activo" => true, "created_at" => now()],

            ["municipio_id" => 77, "nombre" => 'Capital de Miranda', "activo" => true, "created_at" => now()],
            ["municipio_id" => 77, "nombre" => 'Andrés Eloy Blanco', "activo" => true, "created_at" => now()],
            ["municipio_id" => 77, "nombre" => 'La Venta', "activo" => true, "created_at" => now()],
            ["municipio_id" => 77, "nombre" => 'Piñango', "activo" => true, "created_at" => now()],

            ["municipio_id" => 78, "nombre" => 'San Sebastián', "activo" => true, "created_at" => now()],
            ["municipio_id" => 78, "nombre" => 'San Juan Bautista', "activo" => true, "created_at" => now()],
            ["municipio_id" => 78, "nombre" => 'Pedro María Morantes', "activo" => true, "created_at" => now()],
            ["municipio_id" => 78, "nombre" => 'La Concordia', "activo" => true, "created_at" => now()],
            ["municipio_id" => 78, "nombre" => 'Dr. Francisco Romero Lobo', "activo" => true, "created_at" => now()],

            ["municipio_id" => 79, "nombre" => 'Cárdenas', "activo" => true, "created_at" => now()],
            ["municipio_id" => 79, "nombre" => 'La Florida', "activo" => true, "created_at" => now()],
            ["municipio_id" => 79, "nombre" => 'Amenodoro Rangel Lamús', "activo" => true, "created_at" => now()],

            ["municipio_id" => 80, "nombre" => 'Amenodoro Rangel Lamús', "activo" => true, "created_at" => now()],

            ["municipio_id" => 81, "nombre" => 'Amenodoro Rangel Lamús', "activo" => true, "created_at" => now()],

            ["municipio_id" => 82, "nombre" => 'Sucre', "activo" => true, "created_at" => now()],
            ["municipio_id" => 82, "nombre" => 'Eleazar López Contreras', "activo" => true, "created_at" => now()],
            ["municipio_id" => 82, "nombre" => 'San Pablo', "activo" => true, "created_at" => now()],

            ["municipio_id" => 83, "nombre" => 'San Pablo', "activo" => true, "created_at" => now()],

            ["municipio_id" => 84, "nombre" => 'Lobatera', "activo" => true, "created_at" => now()],
            ["municipio_id" => 84, "nombre" => 'Constitución', "activo" => true, "created_at" => now()],

            ["municipio_id" => 85, "nombre" => 'Constitución', "activo" => true, "created_at" => now()],

            ["municipio_id" => 86, "nombre" => 'Pedro María Ureña', "activo" => true, "created_at" => now()],
            ["municipio_id" => 86, "nombre" => 'Nueva Arcadia', "activo" => true, "created_at" => now()],

            ["municipio_id" => 87, "nombre" => 'Nueva Arcadia', "activo" => true, "created_at" => now()],

            ["municipio_id" => 88, "nombre" => 'Junín', "activo" => true, "created_at" => now()],
            ["municipio_id" => 88, "nombre" => 'La Petrólea', "activo" => true, "created_at" => now()],
            ["municipio_id" => 88, "nombre" => 'Quinimarí', "activo" => true, "created_at" => now()],
            ["municipio_id" => 88, "nombre" => 'Bramón', "activo" => true, "created_at" => now()],

            ["municipio_id" => 89, "nombre" => 'Bramón', "activo" => true, "created_at" => now()],

            ["municipio_id" => 90, "nombre" => 'Ayacucho', "activo" => true, "created_at" => now()],
            ["municipio_id" => 90, "nombre" => 'Rivas Berti', "activo" => true, "created_at" => now()],
            ["municipio_id" => 90, "nombre" => 'San Pedro del Río', "activo" => true, "created_at" => now()],

            ["municipio_id" => 91, "nombre" => 'Bolívar', "activo" => true, "created_at" => now()],
            ["municipio_id" => 91, "nombre" => 'Juan Vicente Gómez', "activo" => true, "created_at" => now()],
            ["municipio_id" => 91, "nombre" => 'Palotal', "activo" => true, "created_at" => now()],
            ["municipio_id" => 91, "nombre" => 'Isaías Medina Angarita', "activo" => true, "created_at" => now()],

            ["municipio_id" => 92, "nombre" => 'Jáuregui', "activo" => true, "created_at" => now()],
            ["municipio_id" => 92, "nombre" => 'Emilio Constantino Guerrero', "activo" => true, "created_at" => now()],
            ["municipio_id" => 92, "nombre" => 'Monseñor Miguel Antonio Salas', "activo" => true, "created_at" => now()],

            ["municipio_id" => 93, "nombre" => 'Monseñor Miguel Antonio Salas', "activo" => true, "created_at" => now()],

            ["municipio_id" => 94, "nombre" => 'Monseñor Miguel Antonio Salas', "activo" => true, "created_at" => now()],

            ["municipio_id" => 95, "nombre" => 'Monseñor Miguel Antonio Salas', "activo" => true, "created_at" => now()],

            ["municipio_id" => 96, "nombre" => 'Fernández Feo', "activo" => true, "created_at" => now()],
            ["municipio_id" => 96, "nombre" => 'Alberto Adriani', "activo" => true, "created_at" => now()],
            ["municipio_id" => 96, "nombre" => 'Santo Domingo', "activo" => true, "created_at" => now()],

            ["municipio_id" => 97, "nombre" => 'Libertador', "activo" => true, "created_at" => now()],
            ["municipio_id" => 97, "nombre" => 'Don Emeterio Ochoa', "activo" => true, "created_at" => now()],
            ["municipio_id" => 97, "nombre" => 'San Joaquín de Navay', "activo" => true, "created_at" => now()],
            ["municipio_id" => 97, "nombre" => 'Doradas', "activo" => true, "created_at" => now()],

            ["municipio_id" => 98, "nombre" => 'Uribante', "activo" => true, "created_at" => now()],
            ["municipio_id" => 98, "nombre" => 'Cárdenas', "activo" => true, "created_at" => now()],
            ["municipio_id" => 98, "nombre" => 'Juan Pablo Peñaloza', "activo" => true, "created_at" => now()],
            ["municipio_id" => 98, "nombre" => 'Potosí', "activo" => true, "created_at" => now()],

            ["municipio_id" => 99, "nombre" => 'Independencia', "activo" => true, "created_at" => now()],
            ["municipio_id" => 99, "nombre" => 'Román Cárdenas', "activo" => true, "created_at" => now()],
            ["municipio_id" => 99, "nombre" => 'Juan Germán Roscio', "activo" => true, "created_at" => now()],

            ["municipio_id" => 100, "nombre" => 'Libertad', "activo" => true, "created_at" => now()],
            ["municipio_id" => 100, "nombre" => 'Cipriano Castro', "activo" => true, "created_at" => now()],
            ["municipio_id" => 100, "nombre" => 'Manuel Felipe Rugeles', "activo" => true, "created_at" => now()],

            ["municipio_id" => 101, "nombre" => 'García de Hevia', "activo" => true, "created_at" => now()],
            ["municipio_id" => 101, "nombre" => 'Boca de Grita', "activo" => true, "created_at" => now()],
            ["municipio_id" => 101, "nombre" => 'José Antonio Páez', "activo" => true, "created_at" => now()],

            ["municipio_id" => 102, "nombre" => 'José Antonio Páez', "activo" => true, "created_at" => now()],

            ["municipio_id" => 103, "nombre" => 'Panamericano', "activo" => true, "created_at" => now()],
            ["municipio_id" => 103, "nombre" => 'La Palmita', "activo" => true, "created_at" => now()],

            ["municipio_id" => 104, "nombre" => 'Samuel Darío Maldonado', "activo" => true, "created_at" => now()],
            ["municipio_id" => 104, "nombre" => 'Boconó', "activo" => true, "created_at" => now()],
            ["municipio_id" => 104, "nombre" => 'Hernández', "activo" => true, "created_at" => now()],

            ["municipio_id" => 105, "nombre" => 'Hernández', "activo" => true, "created_at" => now()],

            ["municipio_id" => 106, "nombre" => 'Hernández', "activo" => true, "created_at" => now()],

            ["municipio_id" => 107, "nombre" => 'Urbana Bejuma', "activo" => true, "created_at" => now()],
            ["municipio_id" => 107, "nombre" => 'Canoabo', "activo" => true, "created_at" => now()],
            ["municipio_id" => 107, "nombre" => 'Simón Bolívar', "activo" => true, "created_at" => now()],

            ["municipio_id" => 108, "nombre" => 'Urbana Güigüe', "activo" => true, "created_at" => now()],
            ["municipio_id" => 108, "nombre" => 'Tacarigua', "activo" => true, "created_at" => now()],
            ["municipio_id" => 108, "nombre" => 'Belén', "activo" => true, "created_at" => now()],

            ["municipio_id" => 109, "nombre" => 'Urbana Mariara', "activo" => true, "created_at" => now()],
            ["municipio_id" => 109, "nombre" => 'Urbana Aguas Calientes', "activo" => true, "created_at" => now()],

            ["municipio_id" => 110, "nombre" => 'Urbana Aguas Calientes', "activo" => true, "created_at" => now()],
            ["municipio_id" => 110, "nombre" => 'Urbana Guacara', "activo" => true, "created_at" => now()],
            ["municipio_id" => 110, "nombre" => 'Yagua', "activo" => true, "created_at" => now()],
            ["municipio_id" => 110, "nombre" => 'Urbana Ciudad Alianza', "activo" => true, "created_at" => now()],

            ["municipio_id" => 111, "nombre" => 'Urbana Morón', "activo" => true, "created_at" => now()],
            ["municipio_id" => 111, "nombre" => 'Urama', "activo" => true, "created_at" => now()],

            ["municipio_id" => 112, "nombre" => 'Urbana Tocuyito', "activo" => true, "created_at" => now()],
            ["municipio_id" => 112, "nombre" => 'Urbana Independencia', "activo" => true, "created_at" => now()],

            ["municipio_id" => 113, "nombre" => 'Urbana Los Guayos', "activo" => true, "created_at" => now()],

            ["municipio_id" => 114, "nombre" => 'Urbana Miranda', "activo" => true, "created_at" => now()],

            ["municipio_id" => 115, "nombre" => 'Urbana Montalbán', "activo" => true, "created_at" => now()],

            ["municipio_id" => 116, "nombre" => 'Urbana Naguanagua', "activo" => true, "created_at" => now()],

            ["municipio_id" => 117, "nombre" => 'Urbana Bartolomé Salom', "activo" => true, "created_at" => now()],
            ["municipio_id" => 117, "nombre" => 'Borburata', "activo" => true, "created_at" => now()],
            ["municipio_id" => 117, "nombre" => 'Urbana Democracia', "activo" => true, "created_at" => now()],
            ["municipio_id" => 117, "nombre" => 'Urbana Fraternidad', "activo" => true, "created_at" => now()],
            ["municipio_id" => 117, "nombre" => 'Goaigoaza', "activo" => true, "created_at" => now()],
            ["municipio_id" => 117, "nombre" => 'Juan José Flores', "activo" => true, "created_at" => now()],
            ["municipio_id" => 117, "nombre" => 'Patanemo', "activo" => true, "created_at" => now()],
            ["municipio_id" => 117, "nombre" => 'Urbana Unión', "activo" => true, "created_at" => now()],

            ["municipio_id" => 118, "nombre" => 'Urbana San Diego', "activo" => true, "created_at" => now()],

            ["municipio_id" => 119, "nombre" => 'Urbana San Joaquín', "activo" => true, "created_at" => now()],

            ["municipio_id" => 120, "nombre" => 'Urbana Candelaria', "activo" => true, "created_at" => now()],
            ["municipio_id" => 120, "nombre" => 'Urbana Catedral', "activo" => true, "created_at" => now()],
            ["municipio_id" => 120, "nombre" => 'El Socorro', "activo" => true, "created_at" => now()],
            ["municipio_id" => 120, "nombre" => 'Urbana Miguel Peña', "activo" => true, "created_at" => now()],
            ["municipio_id" => 120, "nombre" => 'Urbana San Blás', "activo" => true, "created_at" => now()],
            ["municipio_id" => 120, "nombre" => 'Urbana Santa Rosa', "activo" => true, "created_at" => now()],
            ["municipio_id" => 120, "nombre" => 'Urbana San José', "activo" => true, "created_at" => now()],
            ["municipio_id" => 120, "nombre" => 'Urbana Rafael Urdaneta', "activo" => true, "created_at" => now()],
            ["municipio_id" => 120, "nombre" => 'Negro Primero', "activo" => true, "created_at" => now()],

            ["municipio_id" => 121, "nombre" => 'Negro Primero', "activo" => true, "created_at" => now()],
            ["municipio_id" => 121, "nombre" => 'Cojedes', "activo" => true, "created_at" => now()],
            ["municipio_id" => 121, "nombre" => 'Juan de Mata Suárez', "activo" => true, "created_at" => now()],

            ["municipio_id" => 122, "nombre" => 'Tinaquillo', "activo" => true, "created_at" => now()],

            ["municipio_id" => 123, "nombre" => 'El Baúl', "activo" => true, "created_at" => now()],
            ["municipio_id" => 123, "nombre" => 'Sucre', "activo" => true, "created_at" => now()],

            ["municipio_id" => 124, "nombre" => 'Macapo', "activo" => true, "created_at" => now()],
            ["municipio_id" => 124, "nombre" => 'La Aguadita', "activo" => true, "created_at" => now()],
            ["municipio_id" => 125, "nombre" => 'El Pao', "activo" => true, "created_at" => now()],

            ["municipio_id" => 126, "nombre" => 'El Pao', "activo" => true, "created_at" => now()],
            ["municipio_id" => 126, "nombre" => 'El Amparo', "activo" => true, "created_at" => now()],
            ["municipio_id" => 126, "nombre" => 'Libertad de Cojedes', "activo" => true, "created_at" => now()],

            ["municipio_id" => 127, "nombre" => 'Rómulo Gallegos', "activo" => true, "created_at" => now()],

            ["municipio_id" => 128, "nombre" => 'Rómulo Gallegos', "activo" => true, "created_at" => now()],
            ["municipio_id" => 128, "nombre" => 'San Carlos de Austria', "activo" => true, "created_at" => now()],
            ["municipio_id" => 128, "nombre" => 'Juan Ángel Bravo', "activo" => true, "created_at" => now()],
            ["municipio_id" => 128, "nombre" => 'Manuel Manrique', "activo" => true, "created_at" => now()],

            ["municipio_id" => 129, "nombre" => 'Manuel Manrique', "activo" => true, "created_at" => now()],
            ["municipio_id" => 129, "nombre" => 'General en Jefe José Laurencio Silva', "activo" => true, "created_at" => now()],

            ["municipio_id" => 130, "nombre" => 'Catedral', "activo" => true, "created_at" => now()],
            ["municipio_id" => 130, "nombre" => 'Concepción', "activo" => true, "created_at" => now()],
            ["municipio_id" => 130, "nombre" => 'El Cují', "activo" => true, "created_at" => now()],
            ["municipio_id" => 130, "nombre" => 'Juan de Villegas', "activo" => true, "created_at" => now()],
            ["municipio_id" => 130, "nombre" => 'Santa Rosa', "activo" => true, "created_at" => now()],
            ["municipio_id" => 130, "nombre" => 'Tamaca', "activo" => true, "created_at" => now()],
            ["municipio_id" => 130, "nombre" => 'Unión', "activo" => true, "created_at" => now()],
            ["municipio_id" => 130, "nombre" => 'Aguedo Felipe Alvarado', "activo" => true, "created_at" => now()],
            ["municipio_id" => 130, "nombre" => 'Buena Vista', "activo" => true, "created_at" => now()],
            ["municipio_id" => 130, "nombre" => 'Juárez', "activo" => true, "created_at" => now()],

            ["municipio_id" => 131, "nombre" => 'Juárez', "activo" => true, "created_at" => now()],
            ["municipio_id" => 131, "nombre" => 'Capital Sarare', "activo" => true, "created_at" => now()],
            ["municipio_id" => 131, "nombre" => 'Buría', "activo" => true, "created_at" => now()],
            ["municipio_id" => 131, "nombre" => 'Gustavo Vegas León', "activo" => true, "created_at" => now()],

            ["municipio_id" => 132, "nombre" => 'Gustavo Vegas León', "activo" => true, "created_at" => now()],
            ["municipio_id" => 132, "nombre" => 'Capital Bolivar', "activo" => true, "created_at" => now()],
            ["municipio_id" => 132, "nombre" => 'Bolívar', "activo" => true, "created_at" => now()],
            ["municipio_id" => 132, "nombre" => 'Morán', "activo" => true, "created_at" => now()],
            ["municipio_id" => 132, "nombre" => 'La Candelaria', "activo" => true, "created_at" => now()],
            ["municipio_id" => 132, "nombre" => 'Anzoátegui', "activo" => true, "created_at" => now()],
            ["municipio_id" => 132, "nombre" => 'Hilario Luna y Luna', "activo" => true, "created_at" => now()],
            ["municipio_id" => 132, "nombre" => 'Guárico', "activo" => true, "created_at" => now()],
            ["municipio_id" => 132, "nombre" => 'Humocaro Alto', "activo" => true, "created_at" => now()],
            ["municipio_id" => 132, "nombre" => 'Humocaro Bajo', "activo" => true, "created_at" => now()],

            ["municipio_id" => 133, "nombre" => 'Capital Cabudare', "activo" => true, "created_at" => now()],
            ["municipio_id" => 133, "nombre" => 'José Gregorio Bastidas', "activo" => true, "created_at" => now()],
            ["municipio_id" => 133, "nombre" => 'Agua Viva', "activo" => true, "created_at" => now()],

            ["municipio_id" => 134, "nombre" => 'Agua Viva', "activo" => true, "created_at" => now()],
            ["municipio_id" => 134, "nombre" => 'Capital Duaca', "activo" => true, "created_at" => now()],
            ["municipio_id" => 134, "nombre" => 'Freitez', "activo" => true, "created_at" => now()],
            ["municipio_id" => 134, "nombre" => 'José María Blanco', "activo" => true, "created_at" => now()],

            ["municipio_id" => 135, "nombre" => 'Capital Andrés Eloy Blanco', "activo" => true, "created_at" => now()],
            ["municipio_id" => 135, "nombre" => 'Pío Tamayo', "activo" => true, "created_at" => now()],
            ["municipio_id" => 135, "nombre" => 'Quebrada Honda de Guache', "activo" => true, "created_at" => now()],
            ["municipio_id" => 135, "nombre" => 'Yacambú', "activo" => true, "created_at" => now()],

            ["municipio_id" => 136, "nombre" => 'Siquisique', "activo" => true, "created_at" => now()],
            ["municipio_id" => 136, "nombre" => 'Moroturo', "activo" => true, "created_at" => now()],
            ["municipio_id" => 136, "nombre" => 'San Miguel', "activo" => true, "created_at" => now()],
            ["municipio_id" => 136, "nombre" => 'Xaquas', "activo" => true, "created_at" => now()],

            ["municipio_id" => 137, "nombre" => 'Trinidad Samuel', "activo" => true, "created_at" => now()],
            ["municipio_id" => 137, "nombre" => 'Antonio Díaz', "activo" => true, "created_at" => now()],
            ["municipio_id" => 137, "nombre" => 'Camacaro', "activo" => true, "created_at" => now()],
            ["municipio_id" => 137, "nombre" => 'Castañeda', "activo" => true, "created_at" => now()],
            ["municipio_id" => 137, "nombre" => 'Cecilio Zubillaga', "activo" => true, "created_at" => now()],
            ["municipio_id" => 137, "nombre" => 'Chiquinquirá', "activo" => true, "created_at" => now()],
            ["municipio_id" => 137, "nombre" => 'El Blanco', "activo" => true, "created_at" => now()],
            ["municipio_id" => 137, "nombre" => 'Espinoza de Los Monteros', "activo" => true, "created_at" => now()],
            ["municipio_id" => 137, "nombre" => 'Lara', "activo" => true, "created_at" => now()],
            ["municipio_id" => 137, "nombre" => 'Las Mercedes', "activo" => true, "created_at" => now()],
            ["municipio_id" => 137, "nombre" => 'Manuel Morillo', "activo" => true, "created_at" => now()],
            ["municipio_id" => 137, "nombre" => 'Montaña Verde', "activo" => true, "created_at" => now()],
            ["municipio_id" => 137, "nombre" => 'Montes de Oca', "activo" => true, "created_at" => now()],

            ["municipio_id" => 137, "nombre" => 'Torres', "activo" => true, "created_at" => now()],
            ["municipio_id" => 137, "nombre" => 'Reyes Vargas', "activo" => true, "created_at" => now()],
            ["municipio_id" => 137, "nombre" => 'Altagracia', "activo" => true, "created_at" => now()],

            ["municipio_id" => 138, "nombre" => 'Altagracia', "activo" => true, "created_at" => now()],
            ["municipio_id" => 138, "nombre" => 'Juan Bautista Rodríguez', "activo" => true, "created_at" => now()],
            ["municipio_id" => 138, "nombre" => 'Cuara', "activo" => true, "created_at" => now()],
            ["municipio_id" => 138, "nombre" => 'Diego de Lozada', "activo" => true, "created_at" => now()],
            ["municipio_id" => 138, "nombre" => 'Paraíso de San José', "activo" => true, "created_at" => now()],
            ["municipio_id" => 138, "nombre" => 'San Miguel', "activo" => true, "created_at" => now()],
            ["municipio_id" => 138, "nombre" => 'Tintorero', "activo" => true, "created_at" => now()],
            ["municipio_id" => 138, "nombre" => 'José Bernardo Dorante', "activo" => true, "created_at" => now()],
            ["municipio_id" => 138, "nombre" => 'Coronel Mariano Peraza', "activo" => true, "created_at" => now()],

            ["municipio_id" => 139, "nombre" => 'Coronel Mariano Peraza', "activo" => true, "created_at" => now()],

            ["municipio_id" => 140, "nombre" => 'Capital Araure', "activo" => true, "created_at" => now()],
            ["municipio_id" => 140, "nombre" => 'Río Acarigua', "activo" => true, "created_at" => now()],

            ["municipio_id" => 141, "nombre" => 'Capital Esteller', "activo" => true, "created_at" => now()],
            ["municipio_id" => 141, "nombre" => 'Uveral', "activo" => true, "created_at" => now()],

            ["municipio_id" => 142, "nombre" => 'Capital Guanare', "activo" => true, "created_at" => now()],
            ["municipio_id" => 142, "nombre" => 'Córdoba', "activo" => true, "created_at" => now()],
            ["municipio_id" => 142, "nombre" => 'San Juan de Guanaguanare', "activo" => true, "created_at" => now()],
            ["municipio_id" => 142, "nombre" => 'Virgen de la Coromoto', "activo" => true, "created_at" => now()],
            ["municipio_id" => 142, "nombre" => 'San José de la Montaña', "activo" => true, "created_at" => now()],

            ["municipio_id" => 143, "nombre" => 'Capital Guanarito', "activo" => true, "created_at" => now()],
            ["municipio_id" => 143, "nombre" => 'Trinidad de la Capilla', "activo" => true, "created_at" => now()],
            ["municipio_id" => 143, "nombre" => 'Divina Pastora', "activo" => true, "created_at" => now()],

            ["municipio_id" => 144, "nombre" => 'Capital Monseñor José Vicente de Unda', "activo" => true, "created_at" => now()],
            ["municipio_id" => 144, "nombre" => 'Peña Blanca', "activo" => true, "created_at" => now()],

            ["municipio_id" => 145, "nombre" => 'Capital Ospino', "activo" => true, "created_at" => now()],
            ["municipio_id" => 145, "nombre" => 'Aparición', "activo" => true, "created_at" => now()],
            ["municipio_id" => 145, "nombre" => 'La Estación', "activo" => true, "created_at" => now()],

            ["municipio_id" => 146, "nombre" => 'Capital Páez', "activo" => true, "created_at" => now()],
            ["municipio_id" => 146, "nombre" => 'Payara', "activo" => true, "created_at" => now()],
            ["municipio_id" => 146, "nombre" => 'Ramón Peraza', "activo" => true, "created_at" => now()],
            ["municipio_id" => 146, "nombre" => 'Pimpinela', "activo" => true, "created_at" => now()],

            ["municipio_id" => 147, "nombre" => 'Capital Papelón', "activo" => true, "created_at" => now()],
            ["municipio_id" => 147, "nombre" => 'Caño Delgadito', "activo" => true, "created_at" => now()],

            ["municipio_id" => 148, "nombre" => 'Capital San Genaro de Boconoito', "activo" => true, "created_at" => now()],
            ["municipio_id" => 148, "nombre" => 'Antolín Tovar', "activo" => true, "created_at" => now()],

            ["municipio_id" => 149, "nombre" => 'Capital San Rafael de Onoto', "activo" => true, "created_at" => now()],
            ["municipio_id" => 149, "nombre" => 'Santa Fe', "activo" => true, "created_at" => now()],
            ["municipio_id" => 149, "nombre" => 'Thermo Morles', "activo" => true, "created_at" => now()],

            ["municipio_id" => 150, "nombre" => 'Capital Santa Rosalia', "activo" => true, "created_at" => now()],
            ["municipio_id" => 150, "nombre" => 'Florida', "activo" => true, "created_at" => now()],

            ["municipio_id" => 151, "nombre" => 'Capital Sucre', "activo" => true, "created_at" => now()],
            ["municipio_id" => 151, "nombre" => 'Concepción', "activo" => true, "created_at" => now()],
            ["municipio_id" => 151, "nombre" => 'San Rafael de Palo Alzado', "activo" => true, "created_at" => now()],
            ["municipio_id" => 151, "nombre" => 'Uvencio Antonio Velásquez', "activo" => true, "created_at" => now()],
            ["municipio_id" => 151, "nombre" => 'San José de Saguaz', "activo" => true, "created_at" => now()],
            ["municipio_id" => 151, "nombre" => 'Villa Rosa', "activo" => true, "created_at" => now()],

            ["municipio_id" => 152, "nombre" => 'Capital Turén', "activo" => true, "created_at" => now()],
            ["municipio_id" => 152, "nombre" => 'Canelones', "activo" => true, "created_at" => now()],
            ["municipio_id" => 152, "nombre" => 'Santa Cruz', "activo" => true, "created_at" => now()],
            ["municipio_id" => 152, "nombre" => 'San Isidro Labrador', "activo" => true, "created_at" => now()],

            ["municipio_id" => 153, "nombre" => 'San Isidro Labrador', "activo" => true, "created_at" => now()],

            ["municipio_id" => 154, "nombre" => 'San Isidro Labrador', "activo" => true, "created_at" => now()],

            ["municipio_id" => 155, "nombre" => 'Capital Bruzual', "activo" => true, "created_at" => now()],
            ["municipio_id" => 155, "nombre" => 'Campo Elías', "activo" => true, "created_at" => now()],

            ["municipio_id" => 156, "nombre" => 'Campo Elías', "activo" => true, "created_at" => now()],

            ["municipio_id" => 157, "nombre" => 'Campo Elías', "activo" => true, "created_at" => now()],

            ["municipio_id" => 158, "nombre" => 'Campo Elías', "activo" => true, "created_at" => now()],

            ["municipio_id" => 159, "nombre" => 'Campo Elías', "activo" => true, "created_at" => now()],

            ["municipio_id" => 160, "nombre" => 'Campo Elías', "activo" => true, "created_at" => now()],

            ["municipio_id" => 161, "nombre" => 'Capital Nirgua', "activo" => true, "created_at" => now()],
            ["municipio_id" => 161, "nombre" => 'Salom', "activo" => true, "created_at" => now()],
            ["municipio_id" => 161, "nombre" => 'Temerla', "activo" => true, "created_at" => now()],

            ["municipio_id" => 162, "nombre" => 'Capital Peña', "activo" => true, "created_at" => now()],
            ["municipio_id" => 162, "nombre" => 'San Andrés', "activo" => true, "created_at" => now()],

            ["municipio_id" => 163, "nombre" => 'Capital San Felipe', "activo" => true, "created_at" => now()],
            ["municipio_id" => 163, "nombre" => 'San Javier', "activo" => true, "created_at" => now()],
            ["municipio_id" => 163, "nombre" => 'Albarico', "activo" => true, "created_at" => now()],

            ["municipio_id" => 164, "nombre" => 'Albarico', "activo" => true, "created_at" => now()],

            ["municipio_id" => 165, "nombre" => 'Albarico', "activo" => true, "created_at" => now()],

            ["municipio_id" => 166, "nombre" => 'Capital Veroes', "activo" => true, "created_at" => now()],
            ["municipio_id" => 166, "nombre" => 'El Guayabo', "activo" => true, "created_at" => now()],

            ["municipio_id" => 167, "nombre" => 'Fernando Girón Tovar', "activo" => true, "created_at" => now()],
            ["municipio_id" => 167, "nombre" => 'Luis Alberto Gómez', "activo" => true, "created_at" => now()],
            ["municipio_id" => 167, "nombre" => 'Parhueña', "activo" => true, "created_at" => now()],
            ["municipio_id" => 167, "nombre" => 'Platanillal', "activo" => true, "created_at" => now()],

            ["municipio_id" => 168, "nombre" => 'Huachamacare', "activo" => true, "created_at" => now()],
            ["municipio_id" => 168, "nombre" => 'Marawaka', "activo" => true, "created_at" => now()],
            ["municipio_id" => 168, "nombre" => 'Mavaca', "activo" => true, "created_at" => now()],
            ["municipio_id" => 168, "nombre" => 'Sierra Parima', "activo" => true, "created_at" => now()],

            ["municipio_id" => 169, "nombre" => 'Ucata', "activo" => true, "created_at" => now()],
            ["municipio_id" => 169, "nombre" => 'Yapacana', "activo" => true, "created_at" => now()],
            ["municipio_id" => 169, "nombre" => 'Caname', "activo" => true, "created_at" => now()],

            ["municipio_id" => 170, "nombre" => 'Victorino', "activo" => true, "created_at" => now()],

            ["municipio_id" => 171, "nombre" => 'Samariapo', "activo" => true, "created_at" => now()],
            ["municipio_id" => 171, "nombre" => 'Sipapo', "activo" => true, "created_at" => now()],
            ["municipio_id" => 171, "nombre" => 'Munduapo', "activo" => true, "created_at" => now()],
            ["municipio_id" => 171, "nombre" => 'Guayapo', "activo" => true, "created_at" => now()],

            ["municipio_id" => 172, "nombre" => 'Alto Ventuari', "activo" => true, "created_at" => now()],
            ["municipio_id" => 172, "nombre" => 'Medio Ventuari', "activo" => true, "created_at" => now()],
            ["municipio_id" => 172, "nombre" => 'Bajo Ventuari', "activo" => true, "created_at" => now()],

            ["municipio_id" => 173, "nombre" => 'Solano', "activo" => true, "created_at" => now()],
            ["municipio_id" => 173, "nombre" => 'Casiquiare', "activo" => true, "created_at" => now()],
            ["municipio_id" => 173, "nombre" => 'Cocuy', "activo" => true, "created_at" => now()],

            ["municipio_id" => 174, "nombre" => 'Urbana San Fernando', "activo" => true, "created_at" => now()],
            ["municipio_id" => 174, "nombre" => 'El Recreo', "activo" => true, "created_at" => now()],
            ["municipio_id" => 174, "nombre" => 'Peñalver', "activo" => true, "created_at" => now()],
            ["municipio_id" => 174, "nombre" => 'San Rafael de Atamaica', "activo" => true, "created_at" => now()],

            ["municipio_id" => 175, "nombre" => 'Urbana Achaguas', "activo" => true, "created_at" => now()],
            ["municipio_id" => 175, "nombre" => 'El Yagual', "activo" => true, "created_at" => now()],
            ["municipio_id" => 175, "nombre" => 'Guachara', "activo" => true, "created_at" => now()],
            ["municipio_id" => 175, "nombre" => 'Queseras del Medio', "activo" => true, "created_at" => now()],
            ["municipio_id" => 175, "nombre" => 'Mucuritas', "activo" => true, "created_at" => now()],

            ["municipio_id" => 176, "nombre" => 'Urbana San Juan de Payara', "activo" => true, "created_at" => now()],
            ["municipio_id" => 176, "nombre" => 'Codazzi', "activo" => true, "created_at" => now()],
            ["municipio_id" => 176, "nombre" => 'Cunaviche', "activo" => true, "created_at" => now()],

            ["municipio_id" => 177, "nombre" => 'Urbana Bruzual', "activo" => true, "created_at" => now()],
            ["municipio_id" => 177, "nombre" => 'Quintero', "activo" => true, "created_at" => now()],
            ["municipio_id" => 177, "nombre" => 'Rincón Hondo', "activo" => true, "created_at" => now()],
            ["municipio_id" => 177, "nombre" => 'Mantecal', "activo" => true, "created_at" => now()],
            ["municipio_id" => 177, "nombre" => 'San Vicente', "activo" => true, "created_at" => now()],

            ["municipio_id" => 178, "nombre" => 'Urbana Biruaca', "activo" => true, "created_at" => now()],

            ["municipio_id" => 179, "nombre" => 'Urbana Guasdualito', "activo" => true, "created_at" => now()],
            ["municipio_id" => 179, "nombre" => 'Aramendi', "activo" => true, "created_at" => now()],
            ["municipio_id" => 179, "nombre" => 'El Amparo', "activo" => true, "created_at" => now()],
            ["municipio_id" => 179, "nombre" => 'San Camilo', "activo" => true, "created_at" => now()],
            ["municipio_id" => 179, "nombre" => 'Urdaneta', "activo" => true, "created_at" => now()],

            ["municipio_id" => 180, "nombre" => 'Urbana Elorza', "activo" => true, "created_at" => now()],
            ["municipio_id" => 180, "nombre" => 'La Trinidad', "activo" => true, "created_at" => now()],

            ["municipio_id" => 181, "nombre" => 'Urbana Las Delicias', "activo" => true, "created_at" => now()],
            ["municipio_id" => 181, "nombre" => 'Urbana Madre María de San José', "activo" => true, "created_at" => now()],
            ["municipio_id" => 181, "nombre" => 'Urbana Joaquín Crespo', "activo" => true, "created_at" => now()],
            ["municipio_id" => 181, "nombre" => 'Urbana Pedro José Ovalles', "activo" => true, "created_at" => now()],
            ["municipio_id" => 181, "nombre" => 'Urbana José Casanova Godoy', "activo" => true, "created_at" => now()],
            ["municipio_id" => 181, "nombre" => 'Urbana Andrés Eloy Blanco', "activo" => true, "created_at" => now()],
            ["municipio_id" => 181, "nombre" => 'Urbana Los Tacariguas', "activo" => true, "created_at" => now()],
            ["municipio_id" => 181, "nombre" => 'Choroní', "activo" => true, "created_at" => now()],

            ["municipio_id" => 182, "nombre" => 'Francisco Linares Alcántara', "activo" => true, "created_at" => now()],
            ["municipio_id" => 182, "nombre" => 'Francisco de Miranda', "activo" => true, "created_at" => now()],
            ["municipio_id" => 182, "nombre" => 'Monseñor Feliciano González', "activo" => true, "created_at" => now()],

            ["municipio_id" => 183, "nombre" => 'Mario Briceño Iragorry', "activo" => true, "created_at" => now()],
            ["municipio_id" => 183, "nombre" => 'Caña de Azúcar', "activo" => true, "created_at" => now()],

            ["municipio_id" => 184, "nombre" => 'Santiago Mariño', "activo" => true, "created_at" => now()],
            ["municipio_id" => 184, "nombre" => 'Arévalo Aponte', "activo" => true, "created_at" => now()],
            ["municipio_id" => 184, "nombre" => 'Chuao', "activo" => true, "created_at" => now()],
            ["municipio_id" => 184, "nombre" => 'Alfredo Pacheco Miranda', "activo" => true, "created_at" => now()],
            ["municipio_id" => 184, "nombre" => 'Samán de Güere', "activo" => true, "created_at" => now()],

            ["municipio_id" => 185, "nombre" => 'Samán de Güere', "activo" => true, "created_at" => now()],

            ["municipio_id" => 186, "nombre" => 'Urbana Juan Vicente Bolívar', "activo" => true, "created_at" => now()],
            ["municipio_id" => 186, "nombre" => 'Castor Nieves Ríos', "activo" => true, "created_at" => now()],
            ["municipio_id" => 186, "nombre" => 'Las Guacamayas', "activo" => true, "created_at" => now()],
            ["municipio_id" => 186, "nombre" => 'Pao de Zárate', "activo" => true, "created_at" => now()],
            ["municipio_id" => 186, "nombre" => 'Zuata', "activo" => true, "created_at" => now()],

            ["municipio_id" => 187, "nombre" => 'Zuata', "activo" => true, "created_at" => now()],

            ["municipio_id" => 188, "nombre" => 'Zuata', "activo" => true, "created_at" => now()],

            ["municipio_id" => 189, "nombre" => 'Zuata', "activo" => true, "created_at" => now()],

            ["municipio_id" => 190, "nombre" => 'Zamora', "activo" => true, "created_at" => now()],
            ["municipio_id" => 190, "nombre" => 'San Francisco de Asís', "activo" => true, "created_at" => now()],
            ["municipio_id" => 190, "nombre" => 'Valles de Tucutunemo', "activo" => true, "created_at" => now()],
            ["municipio_id" => 190, "nombre" => 'Augusto Mijares', "activo" => true, "created_at" => now()],
            ["municipio_id" => 190, "nombre" => 'Magdaleno', "activo" => true, "created_at" => now()],

            ["municipio_id" => 191, "nombre" => 'Sucre', "activo" => true, "created_at" => now()],
            ["municipio_id" => 191, "nombre" => 'Bella Vista', "activo" => true, "created_at" => now()],

            ["municipio_id" => 192, "nombre" => 'Bella Vista', "activo" => true, "created_at" => now()],

            ["municipio_id" => 193, "nombre" => 'Libertador', "activo" => true, "created_at" => now()],
            ["municipio_id" => 193, "nombre" => 'San Martín de Porres', "activo" => true, "created_at" => now()],

            ["municipio_id" => 194, "nombre" => 'Camatagua', "activo" => true, "created_at" => now()],
            ["municipio_id" => 194, "nombre" => 'Carmen de Cura', "activo" => true, "created_at" => now()],

            ["municipio_id" => 195, "nombre" => 'San Casimiro', "activo" => true, "created_at" => now()],
            ["municipio_id" => 195, "nombre" => 'Güiripa', "activo" => true, "created_at" => now()],
            ["municipio_id" => 195, "nombre" => 'Ollas de Caramacate', "activo" => true, "created_at" => now()],
            ["municipio_id" => 195, "nombre" => 'Valle Morín', "activo" => true, "created_at" => now()],

            ["municipio_id" => 196, "nombre" => 'Valle Morín', "activo" => true, "created_at" => now()],

            ["municipio_id" => 197, "nombre" => 'Urdaneta', "activo" => true, "created_at" => now()],
            ["municipio_id" => 197, "nombre" => 'Las Peñitas', "activo" => true, "created_at" => now()],
            ["municipio_id" => 197, "nombre" => 'San Francisco de Cara', "activo" => true, "created_at" => now()],
            ["municipio_id" => 197, "nombre" => 'Taguay', "activo" => true, "created_at" => now()],

            ["municipio_id" => 198, "nombre" => 'Santos Michelena', "activo" => true, "created_at" => now()],
            ["municipio_id" => 198, "nombre" => 'Tiara', "activo" => true, "created_at" => now()],

            ["municipio_id" => 199, "nombre" => 'Capital San Juan de Los Morros', "activo" => true, "created_at" => now()],
            ["municipio_id" => 199, "nombre" => 'Cantagallo', "activo" => true, "created_at" => now()],
            ["municipio_id" => 199, "nombre" => 'Parapara', "activo" => true, "created_at" => now()],


            ["municipio_id" => 200, "nombre" => 'Calabozo', "activo" => true, "created_at" => now()],
            ["municipio_id" => 200, "nombre" => 'El Calvario', "activo" => true, "created_at" => now()],
            ["municipio_id" => 200, "nombre" => 'El Rastro', "activo" => true, "created_at" => now()],
            ["municipio_id" => 200, "nombre" => 'Guardatinajas', "activo" => true, "created_at" => now()],

            ["municipio_id" => 201, "nombre" => 'Valle de la Pascua', "activo" => true, "created_at" => now()],
            ["municipio_id" => 201, "nombre" => 'Espino', "activo" => true, "created_at" => now()],

            ["municipio_id" => 202, "nombre" => 'Chaguaramas', "activo" => true, "created_at" => now()],

            ["municipio_id" => 203, "nombre" => 'El Socorro', "activo" => true, "created_at" => now()],

            ["municipio_id" => 204, "nombre" => 'Capital Las Mercedes', "activo" => true, "created_at" => now()],
            ["municipio_id" => 204, "nombre" => 'Cabruta', "activo" => true, "created_at" => now()],
            ["municipio_id" => 204, "nombre" => 'Santa Rita de Manapire', "activo" => true, "created_at" => now()],

            ["municipio_id" => 205, "nombre" => 'Tucupido', "activo" => true, "created_at" => now()],
            ["municipio_id" => 205, "nombre" => 'San Rafael de Laya', "activo" => true, "created_at" => now()],

            ["municipio_id" => 206, "nombre" => 'Santa María de Ipire', "activo" => true, "created_at" => now()],
            ["municipio_id" => 206, "nombre" => 'Altamira', "activo" => true, "created_at" => now()],

            ["municipio_id" => 207, "nombre" => 'Capital Altagracia de Orituco', "activo" => true, "created_at" => now()],
            ["municipio_id" => 207, "nombre" => 'Lezama', "activo" => true, "created_at" => now()],
            ["municipio_id" => 207, "nombre" => 'Libertad de Orituco', "activo" => true, "created_at" => now()],
            ["municipio_id" => 207, "nombre" => 'Paso Real de Macaira', "activo" => true, "created_at" => now()],
            ["municipio_id" => 207, "nombre" => 'San Francisco de Macaira', "activo" => true, "created_at" => now()],
            ["municipio_id" => 207, "nombre" => 'San Rafael de Orituco', "activo" => true, "created_at" => now()],
            ["municipio_id" => 207, "nombre" => 'Soublette', "activo" => true, "created_at" => now()],

            ["municipio_id" => 208, "nombre" => 'San José de Guaribe', "activo" => true, "created_at" => now()],

            ["municipio_id" => 209, "nombre" => 'Capital Zaraza', "activo" => true, "created_at" => now()],
            ["municipio_id" => 209, "nombre" => 'San José de Unare', "activo" => true, "created_at" => now()],

            ["municipio_id" => 210, "nombre" => 'Capital Ortíz', "activo" => true, "created_at" => now()],
            ["municipio_id" => 210, "nombre" => 'San Lorenzo de Tiznado', "activo" => true, "created_at" => now()],
            ["municipio_id" => 210, "nombre" => 'San Francisco de Tiznado', "activo" => true, "created_at" => now()],
            ["municipio_id" => 210, "nombre" => 'San José de Tiznado', "activo" => true, "created_at" => now()],

            ["municipio_id" => 211, "nombre" => 'Capital Camaguán', "activo" => true, "created_at" => now()],
            ["municipio_id" => 211, "nombre" => 'Puerto Miranda', "activo" => true, "created_at" => now()],
            ["municipio_id" => 211, "nombre" => 'Uverito', "activo" => true, "created_at" => now()],

            ["municipio_id" => 212, "nombre" => 'San Gerónimo de Guayabal', "activo" => true, "created_at" => now()],
            ["municipio_id" => 212, "nombre" => 'Cazorla', "activo" => true, "created_at" => now()],

            ["municipio_id" => 213, "nombre" => 'Capital El Sombrero', "activo" => true, "created_at" => now()],
            ["municipio_id" => 213, "nombre" => 'Sosa', "activo" => true, "created_at" => now()],

            ["municipio_id" => 214, "nombre" => 'San Antonio', "activo" => true, "created_at" => now()],
            ["municipio_id" => 214, "nombre" => 'San Gabriel', "activo" => true, "created_at" => now()],
            ["municipio_id" => 214, "nombre" => 'Santa Ana', "activo" => true, "created_at" => now()],
            ["municipio_id" => 214, "nombre" => 'Guzmán Guillermo', "activo" => true, "created_at" => now()],
            ["municipio_id" => 214, "nombre" => 'Mitare', "activo" => true, "created_at" => now()],
            ["municipio_id" => 214, "nombre" => 'Río Seco', "activo" => true, "created_at" => now()],
            ["municipio_id" => 214, "nombre" => 'Sabaneta', "activo" => true, "created_at" => now()],

            ["municipio_id" => 215, "nombre" => 'Carirubana', "activo" => true, "created_at" => now()],
            ["municipio_id" => 215, "nombre" => 'Norte', "activo" => true, "created_at" => now()],
            ["municipio_id" => 215, "nombre" => 'Punta Cardón', "activo" => true, "created_at" => now()],
            ["municipio_id" => 215, "nombre" => 'Santa Ana', "activo" => true, "created_at" => now()],

            ["municipio_id" => 216, "nombre" => 'Pueblo Nuevo', "activo" => true, "created_at" => now()],
            ["municipio_id" => 216, "nombre" => 'Adícora', "activo" => true, "created_at" => now()],
            ["municipio_id" => 216, "nombre" => 'Baraived', "activo" => true, "created_at" => now()],
            ["municipio_id" => 216, "nombre" => 'Buena Vista', "activo" => true, "created_at" => now()],
            ["municipio_id" => 216, "nombre" => 'Jadacaquiva', "activo" => true, "created_at" => now()],
            ["municipio_id" => 216, "nombre" => 'Moruy', "activo" => true, "created_at" => now()],
            ["municipio_id" => 216, "nombre" => 'Adaure', "activo" => true, "created_at" => now()],
            ["municipio_id" => 216, "nombre" => 'El Hato', "activo" => true, "created_at" => now()],
            ["municipio_id" => 216, "nombre" => 'El Vínculo', "activo" => true, "created_at" => now()],

            ["municipio_id" => 217, "nombre" => 'La Vela de Coro', "activo" => true, "created_at" => now()],
            ["municipio_id" => 217, "nombre" => 'Acurigua', "activo" => true, "created_at" => now()],
            ["municipio_id" => 217, "nombre" => 'Guaibacoa', "activo" => true, "created_at" => now()],
            ["municipio_id" => 217, "nombre" => 'Las Calderas', "activo" => true, "created_at" => now()],
            ["municipio_id" => 217, "nombre" => 'Macoruca', "activo" => true, "created_at" => now()],

            ["municipio_id" => 218, "nombre" => 'Puerto Cumarebo', "activo" => true, "created_at" => now()],
            ["municipio_id" => 218, "nombre" => 'La Ciénaga', "activo" => true, "created_at" => now()],
            ["municipio_id" => 218, "nombre" => 'La Soledad', "activo" => true, "created_at" => now()],
            ["municipio_id" => 218, "nombre" => 'Pueblo Cumarebo', "activo" => true, "created_at" => now()],
            ["municipio_id" => 218, "nombre" => 'Zazárida', "activo" => true, "created_at" => now()],

            ["municipio_id" => 219, "nombre" => 'Píritu', "activo" => true, "created_at" => now()],
            ["municipio_id" => 219, "nombre" => 'San José de la Costa', "activo" => true, "created_at" => now()],

            ["municipio_id" => 220, "nombre" => 'San José de la Costa', "activo" => true, "created_at" => now()],

            ["municipio_id" => 221, "nombre" => 'Tucacas', "activo" => true, "created_at" => now()],
            ["municipio_id" => 221, "nombre" => 'Boca de Aroa', "activo" => true, "created_at" => now()],

            ["municipio_id" => 222, "nombre" => 'Chichiriviche', "activo" => true, "created_at" => now()],
            ["municipio_id" => 222, "nombre" => 'Boca de Tocuyo', "activo" => true, "created_at" => now()],
            ["municipio_id" => 222, "nombre" => 'Tocuyo de la Costa', "activo" => true, "created_at" => now()],

            ["municipio_id" => 223, "nombre" => 'Tocuyo de la Costa', "activo" => true, "created_at" => now()],

            ["municipio_id" => 224, "nombre" => 'San Juan de los Cayos', "activo" => true, "created_at" => now()],
            ["municipio_id" => 224, "nombre" => 'Capadare', "activo" => true, "created_at" => now()],
            ["municipio_id" => 224, "nombre" => 'La Pastora', "activo" => true, "created_at" => now()],
            ["municipio_id" => 224, "nombre" => 'Libertador', "activo" => true, "created_at" => now()],

            ["municipio_id" => 225, "nombre" => 'Jacura', "activo" => true, "created_at" => now()],
            ["municipio_id" => 225, "nombre" => 'Agua Linda', "activo" => true, "created_at" => now()],
            ["municipio_id" => 225, "nombre" => 'Araurima', "activo" => true, "created_at" => now()],

            ["municipio_id" => 226, "nombre" => 'Araurima', "activo" => true, "created_at" => now()],

            ["municipio_id" => 227, "nombre" => 'Araurima', "activo" => true, "created_at" => now()],

            ["municipio_id" => 228, "nombre" => 'Los Taques', "activo" => true, "created_at" => now()],
            ["municipio_id" => 228, "nombre" => 'Judibana', "activo" => true, "created_at" => now()],

            ["municipio_id" => 229, "nombre" => 'Churuguara', "activo" => true, "created_at" => now()],
            ["municipio_id" => 229, "nombre" => 'El Paují', "activo" => true, "created_at" => now()],
            ["municipio_id" => 229, "nombre" => 'Independencia', "activo" => true, "created_at" => now()],
            ["municipio_id" => 229, "nombre" => 'Agua Larga', "activo" => true, "created_at" => now()],
            ["municipio_id" => 229, "nombre" => 'Mapararí', "activo" => true, "created_at" => now()],

            ["municipio_id" => 230, "nombre" => 'Santa Cruz de Bucaral', "activo" => true, "created_at" => now()],
            ["municipio_id" => 230, "nombre" => 'El Charal', "activo" => true, "created_at" => now()],
            ["municipio_id" => 230, "nombre" => 'Las Vegas del Tuy', "activo" => true, "created_at" => now()],

            ["municipio_id" => 231, "nombre" => 'Cabure', "activo" => true, "created_at" => now()],
            ["municipio_id" => 231, "nombre" => 'Colina', "activo" => true, "created_at" => now()],
            ["municipio_id" => 231, "nombre" => 'Curimagua', "activo" => true, "created_at" => now()],

            ["municipio_id" => 232, "nombre" => 'Pedregal', "activo" => true, "created_at" => now()],
            ["municipio_id" => 232, "nombre" => 'Agua Clara', "activo" => true, "created_at" => now()],
            ["municipio_id" => 232, "nombre" => 'Avaria', "activo" => true, "created_at" => now()],
            ["municipio_id" => 232, "nombre" => 'Piedra Grande', "activo" => true, "created_at" => now()],
            ["municipio_id" => 232, "nombre" => 'Purureche', "activo" => true, "created_at" => now()],

            ["municipio_id" => 233, "nombre" => 'San Luis', "activo" => true, "created_at" => now()],
            ["municipio_id" => 233, "nombre" => 'Aracua', "activo" => true, "created_at" => now()],
            ["municipio_id" => 233, "nombre" => 'La Peña', "activo" => true, "created_at" => now()],

            ["municipio_id" => 234, "nombre" => 'Sucre', "activo" => true, "created_at" => now()],
            ["municipio_id" => 234, "nombre" => 'Pecaya', "activo" => true, "created_at" => now()],

            ["municipio_id" => 235, "nombre" => 'Mene de Mauroa', "activo" => true, "created_at" => now()],
            ["municipio_id" => 235, "nombre" => 'Casigua', "activo" => true, "created_at" => now()],
            ["municipio_id" => 235, "nombre" => 'San Félix', "activo" => true, "created_at" => now()],

            ["municipio_id" => 236, "nombre" => 'Capatárida', "activo" => true, "created_at" => now()],
            ["municipio_id" => 236, "nombre" => 'Bariro', "activo" => true, "created_at" => now()],
            ["municipio_id" => 236, "nombre" => 'Borojó', "activo" => true, "created_at" => now()],
            ["municipio_id" => 236, "nombre" => 'Guajiro', "activo" => true, "created_at" => now()],
            ["municipio_id" => 236, "nombre" => 'Seque', "activo" => true, "created_at" => now()],
            ["municipio_id" => 236, "nombre" => 'Zazárida', "activo" => true, "created_at" => now()],

            ["municipio_id" => 237, "nombre" => 'Zazárida', "activo" => true, "created_at" => now()],

            ["municipio_id" => 238, "nombre" => 'Urumaco', "activo" => true, "created_at" => now()],
            ["municipio_id" => 238, "nombre" => 'Bruzual', "activo" => true, "created_at" => now()],

            ["municipio_id" => 239, "nombre" => 'Bolívar', "activo" => true, "created_at" => now()],
            ["municipio_id" => 239, "nombre" => 'Cacique Mara', "activo" => true, "created_at" => now()],
            ["municipio_id" => 239, "nombre" => 'Cecilio Acosta', "activo" => true, "created_at" => now()],
            ["municipio_id" => 239, "nombre" => 'Cristo de Aranza', "activo" => true, "created_at" => now()],
            ["municipio_id" => 239, "nombre" => 'Manuel Dagnino', "activo" => true, "created_at" => now()],
            ["municipio_id" => 239, "nombre" => 'San Isidro', "activo" => true, "created_at" => now()],
            ["municipio_id" => 239, "nombre" => 'Santa Lucía', "activo" => true, "created_at" => now()],
            ["municipio_id" => 239, "nombre" => 'Francisco Eugenio Bustamante', "activo" => true, "created_at" => now()],
            ["municipio_id" => 239, "nombre" => 'Coquivacoa', "activo" => true, "created_at" => now()],
            ["municipio_id" => 239, "nombre" => 'Olegario Villalobos', "activo" => true, "created_at" => now()],
            ["municipio_id" => 239, "nombre" => 'Venancio Pulgar', "activo" => true, "created_at" => now()],
            ["municipio_id" => 239, "nombre" => 'Antonio Borjas Romero', "activo" => true, "created_at" => now()],
            ["municipio_id" => 239, "nombre" => 'Caracciolo Parra Pérez', "activo" => true, "created_at" => now()],
            ["municipio_id" => 239, "nombre" => 'Chiquinquirá', "activo" => true, "created_at" => now()],
            ["municipio_id" => 239, "nombre" => 'Idelfonso Vásquez', "activo" => true, "created_at" => now()],
            ["municipio_id" => 239, "nombre" => 'Juana de Ávila', "activo" => true, "created_at" => now()],
            ["municipio_id" => 239, "nombre" => 'Raúl Leoni', "activo" => true, "created_at" => now()],

            ["municipio_id" => 240, "nombre" => 'Luis Hurtado Higuera', "activo" => true, "created_at" => now()],
            ["municipio_id" => 240, "nombre" => 'San Francisco', "activo" => true, "created_at" => now()],
            ["municipio_id" => 240, "nombre" => 'El Bajo', "activo" => true, "created_at" => now()],
            ["municipio_id" => 240, "nombre" => 'Domitila Flores', "activo" => true, "created_at" => now()],
            ["municipio_id" => 240, "nombre" => 'Francisco Ochoa', "activo" => true, "created_at" => now()],
            ["municipio_id" => 240, "nombre" => 'Marcial Hernández', "activo" => true, "created_at" => now()],
            ["municipio_id" => 240, "nombre" => 'Los Cortijos', "activo" => true, "created_at" => now()],

            ["municipio_id" => 241, "nombre" => 'Ambrosio', "activo" => true, "created_at" => now()],
            ["municipio_id" => 241, "nombre" => 'Carmen Herrera', "activo" => true, "created_at" => now()],
            ["municipio_id" => 241, "nombre" => 'Germán Ríos Linares', "activo" => true, "created_at" => now()],
            ["municipio_id" => 241, "nombre" => 'La Rosa', "activo" => true, "created_at" => now()],
            ["municipio_id" => 241, "nombre" => 'Jorge Hernández', "activo" => true, "created_at" => now()],
            ["municipio_id" => 241, "nombre" => 'Rómulo Betancourt', "activo" => true, "created_at" => now()],
            ["municipio_id" => 241, "nombre" => 'San Benito', "activo" => true, "created_at" => now()],
            ["municipio_id" => 241, "nombre" => 'Arístides Calvani', "activo" => true, "created_at" => now()],
            ["municipio_id" => 241, "nombre" => 'Punta Gorda', "activo" => true, "created_at" => now()],

            ["municipio_id" => 242, "nombre" => 'Santa Rita', "activo" => true, "created_at" => now()],
            ["municipio_id" => 242, "nombre" => 'El Mene', "activo" => true, "created_at" => now()],
            ["municipio_id" => 242, "nombre" => 'José Cenovio Urribarri', "activo" => true, "created_at" => now()],
            ["municipio_id" => 242, "nombre" => 'Pedro Lucas Urribarri', "activo" => true, "created_at" => now()],

            ["municipio_id" => 243, "nombre" => 'Alonso de Ojeda', "activo" => true, "created_at" => now()],
            ["municipio_id" => 243, "nombre" => 'Libertad', "activo" => true, "created_at" => now()],
            ["municipio_id" => 243, "nombre" => 'Campo Lara', "activo" => true, "created_at" => now()],
            ["municipio_id" => 243, "nombre" => 'Eleazar López Contreras', "activo" => true, "created_at" => now()],
            ["municipio_id" => 243, "nombre" => 'Venezuela', "activo" => true, "created_at" => now()],

            ["municipio_id" => 244, "nombre" => 'Venezuela', "activo" => true, "created_at" => now()],
            ["municipio_id" => 244, "nombre" => 'Rafael María Baralt', "activo" => true, "created_at" => now()],
            ["municipio_id" => 244, "nombre" => 'Rafael Urdaneta', "activo" => true, "created_at" => now()],

            ["municipio_id" => 245, "nombre" => 'Tamare', "activo" => true, "created_at" => now()],
            ["municipio_id" => 245, "nombre" => 'La Sierrita', "activo" => true, "created_at" => now()],
            ["municipio_id" => 245, "nombre" => 'Las Parcelas', "activo" => true, "created_at" => now()],
            ["municipio_id" => 245, "nombre" => 'Luis de Vicente', "activo" => true, "created_at" => now()],
            ["municipio_id" => 245, "nombre" => 'Monseñor Marcos Sergio Godoy', "activo" => true, "created_at" => now()],
            ["municipio_id" => 245, "nombre" => 'Ricaurte', "activo" => true, "created_at" => now()],

            ["municipio_id" => 246, "nombre" => 'Alta Guajira', "activo" => true, "created_at" => now()],
            ["municipio_id" => 246, "nombre" => 'Elías Sánchez Rubio', "activo" => true, "created_at" => now()],
            ["municipio_id" => 246, "nombre" => 'Guajira', "activo" => true, "created_at" => now()],

            ["municipio_id" => 247, "nombre" => 'Isla de Toas', "activo" => true, "created_at" => now()],
            ["municipio_id" => 247, "nombre" => 'Monagas', "activo" => true, "created_at" => now()],

            ["municipio_id" => 248, "nombre" => 'San Carlos del Zulia', "activo" => true, "created_at" => now()],
            ["municipio_id" => 248, "nombre" => 'Moralito', "activo" => true, "created_at" => now()],
            ["municipio_id" => 248, "nombre" => 'Santa Bárbara', "activo" => true, "created_at" => now()],
            ["municipio_id" => 248, "nombre" => 'Santa Cruz del Zulia', "activo" => true, "created_at" => now()],
            ["municipio_id" => 248, "nombre" => 'Urribarri', "activo" => true, "created_at" => now()],

            ["municipio_id" => 249, "nombre" => 'Encontrados', "activo" => true, "created_at" => now()],
            ["municipio_id" => 249, "nombre" => 'Udón Pérez', "activo" => true, "created_at" => now()],

            ["municipio_id" => 250, "nombre" => 'Udón Pérez', "activo" => true, "created_at" => now()],
            ["municipio_id" => 250, "nombre" => 'Barí', "activo" => true, "created_at" => now()],

            ["municipio_id" => 251, "nombre" => 'Gibraltar', "activo" => true, "created_at" => now()],
            ["municipio_id" => 251, "nombre" => 'Heras', "activo" => true, "created_at" => now()],
            ["municipio_id" => 251, "nombre" => 'Monseñor Arturo Celestino Álvarez', "activo" => true, "created_at" => now()],
            ["municipio_id" => 251, "nombre" => 'Rómulo Gallegos', "activo" => true, "created_at" => now()],

            ["municipio_id" => 252, "nombre" => 'Simón Rodríguez', "activo" => true, "created_at" => now()],
            ["municipio_id" => 252, "nombre" => 'Carlos Quevedo', "activo" => true, "created_at" => now()],
            ["municipio_id" => 252, "nombre" => 'Francisco Javier Pulgar', "activo" => true, "created_at" => now()],

            ["municipio_id" => 253, "nombre" => 'Libertad', "activo" => true, "created_at" => now()],
            ["municipio_id" => 253, "nombre" => 'Bartolomé de las Casas', "activo" => true, "created_at" => now()],
            ["municipio_id" => 253, "nombre" => 'Río Negro', "activo" => true, "created_at" => now()],
            ["municipio_id" => 253, "nombre" => 'San José de Perijá', "activo" => true, "created_at" => now()],

            ["municipio_id" => 254, "nombre" => 'La Concepción', "activo" => true, "created_at" => now()],
            ["municipio_id" => 254, "nombre" => 'José Ramón Yépez', "activo" => true, "created_at" => now()],
            ["municipio_id" => 254, "nombre" => 'Mariano Parra León', "activo" => true, "created_at" => now()],
            ["municipio_id" => 254, "nombre" => 'San José', "activo" => true, "created_at" => now()],

            ["municipio_id" => 255, "nombre" => 'La Concepción', "activo" => true, "created_at" => now()],
            ["municipio_id" => 255, "nombre" => 'Andrés Bello', "activo" => true, "created_at" => now()],
            ["municipio_id" => 255, "nombre" => 'Chiquinquirá', "activo" => true, "created_at" => now()],
            ["municipio_id" => 255, "nombre" => 'El Carmelo', "activo" => true, "created_at" => now()],
            ["municipio_id" => 255, "nombre" => 'Potreritos', "activo" => true, "created_at" => now()],

            ["municipio_id" => 256, "nombre" => 'Altagracia', "activo" => true, "created_at" => now()],
            ["municipio_id" => 256, "nombre" => 'Ana María Campos', "activo" => true, "created_at" => now()],
            ["municipio_id" => 256, "nombre" => 'Faría', "activo" => true, "created_at" => now()],
            ["municipio_id" => 256, "nombre" => 'San Antonio', "activo" => true, "created_at" => now()],
            ["municipio_id" => 256, "nombre" => 'San José', "activo" => true, "created_at" => now()],

            ["municipio_id" => 257, "nombre" => 'San Timoteo', "activo" => true, "created_at" => now()],
            ["municipio_id" => 257, "nombre" => 'General Urdaneta', "activo" => true, "created_at" => now()],
            ["municipio_id" => 257, "nombre" => 'Libertador', "activo" => true, "created_at" => now()],
            ["municipio_id" => 257, "nombre" => 'Manuel Guanipa Matos', "activo" => true, "created_at" => now()],
            ["municipio_id" => 257, "nombre" => 'Marcelino Briceño', "activo" => true, "created_at" => now()],
            ["municipio_id" => 257, "nombre" => 'Pueblo Nuevo', "activo" => true, "created_at" => now()],

            ["municipio_id" => 258, "nombre" => 'La Victoria', "activo" => true, "created_at" => now()],
            ["municipio_id" => 258, "nombre" => 'Rafael Urdaneta', "activo" => true, "created_at" => now()],
            ["municipio_id" => 258, "nombre" => 'Raúl Cuenca', "activo" => true, "created_at" => now()],
            ["municipio_id" => 259, "nombre" => 'Raúl Cuenca', "activo" => true, "created_at" => now()],
            ["municipio_id" => 259, "nombre" => 'El Rosario', "activo" => true, "created_at" => now()],
            ["municipio_id" => 259, "nombre" => 'Donaldo García', "activo" => true, "created_at" => now()],
            ["municipio_id" => 259, "nombre" => 'Sixto Zambrano', "activo" => true, "created_at" => now()],

            ["municipio_id" => 260, "nombre" => 'El Carmen', "activo" => true, "created_at" => now()],
            ["municipio_id" => 260, "nombre" => 'San Cristóbal', "activo" => true, "created_at" => now()],
            ["municipio_id" => 260, "nombre" => 'Bergantín', "activo" => true, "created_at" => now()],
            ["municipio_id" => 260, "nombre" => 'Caigua', "activo" => true, "created_at" => now()],
            ["municipio_id" => 260, "nombre" => 'El Pilar', "activo" => true, "created_at" => now()],
            ["municipio_id" => 260, "nombre" => 'Naricual', "activo" => true, "created_at" => now()],

            ["municipio_id" => 261, "nombre" => 'Capital Aragua', "activo" => true, "created_at" => now()],
            ["municipio_id" => 261, "nombre" => 'Cachipo', "activo" => true, "created_at" => now()],

            ["municipio_id" => 262, "nombre" => 'Capital Anaco', "activo" => true, "created_at" => now()],
            ["municipio_id" => 262, "nombre" => 'San Joaquín', "activo" => true, "created_at" => now()],

            ["municipio_id" => 263, "nombre" => 'Capital de Boca de Chávez', "activo" => true, "created_at" => now()],

            ["municipio_id" => 264, "nombre" => 'Capital Pedro María Freites', "activo" => true, "created_at" => now()],
            ["municipio_id" => 264, "nombre" => 'Santa Rosa', "activo" => true, "created_at" => now()],
            ["municipio_id" => 264, "nombre" => 'Urica', "activo" => true, "created_at" => now()],

            ["municipio_id" => 265, "nombre" => 'Capital Manuel Ezequiel Bruzual', "activo" => true, "created_at" => now()],
            ["municipio_id" => 265, "nombre" => 'Sabana de Uchire', "activo" => true, "created_at" => now()],

            ["municipio_id" => 266, "nombre" => 'Capital Francisco del Carmen Carvajal', "activo" => true, "created_at" => now()],
            ["municipio_id" => 266, "nombre" => 'Santa Bárbara', "activo" => true, "created_at" => now()],

            ["municipio_id" => 267, "nombre" => 'Capital Independencia', "activo" => true, "created_at" => now()],
            ["municipio_id" => 267, "nombre" => 'Mamo', "activo" => true, "created_at" => now()],

            ["municipio_id" => 268, "nombre" => 'Capital Guanta', "activo" => true, "created_at" => now()],
            ["municipio_id" => 268, "nombre" => 'Chorrerón', "activo" => true, "created_at" => now()],

            ["municipio_id" => 269, "nombre" => 'Capital Diego Bautista Urbaneja', "activo" => true, "created_at" => now()],
            ["municipio_id" => 269, "nombre" => 'El Morro', "activo" => true, "created_at" => now()],

            ["municipio_id" => 270, "nombre" => 'Capital Juan Manuel Cajigal', "activo" => true, "created_at" => now()],
            ["municipio_id" => 270, "nombre" => 'San Pablo', "activo" => true, "created_at" => now()],

            ["municipio_id" => 271, "nombre" => 'Capital Fernando de Peñalver', "activo" => true, "created_at" => now()],
            ["municipio_id" => 271, "nombre" => 'San Miguel', "activo" => true, "created_at" => now()],
            ["municipio_id" => 271, "nombre" => 'Sucre', "activo" => true, "created_at" => now()],

            ["municipio_id" => 272, "nombre" => 'Capital Píritu', "activo" => true, "created_at" => now()],
            ["municipio_id" => 272, "nombre" => 'San Francisco', "activo" => true, "created_at" => now()],

            ["municipio_id" => 273, "nombre" => 'Capital Puerto La Cruz', "activo" => true, "created_at" => now()],
            ["municipio_id" => 273, "nombre" => 'Pozuelos', "activo" => true, "created_at" => now()],
            ["municipio_id" => 273, "nombre" => 'Guanape', "activo" => true, "created_at" => now()],

            ["municipio_id" => 274, "nombre" => 'Capital Santa Ana', "activo" => true, "created_at" => now()],
            ["municipio_id" => 274, "nombre" => 'Pueblo Nuevo', "activo" => true, "created_at" => now()],

            ["municipio_id" => 275, "nombre" => 'Capital Libertad', "activo" => true, "created_at" => now()],
            ["municipio_id" => 275, "nombre" => 'El Carito', "activo" => true, "created_at" => now()],
            ["municipio_id" => 275, "nombre" => 'Santa Inés', "activo" => true, "created_at" => now()],

            ["municipio_id" => 276, "nombre" => 'Edmundo Barrios', "activo" => true, "created_at" => now()],
            ["municipio_id" => 276, "nombre" => 'Miguel Otero Silva', "activo" => true, "created_at" => now()],

            ["municipio_id" => 277, "nombre" => 'Capital Sir Arthur Mc Gregor', "activo" => true, "created_at" => now()],
            ["municipio_id" => 277, "nombre" => 'Tomás Alfaro Calatrava', "activo" => true, "created_at" => now()],

            ["municipio_id" => 278, "nombre" => 'Capital Francisco de Miranda', "activo" => true, "created_at" => now()],
            ["municipio_id" => 278, "nombre" => 'Atapirire', "activo" => true, "created_at" => now()],
            ["municipio_id" => 278, "nombre" => 'Boca del Pao', "activo" => true, "created_at" => now()],
            ["municipio_id" => 278, "nombre" => 'El Pao', "activo" => true, "created_at" => now()],

            ["municipio_id" => 279, "nombre" => 'Múcura', "activo" => true, "created_at" => now()],

            ["municipio_id" => 280, "nombre" => 'Capital José Gregorio Monagas', "activo" => true, "created_at" => now()],
            ["municipio_id" => 280, "nombre" => 'Piar', "activo" => true, "created_at" => now()],
            ["municipio_id" => 280, "nombre" => 'San Diego de Cabrutica', "activo" => true, "created_at" => now()],
            ["municipio_id" => 280, "nombre" => 'Santa Clara', "activo" => true, "created_at" => now()],
            ["municipio_id" => 280, "nombre" => 'Uverito', "activo" => true, "created_at" => now()],
            ["municipio_id" => 280, "nombre" => 'Zuata', "activo" => true, "created_at" => now()],

            ["municipio_id" => 281, "nombre" => 'Agua Salada', "activo" => true, "created_at" => now()],
            ["municipio_id" => 281, "nombre" => 'Catedral', "activo" => true, "created_at" => now()],
            ["municipio_id" => 281, "nombre" => 'José Antonio Páez', "activo" => true, "created_at" => now()],
            ["municipio_id" => 281, "nombre" => 'La Sabanita', "activo" => true, "created_at" => now()],
            ["municipio_id" => 281, "nombre" => 'Marhuanta', "activo" => true, "created_at" => now()],
            ["municipio_id" => 281, "nombre" => 'Vista Hermosa', "activo" => true, "created_at" => now()],
            ["municipio_id" => 281, "nombre" => 'Orinoco', "activo" => true, "created_at" => now()],
            ["municipio_id" => 281, "nombre" => 'Panapana', "activo" => true, "created_at" => now()],
            ["municipio_id" => 281, "nombre" => 'Zea', "activo" => true, "created_at" => now()],

            ["municipio_id" => 282, "nombre" => 'Sección Capital Sucre', "activo" => true, "created_at" => now()],
            ["municipio_id" => 282, "nombre" => 'Aripao', "activo" => true, "created_at" => now()],
            ["municipio_id" => 282, "nombre" => 'Guarataro', "activo" => true, "created_at" => now()],
            ["municipio_id" => 282, "nombre" => 'Las Majadas', "activo" => true, "created_at" => now()],
            ["municipio_id" => 282, "nombre" => 'Moitaco', "activo" => true, "created_at" => now()],

            ["municipio_id" => 283, "nombre" => 'Sección Capital Angostura', "activo" => true, "created_at" => now()],
            ["municipio_id" => 283, "nombre" => 'Barceloneta', "activo" => true, "created_at" => now()],
            ["municipio_id" => 283, "nombre" => 'San Francisco', "activo" => true, "created_at" => now()],
            ["municipio_id" => 283, "nombre" => 'Santa Bárbara', "activo" => true, "created_at" => now()],

            ["municipio_id" => 284, "nombre" => 'Santa Bárbara', "activo" => true, "created_at" => now()],
            ["municipio_id" => 284, "nombre" => 'Sección Capital Cedeño', "activo" => true, "created_at" => now()],
            ["municipio_id" => 284, "nombre" => 'Altagracia', "activo" => true, "created_at" => now()],
            ["municipio_id" => 284, "nombre" => 'Ascensión Farreras', "activo" => true, "created_at" => now()],
            ["municipio_id" => 284, "nombre" => 'Guaniamo', "activo" => true, "created_at" => now()],
            ["municipio_id" => 284, "nombre" => 'La Urbana', "activo" => true, "created_at" => now()],
            ["municipio_id" => 284, "nombre" => 'Pijiguaos', "activo" => true, "created_at" => now()],

            ["municipio_id" => 285, "nombre" => 'Sección Capital Gran Sabana', "activo" => true, "created_at" => now()],
            ["municipio_id" => 285, "nombre" => 'Ikabarú', "activo" => true, "created_at" => now()],

            ["municipio_id" => 286, "nombre" => 'Ikabarú', "activo" => true, "created_at" => now()],

            ["municipio_id" => 287, "nombre" => 'Cachamay', "activo" => true, "created_at" => now()],
            ["municipio_id" => 287, "nombre" => 'Unare', "activo" => true, "created_at" => now()],
            ["municipio_id" => 287, "nombre" => 'Universidad', "activo" => true, "created_at" => now()],
            ["municipio_id" => 287, "nombre" => 'Simón Bolívar', "activo" => true, "created_at" => now()],
            ["municipio_id" => 287, "nombre" => 'Vista al Sol', "activo" => true, "created_at" => now()],
            ["municipio_id" => 287, "nombre" => 'Pozo Verde', "activo" => true, "created_at" => now()],
            ["municipio_id" => 287, "nombre" => 'Yocoima', "activo" => true, "created_at" => now()],
            ["municipio_id" => 287, "nombre" => 'Chirica', "activo" => true, "created_at" => now()],
            ["municipio_id" => 287, "nombre" => 'Dalla Costa', "activo" => true, "created_at" => now()],
            ["municipio_id" => 287, "nombre" => 'Once de Abril', "activo" => true, "created_at" => now()],

            ["municipio_id" => 288, "nombre" => 'Sección Capital Piar', "activo" => true, "created_at" => now()],
            ["municipio_id" => 288, "nombre" => 'Pedro Cova', "activo" => true, "created_at" => now()],

            ["municipio_id" => 289, "nombre" => 'Sección Capital Roscio', "activo" => true, "created_at" => now()],
            ["municipio_id" => 289, "nombre" => 'Salom', "activo" => true, "created_at" => now()],

            ["municipio_id" => 290, "nombre" => 'Salom', "activo" => true, "created_at" => now()],

            ["municipio_id" => 291, "nombre" => 'Sección Capital Sifontes', "activo" => true, "created_at" => now()],
            ["municipio_id" => 291, "nombre" => 'Dalla Costa', "activo" => true, "created_at" => now()],
            ["municipio_id" => 291, "nombre" => 'San Isidro', "activo" => true, "created_at" => now()],

            ["municipio_id" => 292, "nombre" => 'Curiapo', "activo" => true, "created_at" => now()],
            ["municipio_id" => 292, "nombre" => 'Almirante Luis Brión', "activo" => true, "created_at" => now()],
            ["municipio_id" => 292, "nombre" => 'Francisco Aniceto Lugo', "activo" => true, "created_at" => now()],
            ["municipio_id" => 292, "nombre" => 'Manuel Renaud', "activo" => true, "created_at" => now()],
            ["municipio_id" => 292, "nombre" => 'Padre Barral', "activo" => true, "created_at" => now()],
            ["municipio_id" => 292, "nombre" => 'Santos de Abelgas', "activo" => true, "created_at" => now()],

            ["municipio_id" => 293, "nombre" => 'Imataca', "activo" => true, "created_at" => now()],
            ["municipio_id" => 293, "nombre" => 'Cinco de Julio', "activo" => true, "created_at" => now()],
            ["municipio_id" => 293, "nombre" => 'Juan Bautista Arismendi', "activo" => true, "created_at" => now()],
            ["municipio_id" => 293, "nombre" => 'Manuel Piar', "activo" => true, "created_at" => now()],
            ["municipio_id" => 293, "nombre" => 'Rómulo Gallegos', "activo" => true, "created_at" => now()],

            ["municipio_id" => 294, "nombre" => 'Pedernales', "activo" => true, "created_at" => now()],
            ["municipio_id" => 294, "nombre" => 'Luis Beltrán Prieto Figueroa', "activo" => true, "created_at" => now()],

            ["municipio_id" => 295, "nombre" => 'San José', "activo" => true, "created_at" => now()],
            ["municipio_id" => 295, "nombre" => 'José Vidal Marcano', "activo" => true, "created_at" => now()],
            ["municipio_id" => 295, "nombre" => 'Juan Millán', "activo" => true, "created_at" => now()],
            ["municipio_id" => 295, "nombre" => 'Leonardo Ruíz Pineda', "activo" => true, "created_at" => now()],
            ["municipio_id" => 295, "nombre" => 'Mariscal Antonio José de Sucre', "activo" => true, "created_at" => now()],
            ["municipio_id" => 295, "nombre" => 'Monseñor Argimiro García', "activo" => true, "created_at" => now()],
            ["municipio_id" => 295, "nombre" => 'San Rafael', "activo" => true, "created_at" => now()],
            ["municipio_id" => 295, "nombre" => 'Virgen del Valle', "activo" => true, "created_at" => now()],

            ["municipio_id" => 296, "nombre" => 'Capital Acosta', "activo" => true, "created_at" => now()],
            ["municipio_id" => 296, "nombre" => 'San Francisco', "activo" => true, "created_at" => now()],

            ["municipio_id" => 297, "nombre" => 'San Francisco', "activo" => true, "created_at" => now()],

            ["municipio_id" => 298, "nombre" => 'San Francisco', "activo" => true, "created_at" => now()],

            ["municipio_id" => 299, "nombre" => 'Capital Caripe', "activo" => true, "created_at" => now()],
            ["municipio_id" => 299, "nombre" => 'El Guácharo', "activo" => true, "created_at" => now()],
            ["municipio_id" => 299, "nombre" => 'La Guanota', "activo" => true, "created_at" => now()],
            ["municipio_id" => 299, "nombre" => 'Sabana de Piedra', "activo" => true, "created_at" => now()],
            ["municipio_id" => 299, "nombre" => 'San Agustín', "activo" => true, "created_at" => now()],
            ["municipio_id" => 299, "nombre" => 'Teresén', "activo" => true, "created_at" => now()],

            ["municipio_id" => 300, "nombre" => 'Capital Cedeño', "activo" => true, "created_at" => now()],
            ["municipio_id" => 300, "nombre" => 'Areo', "activo" => true, "created_at" => now()],
            ["municipio_id" => 300, "nombre" => 'San Félix', "activo" => true, "created_at" => now()],
            ["municipio_id" => 300, "nombre" => 'Viento Fresco', "activo" => true, "created_at" => now()],

            ["municipio_id" => 301, "nombre" => 'Capital Ezequiel Zamora', "activo" => true, "created_at" => now()],
            ["municipio_id" => 301, "nombre" => 'El Tejero', "activo" => true, "created_at" => now()],

            ["municipio_id" => 302, "nombre" => 'Capital Libertador', "activo" => true, "created_at" => now()],
            ["municipio_id" => 302, "nombre" => 'Chaguaramas', "activo" => true, "created_at" => now()],
            ["municipio_id" => 302, "nombre" => 'Las Alhuacas', "activo" => true, "created_at" => now()],
            ["municipio_id" => 302, "nombre" => 'Tabasca', "activo" => true, "created_at" => now()],

            ["municipio_id" => 303, "nombre" => 'Capital Maturín', "activo" => true, "created_at" => now()],
            ["municipio_id" => 303, "nombre" => 'Alto de los Godos', "activo" => true, "created_at" => now()],
            ["municipio_id" => 303, "nombre" => 'Boquerón', "activo" => true, "created_at" => now()],
            ["municipio_id" => 303, "nombre" => 'Las Cocuizas', "activo" => true, "created_at" => now()],
            ["municipio_id" => 303, "nombre" => 'San Simón', "activo" => true, "created_at" => now()],
            ["municipio_id" => 303, "nombre" => 'Santa Cruz', "activo" => true, "created_at" => now()],
            ["municipio_id" => 303, "nombre" => 'El Corozo', "activo" => true, "created_at" => now()],
            ["municipio_id" => 303, "nombre" => 'El Furrial', "activo" => true, "created_at" => now()],
            ["municipio_id" => 303, "nombre" => 'Jusepín', "activo" => true, "created_at" => now()],
            ["municipio_id" => 303, "nombre" => 'La Pica', "activo" => true, "created_at" => now()],
            ["municipio_id" => 303, "nombre" => 'San Vicente', "activo" => true, "created_at" => now()],

            ["municipio_id" => 304, "nombre" => 'Capital Piar', "activo" => true, "created_at" => now()],
            ["municipio_id" => 304, "nombre" => 'Aparicio', "activo" => true, "created_at" => now()],
            ["municipio_id" => 304, "nombre" => 'Chaguaramal', "activo" => true, "created_at" => now()],
            ["municipio_id" => 304, "nombre" => 'El Pinto', "activo" => true, "created_at" => now()],
            ["municipio_id" => 304, "nombre" => 'Guanaguana', "activo" => true, "created_at" => now()],
            ["municipio_id" => 304, "nombre" => 'La Toscana', "activo" => true, "created_at" => now()],
            ["municipio_id" => 304, "nombre" => 'Taguaya', "activo" => true, "created_at" => now()],

            ["municipio_id" => 305, "nombre" => 'Capital Púnceres', "activo" => true, "created_at" => now()],
            ["municipio_id" => 305, "nombre" => 'Cachipo', "activo" => true, "created_at" => now()],

            ["municipio_id" => 306, "nombre" => 'Cachipo', "activo" => true, "created_at" => now()],

            ["municipio_id" => 307, "nombre" => 'Capital Sotillo', "activo" => true, "created_at" => now()],
            ["municipio_id" => 307, "nombre" => 'Los Barrancos de Fajardo', "activo" => true, "created_at" => now()],

            ["municipio_id" => 308, "nombre" => 'Los Barrancos de Fajardo', "activo" => true, "created_at" => now()],

            ["municipio_id" => 309, "nombre" => 'Los Barrancos de Fajardo', "activo" => true, "created_at" => now()],

            ["municipio_id" => 310, "nombre" => 'Los Barrancos de Fajardo', "activo" => true, "created_at" => now()],

            ["municipio_id" => 311, "nombre" => 'Capital Díaz', "activo" => true, "created_at" => now()],
            ["municipio_id" => 311, "nombre" => 'Zabala', "activo" => true, "created_at" => now()],

            ["municipio_id" => 312, "nombre" => 'Capital García', "activo" => true, "created_at" => now()],
            ["municipio_id" => 312, "nombre" => 'Francisco Fajardo', "activo" => true, "created_at" => now()],

            ["municipio_id" => 313, "nombre" => 'Mariño', "activo" => true, "created_at" => now()],

            ["municipio_id" => 314, "nombre" => 'Capital Goméz', "activo" => true, "created_at" => now()],
            ["municipio_id" => 314, "nombre" => 'Bolívar', "activo" => true, "created_at" => now()],
            ["municipio_id" => 314, "nombre" => 'Guevara', "activo" => true, "created_at" => now()],
            ["municipio_id" => 314, "nombre" => 'Matasiete', "activo" => true, "created_at" => now()],
            ["municipio_id" => 314, "nombre" => 'Sucre', "activo" => true, "created_at" => now()],

            ["municipio_id" => 315, "nombre" => 'Capital Maneiro', "activo" => true, "created_at" => now()],
            ["municipio_id" => 315, "nombre" => 'Aguirre', "activo" => true, "created_at" => now()],

            ["municipio_id" => 316, "nombre" => 'Capital Marcano', "activo" => true, "created_at" => now()],
            ["municipio_id" => 316, "nombre" => 'Adrián', "activo" => true, "created_at" => now()],

            ["municipio_id" => 317, "nombre" => 'Capital Península de Macanao', "activo" => true, "created_at" => now()],
            ["municipio_id" => 317, "nombre" => 'San Francisco', "activo" => true, "created_at" => now()],

            ["municipio_id" => 318, "nombre" => 'Capital Tubores', "activo" => true, "created_at" => now()],
            ["municipio_id" => 318, "nombre" => 'Los Barales', "activo" => true, "created_at" => now()],

            ["municipio_id" => 319, "nombre" => 'Capital Villalba', "activo" => true, "created_at" => now()],
            ["municipio_id" => 319, "nombre" => 'Vicente Fuentes', "activo" => true, "created_at" => now()],

            ["municipio_id" => 320, "nombre" => 'Altagracia', "activo" => true, "created_at" => now()],
            ["municipio_id" => 320, "nombre" => 'Ayacucho', "activo" => true, "created_at" => now()],
            ["municipio_id" => 320, "nombre" => 'Santa Inés', "activo" => true, "created_at" => now()],
            ["municipio_id" => 320, "nombre" => 'Valentín Valiente', "activo" => true, "created_at" => now()],
            ["municipio_id" => 320, "nombre" => 'San Juan', "activo" => true, "created_at" => now()],
            ["municipio_id" => 320, "nombre" => 'Raúl Leoni', "activo" => true, "created_at" => now()],
            ["municipio_id" => 320, "nombre" => 'Gran Mariscal', "activo" => true, "created_at" => now()],

            ["municipio_id" => 321, "nombre" => 'Araya', "activo" => true, "created_at" => now()],
            ["municipio_id" => 321, "nombre" => 'Chacopata', "activo" => true, "created_at" => now()],
            ["municipio_id" => 321, "nombre" => 'Manicuare', "activo" => true, "created_at" => now()],

            ["municipio_id" => 322, "nombre" => 'Cumanacoa', "activo" => true, "created_at" => now()],
            ["municipio_id" => 322, "nombre" => 'Arenas', "activo" => true, "created_at" => now()],
            ["municipio_id" => 322, "nombre" => 'Aricagua', "activo" => true, "created_at" => now()],
            ["municipio_id" => 322, "nombre" => 'Cocollar', "activo" => true, "created_at" => now()],
            ["municipio_id" => 322, "nombre" => 'San Fernando', "activo" => true, "created_at" => now()],
            ["municipio_id" => 322, "nombre" => 'San Lorenzo', "activo" => true, "created_at" => now()],

            ["municipio_id" => 323, "nombre" => 'San Lorenzo', "activo" => true, "created_at" => now()],

            ["municipio_id" => 324, "nombre" => 'Mariguitar', "activo" => true, "created_at" => now()],

            ["municipio_id" => 325, "nombre" => 'Villa Frontado (Muelle de Cariaco)', "activo" => true, "created_at" => now()],
            ["municipio_id" => 325, "nombre" => 'Catuaro', "activo" => true, "created_at" => now()],
            ["municipio_id" => 325, "nombre" => 'Rendón', "activo" => true, "created_at" => now()],
            ["municipio_id" => 325, "nombre" => 'Santa Cruz', "activo" => true, "created_at" => now()],
            ["municipio_id" => 325, "nombre" => 'Santa María', "activo" => true, "created_at" => now()],

            ["municipio_id" => 326, "nombre" => 'Bolívar', "activo" => true, "created_at" => now()],
            ["municipio_id" => 326, "nombre" => 'Macarapana', "activo" => true, "created_at" => now()],
            ["municipio_id" => 326, "nombre" => 'Santa Catalina', "activo" => true, "created_at" => now()],
            ["municipio_id" => 326, "nombre" => 'Santa Rosa', "activo" => true, "created_at" => now()],
            ["municipio_id" => 326, "nombre" => 'Santa Teresa', "activo" => true, "created_at" => now()],

            ["municipio_id" => 327, "nombre" => 'El Pilar', "activo" => true, "created_at" => now()],
            ["municipio_id" => 327, "nombre" => 'El Rincón', "activo" => true, "created_at" => now()],
            ["municipio_id" => 327, "nombre" => 'General Francisco Antonio Vásquez', "activo" => true, "created_at" => now()],
            ["municipio_id" => 327, "nombre" => 'Guaraúnos', "activo" => true, "created_at" => now()],
            ["municipio_id" => 327, "nombre" => 'Tunapuicito', "activo" => true, "created_at" => now()],
            ["municipio_id" => 327, "nombre" => 'Unión', "activo" => true, "created_at" => now()],

            ["municipio_id" => 328, "nombre" => 'Tunapuy', "activo" => true, "created_at" => now()],
            ["municipio_id" => 328, "nombre" => 'Campo Elías', "activo" => true, "created_at" => now()],

            ["municipio_id" => 329, "nombre" => 'Yaguaraparo', "activo" => true, "created_at" => now()],
            ["municipio_id" => 329, "nombre" => 'El Paujil', "activo" => true, "created_at" => now()],
            ["municipio_id" => 329, "nombre" => 'Libertad', "activo" => true, "created_at" => now()],

            ["municipio_id" => 330, "nombre" => 'Irapa', "activo" => true, "created_at" => now()],
            ["municipio_id" => 330, "nombre" => 'Campo Claro', "activo" => true, "created_at" => now()],
            ["municipio_id" => 330, "nombre" => 'Marabal', "activo" => true, "created_at" => now()],
            ["municipio_id" => 330, "nombre" => 'San Antonio de Irapa', "activo" => true, "created_at" => now()],
            ["municipio_id" => 330, "nombre" => 'Soro', "activo" => true, "created_at" => now()],

            ["municipio_id" => 331, "nombre" => 'Güiria', "activo" => true, "created_at" => now()],
            ["municipio_id" => 331, "nombre" => 'Bideau', "activo" => true, "created_at" => now()],
            ["municipio_id" => 331, "nombre" => 'Cristóbal Colón', "activo" => true, "created_at" => now()],
            ["municipio_id" => 331, "nombre" => 'Punta de Piedras', "activo" => true, "created_at" => now()],

            ["municipio_id" => 332, "nombre" => 'Río Caribe', "activo" => true, "created_at" => now()],
            ["municipio_id" => 332, "nombre" => 'Antonio José de Sucre', "activo" => true, "created_at" => now()],
            ["municipio_id" => 332, "nombre" => 'El Morro de Puerto Santo', "activo" => true, "created_at" => now()],
            ["municipio_id" => 332, "nombre" => 'Puerto Santo', "activo" => true, "created_at" => now()],
            ["municipio_id" => 332, "nombre" => 'San Juan de Las Galdonas', "activo" => true, "created_at" => now()],

            ["municipio_id" => 333, "nombre" => 'San José de Aerocuar', "activo" => true, "created_at" => now()],
            ["municipio_id" => 333, "nombre" => 'Tavera Acosta', "activo" => true, "created_at" => now()],

            ["municipio_id" => 334, "nombre" => 'Mariño', "activo" => true, "created_at" => now()],
            ["municipio_id" => 334, "nombre" => 'Rómulo Gallegos', "activo" => true, "created_at" => now()],

            ["municipio_id" => 335, "nombre" => 'La Guaira', "activo" => true, "created_at" => now()],
            ["municipio_id" => 335, "nombre" => 'Maiquetía', "activo" => true, "created_at" => now()],
            ["municipio_id" => 335, "nombre" => 'Carlos Soublette', "activo" => true, "created_at" => now()],
            ["municipio_id" => 335, "nombre" => 'Urimare', "activo" => true, "created_at" => now()],
            ["municipio_id" => 335, "nombre" => 'Catia La Mar', "activo" => true, "created_at" => now()],
            ["municipio_id" => 335, "nombre" => 'Macuto', "activo" => true, "created_at" => now()],
            ["municipio_id" => 335, "nombre" => 'Caraballeda', "activo" => true, "created_at" => now()],
            ["municipio_id" => 335, "nombre" => 'Naiguatá', "activo" => true, "created_at" => now()],
            ["municipio_id" => 335, "nombre" => 'Carayaca', "activo" => true, "created_at" => now()],
            ["municipio_id" => 335, "nombre" => 'Caruao', "activo" => true, "created_at" => now()],


        ]);
    }
}
