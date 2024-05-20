<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModuloSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permisosData = [
            [
                'codigo' => 'MO01',
                'modulo' => 'Seguridad',
                'alias' => 'MO',
                'imageurl' => 'imagen.png',
                'estado' => true,
                'created_at' => DB::raw('CURRENT_TIMESTAMP')
            ],
          

        ];
        foreach ($permisosData as $data) {
            DB::table('modulos')->insert($data);
        }
    }
}
