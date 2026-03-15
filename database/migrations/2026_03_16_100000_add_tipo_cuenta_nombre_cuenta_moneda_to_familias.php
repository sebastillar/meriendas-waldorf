<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('familias', function (Blueprint $table) {
            $table->string('tipo_cuenta', 100)->nullable()->after('numero_cuenta');
            $table->string('nombre_cuenta', 255)->nullable()->after('tipo_cuenta');
            $table->string('moneda', 50)->nullable()->after('nombre_cuenta');
        });
    }

    public function down(): void
    {
        Schema::table('familias', function (Blueprint $table) {
            $table->dropColumn(['tipo_cuenta', 'nombre_cuenta', 'moneda']);
        });
    }
};
