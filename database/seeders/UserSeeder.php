<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $usersData = [
            [
                'uuid' =>Str::uuid(),
                'id_tipo_documento' => 10001,
                'numero_documento' => '123456789',
                'nombres' => 'Pedro Alejandro',
                'apellidos' => 'Holguin Cueva',
                'email' => 'holguinpedro90@gmail.com',
                'password' => bcrypt('123456'),
                'captcha' => '12ER',
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
