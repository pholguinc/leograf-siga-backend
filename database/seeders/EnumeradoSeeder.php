<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EnumeradoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $EnumeradosData = [
            [
                'id' => 10001,
                'id_tipo_enumerado' => 10,
                'descripcion' => 'DNI',
                'estado' => true,
                'created_at' => DB::raw('CURRENT_TIMESTAMP')
            ],
            [
                'id' => 10002,
                'id_tipo_enumerado' => 10,
                'descripcion' => 'Pasaporte',
                'estado' => true,
                'created_at' => DB::raw('CURRENT_TIMESTAMP')
            ],
            [
                'id' => 10003,
                'id_tipo_enumerado' => 10,
                'descripcion' => 'Carnet de Extranjería',
                'estado' => true,
                'created_at' => DB::raw('CURRENT_TIMESTAMP')
            ],
            [
                'id' => 11001,
                'id_tipo_enumerado' => 11,
                'descripcion' => 'Masculino',
                'estado' => true,
                'created_at' => DB::raw('CURRENT_TIMESTAMP')
            ],
            [
                'id' => 11002,
                'id_tipo_enumerado' => 11,
                'descripcion' => 'Femenino',
                'estado' => true,
                'created_at' => DB::raw('CURRENT_TIMESTAMP')
            ],
            [
                'id' => 12001,
                'id_tipo_enumerado' => 12,
                'descripcion' => 'Soltero',
                'estado' => true,
                'created_at' => DB::raw('CURRENT_TIMESTAMP')
            ],
            [
                'id' => 12002,
                'id_tipo_enumerado' => 12,
                'descripcion' => 'Casado',
                'estado' => true,
                'created_at' => DB::raw('CURRENT_TIMESTAMP')
            ],
            
           

        ];
        foreach ($EnumeradosData as $data) {
            DB::table('enumerados')->insert($data);
        }
    }
}
