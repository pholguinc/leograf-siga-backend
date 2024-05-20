<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $usersData = [
            [
                'id_tipo_documento' => 10001,
                'numero_documento' => '123456789',
                'nombres' => 'Pedro Alejandro',
                'apellidos' => 'Holguin Cueva',
                'email' => 'holguinpedro90@gmail.com',
                'rol_id' => null,
                'fecha_nacimiento' => DB::raw('CURRENT_TIMESTAMP'),
                'id_genero' => 11001,
                'id_codigo_pais' => '+51',
                'celular' => '123456789',
                'id_estado_civil' => 12001,
                'direccion' => 'address',
                'estado' => true,
                'created_at' => DB::raw('CURRENT_TIMESTAMP'),
            ],
        ];

        foreach ($usersData as $data) {
            DB::table('users')->insert($data);
        }
    }
}
