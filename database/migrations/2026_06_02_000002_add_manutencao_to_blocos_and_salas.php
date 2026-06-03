<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blocos', function (Blueprint $table) {
            if (!Schema::hasColumn('blocos', 'manutencao_ativa')) {
                $table->boolean('manutencao_ativa')->default(false);
            }
            if (!Schema::hasColumn('blocos', 'manutencao_fim')) {
                $table->date('manutencao_fim')->nullable();
            }
            if (!Schema::hasColumn('blocos', 'manutencao_indeterminada')) {
                $table->boolean('manutencao_indeterminada')->default(false);
            }
            if (!Schema::hasColumn('blocos', 'manutencao_aviso')) {
                $table->text('manutencao_aviso')->nullable();
            }
        });

        Schema::table('salas', function (Blueprint $table) {
            if (!Schema::hasColumn('salas', 'manutencao_ativa')) {
                $table->boolean('manutencao_ativa')->default(false);
            }
            if (!Schema::hasColumn('salas', 'manutencao_fim')) {
                $table->date('manutencao_fim')->nullable();
            }
            if (!Schema::hasColumn('salas', 'manutencao_indeterminada')) {
                $table->boolean('manutencao_indeterminada')->default(false);
            }
            if (!Schema::hasColumn('salas', 'manutencao_aviso')) {
                $table->text('manutencao_aviso')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('blocos', function (Blueprint $table) {
            foreach (['manutencao_ativa', 'manutencao_fim', 'manutencao_indeterminada', 'manutencao_aviso'] as $column) {
                if (Schema::hasColumn('blocos', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('salas', function (Blueprint $table) {
            foreach (['manutencao_ativa', 'manutencao_fim', 'manutencao_indeterminada', 'manutencao_aviso'] as $column) {
                if (Schema::hasColumn('salas', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
