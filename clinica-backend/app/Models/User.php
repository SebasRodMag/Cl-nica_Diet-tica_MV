<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nombre',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function citasComoPaciente()
    {
        return $this->hasMany(Cita::class, 'paciente_id');
    }

    public function citasComoEspecialista()
    {
        return $this->hasMany(Cita::class, 'especialista_id');
    }

    public function documentos()
    {
        return $this->hasMany(Documento::class, 'paciente_id');
    }

    public function logs()
    {
        return $this->hasMany(Log::class);
    }

    public function especialistas()
    {
        return $this->belongsToMany(User::class, 'consultas', 'paciente_id', 'especialista_id');
    }
}
