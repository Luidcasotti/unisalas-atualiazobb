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
        Schema::create('salas', function (Blueprint $table) {
            $table->id();
            $table->string('nome'); // Ex: Sala 101, Laboratório 02
            $table->integer('capacidade')->nullable(); // Capacidade de alunos (opcional)
            
            // Cria a ligação com a tabela de blocos
            // Se o bloco for deletado, as salas dele também serão (onDelete cascade)
            $table->foreignId('bloco_id')->constrained('blocos')->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salas');
    }
};