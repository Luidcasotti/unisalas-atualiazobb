<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function hasIndex(string $table, string $name): bool
    {
        return collect(Schema::getIndexes($table))->contains(fn ($index) => ($index['name'] ?? null) === $name);
    }

    private function hasForeignKey(string $table, string $name): bool
    {
        $database = DB::getDatabaseName();

        return DB::table('information_schema.table_constraints')
            ->where('table_schema', $database)
            ->where('table_name', $table)
            ->where('constraint_name', $name)
            ->where('constraint_type', 'FOREIGN KEY')
            ->exists();
    }

    public function up(): void
    {
        if (Schema::hasTable('mensagens_diretas') && !$this->hasForeignKey('mensagens_diretas', 'mensagens_diretas_destinatario_id_foreign')) {
            Schema::table('mensagens_diretas', function (Blueprint $table) {
                $table->foreign('destinatario_id', 'mensagens_diretas_destinatario_id_foreign')
                    ->references('id')
                    ->on('users')
                    ->cascadeOnDelete();
            });
        }

        if (Schema::hasTable('reservas') && !$this->hasIndex('reservas', 'reservas_user_status_updated_index')) {
            Schema::table('reservas', function (Blueprint $table) {
                $table->index(['user_id', 'status', 'updated_at'], 'reservas_user_status_updated_index');
            });
        }

        if (Schema::hasTable('reservas') && !$this->hasIndex('reservas', 'reservas_grupo_recorrencia_index')) {
            Schema::table('reservas', function (Blueprint $table) {
                $table->index('grupo_recorrencia', 'reservas_grupo_recorrencia_index');
            });
        }

        if (Schema::hasTable('avisos') && !$this->hasIndex('avisos', 'avisos_created_at_index')) {
            Schema::table('avisos', function (Blueprint $table) {
                $table->index('created_at', 'avisos_created_at_index');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('avisos') && $this->hasIndex('avisos', 'avisos_created_at_index')) {
            Schema::table('avisos', function (Blueprint $table) {
                $table->dropIndex('avisos_created_at_index');
            });
        }

        if (Schema::hasTable('reservas') && $this->hasIndex('reservas', 'reservas_grupo_recorrencia_index')) {
            Schema::table('reservas', function (Blueprint $table) {
                $table->dropIndex('reservas_grupo_recorrencia_index');
            });
        }

        if (Schema::hasTable('reservas') && $this->hasIndex('reservas', 'reservas_user_status_updated_index')) {
            Schema::table('reservas', function (Blueprint $table) {
                $table->dropIndex('reservas_user_status_updated_index');
            });
        }

        if (Schema::hasTable('mensagens_diretas') && $this->hasForeignKey('mensagens_diretas', 'mensagens_diretas_destinatario_id_foreign')) {
            Schema::table('mensagens_diretas', function (Blueprint $table) {
                $table->dropForeign('mensagens_diretas_destinatario_id_foreign');
            });
        }
    }
};
