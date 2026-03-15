<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('familias', function (Blueprint $table) {
            $table->date('fecha_cumpleanos')->nullable()->after('email_padre');
            $table->foreignId('familia_regalo_id')->nullable()->after('fecha_cumpleanos')->constrained('familias')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('familias', function (Blueprint $table) {
            $table->dropForeign(['familia_regalo_id']);
            $table->dropColumn(['fecha_cumpleanos', 'familia_regalo_id']);
        });
    }
};
