<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cita extends Model
{
    use SoftDeletes;

    protected $fillable = ['paciente_id', 'especialista_id', 'fecha', 'hora', 'estado', 'comentarios'];

    public function paciente()
    {
        return $this->belongsTo(User::class, 'paciente_id');
    }

    public function especialista()
    {
        return $this->belongsTo(User::class, 'especialista_id');
    }
}
