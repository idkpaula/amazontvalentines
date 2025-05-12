<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class userSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'id' => 1,
                'name' => 'AMAZONT',
                'email' => 'amazont@amazont.com',
                'password' => Hash::make('Administrador10@'),
                'rol' => 'Vendedor',
            ],
            [
                'id' => 2,
                'name' => 'Juan Pérez',
                'email' => 'juanperez@email.com',
                'password' => Hash::make('Cliente10@'),
                'rol' => 'Cliente',
            ],
            [
                'id' => 3,
                'name' => 'María López',
                'email' => 'marialopez@email.com',
                'password' => Hash::make('Cliente10@'),
                'rol' => 'Cliente',
            ],
            [
                'id' => 4,
                'name' => 'Carlos Ramírez',
                'email' => 'carlosramirez@email.com',
                'password' => Hash::make('Vendedor10@'),
                'rol' => 'Vendedor',
            ],
            [
                'id' => 5,
                'name' => 'Ana Torres',
                'email' => 'anatorres@email.com',
                'password' => Hash::make('Vendedor10@'),
                'rol' => 'Vendedor',
            ],
        ]);
    }
}
