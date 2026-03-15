<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recolecta_aportes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('familia_beneficiaria_id')->constrained('familias')->cascadeOnDelete();
            $table->foreignId('alumno_id')->constrained('alumnos')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['familia_beneficiaria_id', 'alumno_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recolecta_aportes');
    }
};
