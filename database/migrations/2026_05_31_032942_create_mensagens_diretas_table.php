<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('mensagens_diretas')) {
            Schema::create('mensagens_diretas', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('remetente_id');
                $table->unsignedBigInteger('destinatario_id');
                $table->text('mensagem');
                $table->boolean('lida')->default(false);
                $table->timestamps();
            });

            return;
        }

        Schema::table('mensagens_diretas', function (Blueprint $table) {
            if (!Schema::hasColumn('mensagens_diretas', 'remetente_id')) {
                $table->unsignedBigInteger('remetente_id')->after('id');
            }
            if (!Schema::hasColumn('mensagens_diretas', 'destinatario_id')) {
                $table->unsignedBigInteger('destinatario_id')->after('remetente_id');
            }
            if (!Schema::hasColumn('mensagens_diretas', 'mensagem')) {
                $table->text('mensagem')->after('destinatario_id');
            }
            if (!Schema::hasColumn('mensagens_diretas', 'lida')) {
                $table->boolean('lida')->default(false)->after('mensagem');
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mensagens_diretas');
    }
};
