<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function hasIndex(string $table, string $name): bool
    {
        return collect(Schema::getIndexes($table))->contains(fn ($index) => ($index['name'] ?? null) === $name);
    }

    public function up(): void
    {
        if (Schema::hasTable('reservas') && !$this->hasIndex('reservas', 'reservas_sala_data_periodo_status_index')) {
            Schema::table('reservas', function (Blueprint $table) {
                $table->index(['sala_id', 'data_reserva', 'periodo', 'status'], 'reservas_sala_data_periodo_status_index');
            });
        }

        if (Schema::hasTable('mensagens_diretas') && !$this->hasIndex('mensagens_diretas', 'mensagens_diretas_conversa_index')) {
            Schema::table('mensagens_diretas', function (Blueprint $table) {
                $table->index(['remetente_id', 'destinatario_id', 'created_at'], 'mensagens_diretas_conversa_index');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('reservas') && $this->hasIndex('reservas', 'reservas_sala_data_periodo_status_index')) {
            Schema::table('reservas', function (Blueprint $table) {
                $table->dropIndex('reservas_sala_data_periodo_status_index');
            });
        }

        if (Schema::hasTable('mensagens_diretas') && $this->hasIndex('mensagens_diretas', 'mensagens_diretas_conversa_index')) {
            Schema::table('mensagens_diretas', function (Blueprint $table) {
                $table->dropIndex('mensagens_diretas_conversa_index');
            });
        }
    }
};
