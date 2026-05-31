<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckAdmin
{
    public function handle(Request $request, Closure $next)
    {
        // Verifica se está logado
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // REGRAS: E-mail fixo ou campo 'tipo' igual a 'admin'
        if ($user->email === 'l@gmail.com' || $user->tipo === 'admin') {
            return $next($request);
        }

        // Se não for admin, manda para o painel do professor
        return redirect()->route('professor.painel');
    }
}