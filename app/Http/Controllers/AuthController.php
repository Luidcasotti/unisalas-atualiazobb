<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function telaLogin()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        // 1. Validação simples
        $credenciais = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 2. Tenta autenticar
        if (Auth::attempt($credenciais)) {
            $request->session()->regenerate();
            $user = Auth::user();

            // 3. Define o perfil na sessão para o Middleware e Sidebar funcionarem
            $perfil = ($user->email === 'l@gmail.com' || $user->tipo === 'admin') ? 'admin' : 'professor';
            
            session([
                'user_perfil' => $perfil, 
                'user_name'   => $user->name
            ]);

            // 4. Redireciona baseado no perfil
            return ($perfil === 'admin') 
                ? redirect()->route('admin.dashboard') 
                : redirect()->route('professor.painel');
        }

        // 5. Se falhar, retorna com erro
        return back()->withErrors([
            'email' => 'As credenciais fornecidas estão incorretas ou o usuário não existe.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}