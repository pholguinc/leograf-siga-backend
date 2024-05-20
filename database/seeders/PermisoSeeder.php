<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermisoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $permisosData = [
            [
                'nombre' => 'Acceder',
                'estado' => true,
                'modulo_id'=> 1,
                'created_at' => DB::raw('CURRENT_TIMESTAMP')
            ],
            [
                'nombre' => 'Consulyar',
                'estado' => true,
                'modulo_id' => 1,
                'created_at' => DB::raw('CURRENT_TIMESTAMP')
            ],
            [
                'nombre' => 'Agregar',
                'estado' => true,
                'modulo_id' => 1,
                'created_at' => DB::raw('CURRENT_TIMESTAMP')
            ],
            [
                'nombre' => 'Modificar',
                'estado' => true,
                'modulo_id' => 1,
                'created_at' => DB::raw('CURRENT_TIMESTAMP')
            ],
            [
                'nombre' => 'Eliminar',
                'estado' => true,
                'modulo_id' => 1,
                'created_at' => DB::raw('CURRENT_TIMESTAMP')
            ],
            [
                'nombre' => 'Aprobar',
                'estado' => true,
                'modulo_id' => 1,
                'created_at' => DB::raw('CURRENT_TIMESTAMP')
            ],
            [
                'nombre' => 'Asignar',
                'estado' => true,
                'modulo_id' => 1,
                'created_at' => DB::raw('CURRENT_TIMESTAMP')
            ],
            [
                'nombre' => 'Observar',
                'estado' => true,
                'modulo_id' => 1,
                'created_at' => DB::raw('CURRENT_TIMESTAMP')
            ],
            [
                'nombre' => 'Derivar',
                'estado' => true,
                'modulo_id' => 1,
                'created_at' => DB::raw('CURRENT_TIMESTAMP')
            ],
            [
                'nombre' => 'Exportar',
                'estado' => true,
                'modulo_id' => 1,
                'created_at' => DB::raw('CURRENT_TIMESTAMP')
            ],
            [
                'nombre' => 'Importar',
                'estado' => true,
                'modulo_id' => 1,
                'created_at' => DB::raw('CURRENT_TIMESTAMP')
            ],
            [
                'nombre' => 'Activar',
                'estado' => true,
                'modulo_id' => 1,
                'created_at' => DB::raw('CURRENT_TIMESTAMP')
            ],
            [
                'nombre' => 'Inactivar',
                'estado' => true,
                'modulo_id' => 1,
                'created_at' => DB::raw('CURRENT_TIMESTAMP')
            ],
            

        ];
        foreach ($permisosData as $data) {
            DB::table('permisos')->insert($data);
        }
    }
}
