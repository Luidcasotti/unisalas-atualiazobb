<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\MensagemDireta;
use App\Models\Reserva;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Limpeza automatica de historico antigo (mais de 6 meses)
Schedule::call(function () {
    MensagemDireta::where('created_at', '<', now()->subMonths(6))->delete();
    Reserva::where('data_reserva', '<', now()->subMonths(6)->toDateString())->delete();
})->daily();
