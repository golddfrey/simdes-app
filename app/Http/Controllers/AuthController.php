<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\KepalaKeluarga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->only([
            'showAdminLogin', 'adminLogin',
            'showKkLogin', 'kkLogin',
        ]);

        $this->middleware('auth')->only(['logout']);
    }

    /* =========================
     * ADMIN LOGIN (EMAIL)
     * ========================= */
    public function showAdminLogin()
    {
        return view('auth.login-admin');
    }

    public function adminLogin(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // force role=admin
        $remember = $request->boolean('remember');

        if (Auth::attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
            'role' => 'admin',
        ], $remember)) {
            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'))
                ->with('success', 'Berhasil login sebagai Admin.');
        }

        return back()->withInput($request->only('email'))
            ->withErrors(['email' => 'Email / password tidak valid atau bukan admin.']);
    }

    /* =========================
     * KEPALA KELUARGA LOGIN (NIK)
     * ========================= */
    public function showKkLogin()
    {
        return view('auth.login-kk');
    }

    public function kkLogin(Request $request)
    {
        $data = $request->validate([
            'nik'      => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // Cari KK berdasarkan NIK
        $kk = KepalaKeluarga::where('nik', $data['nik'])->first();

        if (!$kk) {
            return back()->withInput($request->only('nik'))
                ->withErrors(['nik' => 'NIK tidak ditemukan.']);
        }

        // Cari user yang terhubung dengan KK tsb & berperan kepala_keluarga
        $user = User::where('role', 'kepala_keluarga')
            ->where('kepala_keluarga_id', $kk->id)
            ->first();

        if (!$user) {
            return back()->withInput($request->only('nik'))
                ->withErrors(['nik' => 'Akun untuk NIK tersebut belum dibuat oleh petugas.']);
        }

        // Verifikasi password manual (karena kita tidak login via email)
        if (!Hash::check($data['password'], $user->password)) {
            return back()->withInput($request->only('nik'))
                ->withErrors(['password' => 'Kata sandi salah.']);
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended(route('kk.dashboard'))
            ->with('success', 'Berhasil login sebagai Kepala Keluarga.');
    }

    /* =========================
     * LOGOUT
     * ========================= */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Anda berhasil logout.');
    }
}
