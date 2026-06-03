<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Verifica se a coluna 'cor' ainda não existe antes de tentar adicioná-la
        if (!Schema::hasColumn('blocos', 'cor')) {
            Schema::table('blocos', function (Blueprint $table) {
                $table->string('cor')->default('#7010a8');
            });
        }
    }

    public function down(): void
    {
        // Remove a coluna apenas se ela existir
        if (Schema::hasColumn('blocos', 'cor')) {
            Schema::table('blocos', function (Blueprint $table) {
                $table->dropColumn('cor');
            });
        }
    }
};