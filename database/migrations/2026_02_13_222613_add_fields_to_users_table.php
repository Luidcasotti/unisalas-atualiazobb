<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Esta função ADICIONA os campos no banco de dados.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 1. Adiciona o campo 'tipo' (admin ou professor)
            // O default('professor') garante que todo novo usuário comece como professor
            if (!Schema::hasColumn('users', 'tipo')) {
                $table->string('tipo')->default('professor')->after('email');
            }

            // 2. Adiciona o campo 'telefone' (pode ser vazio/null)
            if (!Schema::hasColumn('users', 'telefone')) {
                $table->string('telefone')->nullable()->after('tipo');
            }
        });
    }

    /**
     * Reverse the migrations.
     * Esta função REMOVE os campos caso você precise desfazer a migration.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['tipo', 'telefone']);
        });
    }
};