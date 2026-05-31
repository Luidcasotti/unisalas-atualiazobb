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
        Schema::table('blocos', function (Blueprint $table) {
            // Adiciona a coluna cor com um valor padrão roxo
            $table->string('cor')->default('#7010a8');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blocos', function (Blueprint $table) {
            // Remove a coluna caso precise desfazer a migração
            $table->dropColumn('cor');
        });
    }
};