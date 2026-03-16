<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('configuracion_calendario', function (Blueprint $table) {
            $table->date('fecha_fin_clases')->nullable()->after('fecha_inicio_clases');
        });
    }

    public function down(): void
    {
        Schema::table('configuracion_calendario', function (Blueprint $table) {
            $table->dropColumn('fecha_fin_clases');
        });
    }
};

