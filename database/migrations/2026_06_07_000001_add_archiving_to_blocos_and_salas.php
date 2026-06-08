<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blocos', function (Blueprint $table) {
            if (!Schema::hasColumn('blocos', 'arquivado_em')) {
                $table->timestamp('arquivado_em')->nullable()->after('manutencao_aviso');
            }
        });

        Schema::table('salas', function (Blueprint $table) {
            if (!Schema::hasColumn('salas', 'arquivado_em')) {
                $table->timestamp('arquivado_em')->nullable()->after('manutencao_aviso');
            }
        });
    }

    public function down(): void
    {
        Schema::table('salas', function (Blueprint $table) {
            if (Schema::hasColumn('salas', 'arquivado_em')) {
                $table->dropColumn('arquivado_em');
            }
        });

        Schema::table('blocos', function (Blueprint $table) {
            if (Schema::hasColumn('blocos', 'arquivado_em')) {
                $table->dropColumn('arquivado_em');
            }
        });
    }
};
