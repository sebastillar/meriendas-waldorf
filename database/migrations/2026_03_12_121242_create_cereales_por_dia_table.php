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
        Schema::create('cereales_por_dia', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('dia_semana'); // 1=lunes ... 7=domingo
            $table->string('cereal');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cereales_por_dia');
    }
};
