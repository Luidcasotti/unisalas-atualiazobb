<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blocos', function (Blueprint $table) {
            $table->string('cor')->default('#7010a8'); // Adiciona a coluna
        });
    }

    public function down(): void
    {
        Schema::table('blocos', function (Blueprint $table) {
            $table->dropColumn('cor');
        });
    }
};