<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Login admin berbasis session (route di web.php agar session aktif)
class AuthController extends Controller
{
    public function login(Request $r)
    {
        $c = $r->validate(['email' => 'required|email', 'password' => 'required']);
        if (!Auth::attempt($c, $r->boolean('remember'))) {
            return response()->json(['message' => 'Email atau kata sandi salah'], 422); // Peringatan Login Gagal
        }
        $r->session()->regenerate();
        return ['message' => 'Login berhasil', 'role' => Auth::user()->role];
    }

    public function me(Request $r)
    {
        $u = $r->user();
        return $u ? ['name'=>$u->name,'email'=>$u->email,'role'=>$u->role] : response()->json(null);
    }

    public function logout(Request $r)
    {
        Auth::logout();
        $r->session()->invalidate();
        $r->session()->regenerateToken();
        return ['message' => 'Keluar'];
    }
}
