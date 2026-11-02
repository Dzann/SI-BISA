<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * POST /api/login
     */
    public function login(Request $r): JsonResponse
    {
        $credentials = $r->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($credentials, $r->boolean('remember'))) {
            // Sesuai use case Peringatan Login Gagal (status 422)
            return response()->json([
                'message' => 'Email atau kata sandi salah',
            ], 422);
        }

        $r->session()->regenerate();

        return response()->json([
            'message' => 'Login berhasil',
            'role' => Auth::user()->role,
        ]);
    }

    /**
     * GET /api/me
     */
    public function me(Request $r): JsonResponse
    {
        $user = $r->user();

        if (!$user) {
            return response()->json(null);
        }

        return response()->json([
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ]);
    }

    /**
     * POST /api/logout
     */
    public function logout(Request $r): JsonResponse
    {
        Auth::logout();
        $r->session()->invalidate();
        $r->session()->regenerateToken();

        return response()->json([
            'message' => 'Keluar',
        ]);
    }
}
