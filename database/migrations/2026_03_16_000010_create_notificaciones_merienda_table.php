<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notificaciones_merienda', function (Blueprint $table) {
            $table->id();
            $table->date('fecha_envio_programada');
            $table->string('tipo', 50)->default('recordatorio_merienda');
            $table->string('email');
            $table->string('rol', 20)->nullable();
            $table->string('nombre_alumno');
            $table->string('estado', 20)->default('pendiente'); // pendiente, enviado, fallido
            $table->unsignedSmallInteger('intentos')->default(0);
            $table->timestamp('ultimo_intento_at')->nullable();
            $table->text('error_ultimo_intento')->nullable();
            $table->timestamps();

            $table->index(['fecha_envio_programada', 'tipo', 'email'], 'notificaciones_fecha_tipo_email');
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificaciones_merienda');
    }
};

