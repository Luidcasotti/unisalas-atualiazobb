<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservas', function (Blueprint $table) {
            if (!Schema::hasColumn('reservas', 'recorrente')) {
                $table->boolean('recorrente')->default(false)->after('status');
            }

            if (!Schema::hasColumn('reservas', 'grupo_recorrencia')) {
                $table->string('grupo_recorrencia')->nullable()->after('recorrente');
            }
        });
    }

    public function down(): void
    {
        Schema::table('reservas', function (Blueprint $table) {
            if (Schema::hasColumn('reservas', 'grupo_recorrencia')) {
                $table->dropColumn('grupo_recorrencia');
            }

            if (Schema::hasColumn('reservas', 'recorrente')) {
                $table->dropColumn('recorrente');
            }
        });
    }
};
