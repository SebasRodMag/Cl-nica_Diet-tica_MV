<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('citas', function (Blueprint $table) {
        $table->id();
        $table->foreignId('paciente_id')->constrained('users')->onDelete('cascade');
        $table->foreignId('especialista_id')->constrained('users')->onDelete('cascade');
        $table->date('fecha_cita');
        $table->time('hora_cita');
        $table->enum('estado', ['pendiente', 'realizada', 'no realizada', 'cancelada'])->default('pendiente');
        $table->text('comentarios')->nullable();
        $table->softDeletes();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};
