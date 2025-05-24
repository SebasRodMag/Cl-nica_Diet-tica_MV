<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Paciente;
use Spatie\Permission\Models\Role;
use Faker\Factory as Faker;

class PacientesSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        $pacienteRole = Role::where('name', 'paciente')->first();

        if (!$pacienteRole) {
            $this->command->error('No existe el rol paciente.');
            return;
        }

        $usuariosPacientes = User::role('paciente')->get();

        foreach ($usuariosPacientes as $usuario) {
            $pacienteExistente = Paciente::where('user_id', $usuario->id)->first();
            if (!$pacienteExistente) {
                Paciente::create([
                    'user_id' => $usuario->id,
                    'fecha_nacimiento' => $faker->dateTimeBetween('-65 years', '-18 years')->format('Y-m-d'),
                    'telefono' => $faker->phoneNumber,
                    // otros campos que puedas necesitar
                ]);
            }
        }
    }
}
