<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        $totalAdmins = 2;
        $totalEspecialistas = 10;
        $totalPacientes = 180;
        $totalUsuarios = 8;

        $password = Hash::make('pass123');

        $roles = ['administrador', 'especialista', 'paciente', 'usuario'];
        foreach ($roles as $rol) {
            Role::firstOrCreate(['name' => $rol]);
        }

        //Crear administradores
        for ($i = 0; $i < $totalAdmins; $i++) {
            $user = User::create([
                'name' => $faker->name,
                'email' => "admin{$i}@ejemplo.com",
                'password' => $password,
            ]);
            $user->assignRole('administrador');
        }

        //Crear especialistas
        for ($i = 0; $i < $totalEspecialistas; $i++) {
            $user = User::create([
                'name' => $faker->name,
                'email' => "especialista{$i}@ejemplo.com",
                'password' => $password,
            ]);
            $user->assignRole('especialista');
        }

        //Crear pacientes
        for ($i = 0; $i < $totalPacientes; $i++) {
            $user = User::create([
                'name' => $faker->name,
                'email' => "paciente{$i}@ejemplo.com",
                'password' => $password,
            ]);
            $user->assignRole('paciente');
        }

        //Crear usuarios normales
        for ($i = 0; $i < $totalUsuarios; $i++) {
            $user = User::create([
                'name' => $faker->name,
                'email' => "usuario{$i}@ejemplo.com",
                'password' => $password,
            ]);
            $user->assignRole('usuario');
        }
    }
}
