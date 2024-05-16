<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Permission::create(['name' => 'Acceder Seguridad']);
        Permission::create(['name' => 'Consultar Seguridad']);
        Permission::create(['name' => 'Agregar Seguridad']);
        Permission::create(['name' => 'Modificar  Seguridad']);
        Permission::create(['name' => 'Eliminar Seguridad']);
        Permission::create(['name' => 'Aprobar Seguridad']);
        Permission::create(['name' => 'Asignar Seguridad']);
        Permission::create(['name' => 'Observar Seguridad']);
        Permission::create(['name' => 'Derivar Seguridad']);
        Permission::create(['name' => 'Exportar Seguridad']);
        Permission::create(['name' => 'Importar Seguridad']);
        Permission::create(['name' => 'Activar Seguridad']);
        Permission::create(['name' => 'Inactivar Seguridad']);

        // Permission::create(['name'=> 'Acceder Almacen']);
        // Permission::create(['name' => 'Consultar Almacen']);
        // Permission::create(['name' => 'Agregar Almacen']);
        // Permission::create(['name' => 'Modificar  Almacen']);
        // Permission::create(['name' => 'Eliminar Almacen']);
        // Permission::create(['name' => 'Aprobar Almacen']);
        // Permission::create(['name' => 'Asignar Almacen']);
        // Permission::create(['name' => 'Observar Almacen']);
        // Permission::create(['name' => 'Derivar Almacen']);
        // Permission::create(['name' => 'Exportar Almacen']);
        // Permission::create(['name' => 'Importar Almacen']);
        // Permission::create(['name' => 'Activar Almacen']);
        // Permission::create(['name' => 'Inactivar Almacen']);
    }
}
