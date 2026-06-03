<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('mensagens_diretas') || Schema::hasColumn('mensagens_diretas', 'lida')) {
            return;
        }

        Schema::table('mensagens_diretas', function (Blueprint $table) {
            $table->boolean('lida')->default(false);
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('mensagens_diretas') || !Schema::hasColumn('mensagens_diretas', 'lida')) {
            return;
        }

        Schema::table('mensagens_diretas', function (Blueprint $table) {
            $table->dropColumn('lida');
        });
    }
};
