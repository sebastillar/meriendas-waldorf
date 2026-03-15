<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('familias', function (Blueprint $table) {
            $table->string('banco', 255)->nullable()->after('familia_regalo_id');
            $table->string('numero_cuenta', 255)->nullable()->after('banco');
        });
    }

    public function down(): void
    {
        Schema::table('familias', function (Blueprint $table) {
            $table->dropColumn(['banco', 'numero_cuenta']);
        });
    }
};
