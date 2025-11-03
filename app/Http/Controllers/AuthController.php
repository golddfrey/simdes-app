<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\KepalaKeluarga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * AuthController constructor.
     * Guests may access login & register pages; authenticated users can logout.
     */
    public function __construct()
    {
        $this->middleware('guest')->only(['showLogin', 'login', 'showRegister', 'register']);
        $this->middleware('auth')->only(['logout']);
    }

    /**
     * Show login page (blade). If you prefer API-only, skip views and return JSON.
     *
     * @return \Illuminate\View\View
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Handle login form POST.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required','email'],
            'password' => ['required','string'],
        ]);

        // Attempt login
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('home'))->with('success', 'Berhasil login.');
        }

        return back()->withInput($request->only('email'))->withErrors([
            'email' => 'Email atau password salah.',
        ]);
    }

    /**
     * Show register page for kepala keluarga.
     * This route is public in this implementation; if you prefer admin-only creation, skip this.
     *
     * @return \Illuminate\View\View
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Handle registration of kepala_keluarga user.
     * This creates a User with role 'kepala_keluarga'. If you already have a kepala_keluarga record
     * and want to link it, pass kepala_keluarga_id in the request (optional).
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255', Rule::unique('users','email')],
            'password' => ['required','string','min:6','confirmed'],
            'kepala_keluarga_id' => ['nullable','integer','exists:kepala_keluargas,id'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'], // mutator in model will hash
            'role' => 'kepala_keluarga',
            'kepala_keluarga_id' => $data['kepala_keluarga_id'] ?? null,
        ]);

        // Auto-login after register (optional)
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('home')->with('success', 'Akun kepala keluarga berhasil dibuat.');
    }

    /**
     * Logout user.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda berhasil logout.');
    }
}
