<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::create('reservas', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained(); // Quem pediu
        $table->foreignId('sala_id')->constrained(); // Qual sala
        $table->date('data_reserva');                // Que dia
        $table->string('periodo');                   // Matutino, Vespertino ou Noturno
        $table->string('status')->default('pendente'); // pendente, aprovada, recusada
        $table->text('observacao')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservas');
    }
};
