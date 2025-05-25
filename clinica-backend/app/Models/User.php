<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable, HasRoles, HasApiTokens;

    protected $fillable = [
        'nombre',
        'email',
        'password',
    ];

    public function citasComoPaciente()
    {
        return $this->hasMany(Cita::class, 'paciente_id');
    }

    public function citasComoEspecialista()
    {
        return $this->hasMany(Cita::class, 'especialista_id');
    }
}
