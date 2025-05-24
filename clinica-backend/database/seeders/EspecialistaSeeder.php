<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Especialista;
use Spatie\Permission\Models\Role;
use Faker\Factory as Faker;

class EspecialistasSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        $especialistaRole = Role::where('name', 'especialista')->first();

        if (!$especialistaRole) {
            $this->command->error('No existe el rol especialista.');
            return;
        }

        $usuariosEspecialistas = User::role('especialista')->get();

        foreach ($usuariosEspecialistas as $usuario) {
            $especialistaExistente = Especialista::where('user_id', $usuario->id)->first();
            if (!$especialistaExistente) {
                Especialista::create([
                    'user_id' => $usuario->id,
                    'telefono' => $faker->phoneNumber,
                    'especialidad' => $faker->randomElement(['Nutrición', 'Dietética', 'Endocrinología', 'Psicología']),
                ]);
            }
        }
    }
}
