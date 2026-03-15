<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Cumpleaños solo en Alumno; quitar de Familia.
     */
    public function up(): void
    {
        Schema::table('familias', function (Blueprint $table) {
            $table->dropColumn('fecha_cumpleanos');
        });
    }

    public function down(): void
    {
        Schema::table('familias', function (Blueprint $table) {
            $table->date('fecha_cumpleanos')->nullable()->after('email_padre');
        });
    }
};
