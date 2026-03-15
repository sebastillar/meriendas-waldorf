<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('asignaciones', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->foreignId('alumno_fruta_id')->constrained('alumnos')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('alumno_elaboracion_id')->constrained('alumnos')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('cereal');
            $table->enum('estado', ['planificada', 'confirmada', 'intercambiada', 'cancelada'])->default('planificada');
            $table->timestamps();

            $table->unique('fecha');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asignaciones');
    }
};
