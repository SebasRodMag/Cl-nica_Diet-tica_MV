<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    // Relaciones

    // Citas donde es paciente
    public function citasComoPaciente()
    {
        return $this->hasMany(Cita::class, 'paciente_id');
    }

    // Citas donde es especialista
    public function citasComoEspecialista()
    {
        return $this->hasMany(Cita::class, 'especialista_id');
    }
}
