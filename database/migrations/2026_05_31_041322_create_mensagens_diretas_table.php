<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Usamos o ifNotExists para evitar erros se a tabela já estiver lá
        if (!Schema::hasTable('mensagens_diretas')) {
            Schema::create('mensagens_diretas', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('remetente_id');
                $table->unsignedBigInteger('destinatario_id');
                $table->text('mensagem');
                $table->boolean('lida')->default(false); // Já incluímos a coluna aqui
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('mensagens_diretas');
    }
};
