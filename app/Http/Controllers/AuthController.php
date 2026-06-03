<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function telaLogin()
    {
        return response()
            ->view('auth.login')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache');
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

    public function loginLocal(Request $request)
    {
        abort_unless(app()->environment('local'), 404);

        return $this->autenticar($request);
    }

    public function loginDemo(Request $request, string $perfil)
    {
        abort_unless(app()->environment('local'), 404);

        $email = match ($perfil) {
            'admin' => 'l@gmail.com',
            'professor' => 'professor@unisalas.local',
            default => abort(404),
        };

        $user = User::where('email', $email)->firstOrFail();

        Auth::login($user);
        $request->session()->regenerate();

        session([
            'user_perfil' => $user->tipo === 'admin' ? 'admin' : 'professor',
            'user_name' => $user->name,
        ]);

        return $user->tipo === 'admin'
            ? redirect()->route('admin.dashboard')
            : redirect()->route('professor.painel');
    }

    private function autenticar(Request $request)
    {
        $credenciais = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credenciais)) {
            $request->session()->regenerate();
            $user = Auth::user();
            $perfil = ($user->email === 'l@gmail.com' || $user->tipo === 'admin') ? 'admin' : 'professor';

            session([
                'user_perfil' => $perfil,
                'user_name' => $user->name,
            ]);

            return $perfil === 'admin'
                ? redirect()->route('admin.dashboard')
                : redirect()->route('professor.painel');
        }

        return back()->withErrors([
            'email' => 'As credenciais fornecidas estao incorretas ou o usuario nao existe.',
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
