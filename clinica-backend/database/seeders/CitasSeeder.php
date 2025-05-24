<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Cita;
use Faker\Factory as Faker;
use Carbon\Carbon;

class CitasSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        $duracionCitaMinutos = 30;
        $horarioInicio = 8;  // 08:00 horas
        $horarioFin = 15;    // 15:00 horas

        $fechaInicio = Carbon::today();

        // Obtener especialistas y pacientes
        $especialistas = User::role('especialista')->get();
        $pacientes = User::role('paciente')->get();

        // Crear citas para cada paciente con especialistas aleatorios
        foreach ($pacientes as $paciente) {
            // Cada paciente tendrá entre 1 y 3 citas
            $numCitas = rand(1, 3);

            for ($i = 0; $i < $numCitas; $i++) {
                $especialista = $especialistas->random();

                // Generar fecha y hora de la cita dentro del rango permitido
                $fecha = $fechaInicio->copy()->addDays(rand(0, 30));
                $hora = rand($horarioInicio * 60, ($horarioFin - 1) * 60); // minutos desde medianoche

                // Convertir minutos a hora y minuto
                $horaCita = intdiv($hora, 60);
                $minutoCita = $hora % 60;

                Cita::create([
                    'paciente_id' => $paciente->id,
                    'especialista_id' => $especialista->id,
                    'fecha' => $fecha->toDateString(),
                    'hora' => sprintf('%02d:%02d:00', $horaCita, $minutoCita),
                    'estado' => 'pendiente',
                    'comentarios' => $faker->sentence(),
                ]);
            }
        }
    }
}
