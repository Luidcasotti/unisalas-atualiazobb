<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservas', function (Blueprint $table) {
            if (!Schema::hasColumn('reservas', 'comentario_professor')) {
                $table->text('comentario_professor')->nullable();
            }
            if (!Schema::hasColumn('reservas', 'comentario_adm')) {
                $table->text('comentario_adm')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('reservas', function (Blueprint $table) {
            if (Schema::hasColumn('reservas', 'comentario_professor')) {
                $table->dropColumn('comentario_professor');
            }
            if (Schema::hasColumn('reservas', 'comentario_adm')) {
                $table->dropColumn('comentario_adm');
            }
        });
    }
};