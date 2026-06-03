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
        Schema::create('notificacao_visualizadas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('tipo', 80);
            $table->string('referencia', 160);
            $table->timestamp('visualizada_em')->useCurrent();
            $table->timestamps();

            $table->unique(['user_id', 'tipo', 'referencia'], 'notificacao_visualizadas_unique');
            $table->index(['user_id', 'tipo'], 'notificacao_visualizadas_user_tipo_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notificacao_visualizadas');
    }
};
