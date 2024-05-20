<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoEnumeradoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tipoEnumeradosData = [
            [
                'id' => 10,
                'descripcion' => 'Tipo de Documento',
                'estado' => true,
                'created_at' => DB::raw('CURRENT_TIMESTAMP')
            ],
            [
                'id' => 11,
                'descripcion' => 'Tipo de Género',
                'estado' => true,
                'created_at' => DB::raw('CURRENT_TIMESTAMP')
            ],
            [
                'id' => 12,
                'descripcion' => 'Estado Civil',
                'estado' => true,
                'created_at' => DB::raw('CURRENT_TIMESTAMP')
            ],
            
        ];
        foreach ($tipoEnumeradosData as $data) {
            DB::table('tipo_enumerados')->insert($data);
        }
    }
}
