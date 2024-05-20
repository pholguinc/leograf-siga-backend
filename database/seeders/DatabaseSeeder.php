<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(UserSeeder::class);
        $this->call(ModuloSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(PermisoSeeder::class);
        $this->call(TipoEnumeradoSeeder::class);
        $this->call(EnumeradoSeeder::class);
    }
}
