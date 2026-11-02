<?php

namespace App\Http\Controllers;

use App\Models\KamusIsyarat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KamusAdminController extends Controller
{
    /**
     * GET /api/kamus
     * Daftar seluruh aset isyarat (admin)
     */
    public function index(): JsonResponse
    {
        $rows = KamusIsyarat::with('kategori')
            ->orderBy('kata_dasar')
            ->get();

        return response()->json($rows);
    }

    /**
     * POST /api/kamus
     * Tambah aset isyarat
     */
    public function store(Request $r): JsonResponse
    {
        $r->validate([
            'kata_dasar' => 'required|string|max:100|unique:kamus_isyarat,kata_dasar',
            'kategori_id' => 'nullable|exists:kategori_isyarat,id',
            'media' => 'required|file|mimes:gif,png,jpg,jpeg,mp4,webm|max:10240', // format/ukuran salah -> 422
            'durasi_ms' => 'nullable|integer|min:500|max:10000',
        ]);

        $file = $r->file('media');
        $path = $file->store('bisindo', 'public');

        $kamus = KamusIsyarat::create([
            'kategori_id' => $r->kategori_id,
            'kata_dasar' => mb_strtolower($r->kata_dasar),
            'tipe_media' => $this->detectMediaType($file->extension()),
            'url_media' => $path,
            'durasi_ms' => $r->durasi_ms ?? 2000,
            'is_active' => true,
        ]);

        return response()->json([
            'message' => 'Berhasil disimpan',
            'id' => $kamus->id,
        ], 201);
    }

    /**
     * PUT /api/kamus/{id}
     * Ubah aset isyarat
     */
    public function update(Request $r, $id): JsonResponse
    {
        $kamus = KamusIsyarat::find($id);
        abort_unless($kamus, 404, 'Isyarat tidak ditemukan');

        $r->validate([
            'kata_dasar' => 'nullable|string|max:100|unique:kamus_isyarat,kata_dasar,' . $id,
            'kategori_id' => 'nullable|exists:kategori_isyarat,id',
            'durasi_ms' => 'nullable|integer|min:500|max:10000',
            'is_active' => 'nullable|boolean',
            'media' => 'nullable|file|mimes:gif,png,jpg,jpeg,mp4,webm|max:10240',
        ]);

        $data = $r->only(['kategori_id', 'durasi_ms', 'is_active']);

        if ($r->filled('kata_dasar')) {
            $data['kata_dasar'] = mb_strtolower($r->kata_dasar);
        }

        if ($file = $r->file('media')) {
            if ($kamus->url_media && Storage::disk('public')->exists($kamus->url_media)) {
                Storage::disk('public')->delete($kamus->url_media);
            }
            $data['url_media'] = $file->store('bisindo', 'public');
            $data['tipe_media'] = $this->detectMediaType($file->extension());
        }

        if (!empty($data)) {
            $kamus->update($data);
        }

        return response()->json([
            'message' => 'Berhasil diperbarui',
        ]);
    }

    /**
     * DELETE /api/kamus/{id}
     * Hapus aset isyarat
     */
    public function destroy($id): JsonResponse
    {
        $kamus = KamusIsyarat::find($id);
        abort_unless($kamus, 404, 'Isyarat tidak ditemukan');

        if ($kamus->url_media && Storage::disk('public')->exists($kamus->url_media)) {
            Storage::disk('public')->delete($kamus->url_media);
        }

        $kamus->delete();

        return response()->json([
            'message' => 'Berhasil dihapus',
        ]);
    }

    /**
     * Deteksi tipe media berdasarkan ekstensi
     */
    private function detectMediaType(string $ext): string
    {
        $ext = strtolower($ext);
        if ($ext === 'gif') {
            return 'gif';
        }
        if (in_array($ext, ['mp4', 'webm'])) {
            return 'video';
        }
        return 'gambar';
    }
}
