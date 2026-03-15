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
        Schema::create('intercambios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asignacion_id')->constrained('asignaciones')->cascadeOnUpdate()->cascadeOnDelete();
            $table->enum('rol', ['fruta', 'elaboracion']);
            $table->foreignId('alumno_original_id')->constrained('alumnos')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('alumno_nuevo_id')->constrained('alumnos')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('motivo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intercambios');
    }
};
