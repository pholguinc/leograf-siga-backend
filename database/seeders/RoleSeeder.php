<?php

namespace Database\Seeders;

use App\Models\Permiso;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;


class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $rolessData = [
            [
                'nombre' => 'Administrador',
                'codigo' => 'RO01',
                'estado' => true,
                'created_at' => DB::raw('CURRENT_TIMESTAMP')
            ],
            [
                'nombre' => 'Asistente administrativo',
                'codigo' => 'RO02',
                'estado' => true,
                'created_at' => DB::raw('CURRENT_TIMESTAMP')
            ],
            [
                'nombre' => 'Coordinador',
                'codigo' => 'RO03',
                'estado' => true,
                'created_at' => DB::raw('CURRENT_TIMESTAMP')
            ],
            [
                'nombre' => 'Asistente de producción',
                'codigo' => 'RO04',
                'estado' => true,
                'created_at' => DB::raw('CURRENT_TIMESTAMP')
            ],
            

        ];
        foreach ($rolessData as $data) {
            DB::table('roles')->insert($data);
        }
       

   
    }
}
