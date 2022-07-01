<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
            'rol' => 'Administrador'
        ]);

        DB::table('roles')->insert([
            'rol' => 'Vendedor'
        ]);

        DB::table('roles')->insert([
            'rol' => 'Cliente'
        ]);

        DB::table('users')->insert([
            'nombre' => 'Yosef',
            'email' => 'yosef@mail.com',
            'password' => Hash::make('12345678'),
            'role_id' => 1
        ]);

        DB::table('users')->insert([
            'nombre' => 'Juan',
            'email' => 'juan@mail.com',
            'password' => Hash::make('12345678'),
            'role_id' => 2
        ]);

        DB::table('users')->insert([
            'nombre' => 'Oscar',
            'email' => 'oscar@mail.com',
            'password' => Hash::make('12345678'),
            'role_id' => 3
        ]);

        DB::table('products')->insert([
            'nombre_producto' => 'Papas Margarita',
            'referencia' => 'pollo 110g',
            'precio' => 4150,
            'categoria' => 'Pasabocas',
            'stock' => 20,
        ]);

        DB::table('products')->insert([
            'nombre_producto' => 'Ponqué Ramo Tradicional',
            'referencia' => '236g',
            'precio' => 4200,
            'categoria' => 'Pasteleria',
            'stock' => 10,
        ]);

        DB::table('products')->insert([
            'nombre_producto' => 'Maní Maní Moto',
            'referencia' => 'limon 46g',
            'precio' => 1500,
            'categoria' => 'Pasabocas',
            'stock' => 10,
        ]);

        Log::notice('Migration tables created from database seeder');
    }
}
