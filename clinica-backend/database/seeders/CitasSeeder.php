<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cita;

class CitasSeeder extends Seeder
{
    public function run()
    {
        $citasACrear = 200; //Cantidad de citas
        $intentosMaximos = 20; // Para evitar bucle infinito si hay muchas colisiones
        $creadas = 0;
        $intentos = 0;

        while ($creadas < $citasACrear && $intentos < $intentosMaximos) {
            $intentos++;

            $cita = Cita::factory()->make();

            //Verificar si ya existe cita con paciente, especialista, fecha y hora idénticos
            $existe = Cita::where('paciente_id', $cita->paciente_id)
                ->where('especialista_id', $cita->especialista_id)
                ->where('fecha_cita', $cita->fecha_cita)
                ->where('hora_cita', $cita->hora_cita)
                ->exists();

            if (!$existe) {
                $cita->save();
                $creadas++;
            }
        }

        if ($creadas < $citasACrear) {
            $this->command->warn("Solo se pudieron crear {$creadas} citas después de {$intentos} intentos.");
        } else {
            $this->command->info("Se crearon {$creadas} citas correctamente.");
        }
    }
}
