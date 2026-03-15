<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Para limpiar familias duplicadas, ejecutar:
 * php artisan db:seed --class=RemoverFamiliasDuplicadasSeeder
 * (antes de esta migración: agrupa por apellido; después: por nombre del primer hijo).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('familias', function (Blueprint $table) {
            $table->dropColumn('apellido');
        });
    }

    public function down(): void
    {
        Schema::table('familias', function (Blueprint $table) {
            $table->string('apellido')->after('id');
        });
    }
};
