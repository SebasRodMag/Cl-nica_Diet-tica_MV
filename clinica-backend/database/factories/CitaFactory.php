<?php

namespace Database\Factories;

use App\Models\Cita;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class CitaFactory extends Factory
{
    protected $model = Cita::class;

    //Definimos los festivos
    protected $festivos = [
        '2025-01-01',
        '2025-05-01',
        '2025-12-25',
    ];

    public function definition(): array
    {
        $fecha = $this->obtenerFechaValida();

        $horaInicio = Carbon::createFromTime(8, 0);
        $bloque = rand(0, 13); // 14 bloques de 30 min (08:00 a 15:00)
        $hora = $horaInicio->copy()->addMinutes($bloque * 30);

        return [
            'paciente_id' => User::role('paciente')->inRandomOrder()->first()?->id,
            'especialista_id' => User::role('especialista')->inRandomOrder()->first()?->id,
            'fecha_cita' => $fecha->toDateString(),
            'hora_cita' => $hora->format('H:i:s'),
            'estado' => 'pendiente',
            'comentarios' => $this->faker->sentence(),
        ];
    }

    /**
     * Obtiene una fecha válida que no sea sábado, domingo ni festivo.
     */
    private function obtenerFechaValida(): Carbon
    {
        $fecha = Carbon::now()->addDays(rand(0, 30))->startOfDay();

        // Si cae en fin de semana o festivo, se recorre hasta encontrar fecha válida
        while ($this->esFinDeSemana($fecha) || $this->esFestivo($fecha)) {
            $fecha->addDay();
        }

        return $fecha;
    }

    private function esFinDeSemana(Carbon $fecha): bool
    {
        return $fecha->isWeekend();
    }

    private function esFestivo(Carbon $fecha): bool
    {
        return in_array($fecha->toDateString(), $this->festivos);
    }
}
