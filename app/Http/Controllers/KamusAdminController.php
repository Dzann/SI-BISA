<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

// CRUD aset BISINDO (khusus admin)
class KamusAdminController extends Controller
{
    private function adminOnly(Request $r): void
    {
        abort_unless(optional($r->user())->role === 'admin', 403, 'Khusus admin');
    }

    public function index(Request $r) { $this->adminOnly($r); return DB::table('kamus_isyarat')->orderBy('kata_dasar')->get(); }

    public function store(Request $r)
    {
        $this->adminOnly($r);
        $r->validate([
            'kata_dasar' => 'required|string|unique:kamus_isyarat,kata_dasar',
            'kategori_id' => 'nullable|exists:kategori_isyarat,id',
            'media' => 'required|file|mimes:gif,png,jpg,jpeg,mp4,webm|max:10240', // format/ukuran salah -> 422
            'durasi_ms' => 'nullable|integer',
        ]);
        $f = $r->file('media');
        $path = $f->store('bisindo', 'public');
        $id = DB::table('kamus_isyarat')->insertGetId([
            'kategori_id' => $r->kategori_id, 'kata_dasar' => mb_strtolower($r->kata_dasar),
            'tipe_media' => $this->tipe($f->extension()), 'url_media' => $path,
            'durasi_ms' => $r->durasi_ms ?? 2000,
        ]);
        return response()->json(['message' => 'Berhasil disimpan', 'id' => $id], 201);
    }

    public function update(Request $r, $id)
    {
        $this->adminOnly($r);
        $row = DB::table('kamus_isyarat')->find($id); abort_unless($row, 404);
        $r->validate(['media' => 'nullable|file|mimes:gif,png,jpg,jpeg,mp4,webm|max:10240']);
        $data = $r->only(['kategori_id', 'durasi_ms', 'is_active']);
        if ($r->filled('kata_dasar')) $data['kata_dasar'] = mb_strtolower($r->kata_dasar);
        if ($f = $r->file('media')) {
            Storage::disk('public')->delete($row->url_media);
            $data += ['url_media' => $f->store('bisindo', 'public'), 'tipe_media' => $this->tipe($f->extension())];
        }
        if ($data) DB::table('kamus_isyarat')->where('id', $id)->update($data);
        return ['message' => 'Berhasil diperbarui'];
    }

    public function destroy(Request $r, $id)
    {
        $this->adminOnly($r);
        $row = DB::table('kamus_isyarat')->find($id); abort_unless($row, 404);
        Storage::disk('public')->delete($row->url_media);
        DB::table('kamus_isyarat')->where('id', $id)->delete();
        return ['message' => 'Berhasil dihapus'];
    }

    private function tipe(string $ext): string
    {
        return $ext === 'gif' ? 'gif' : (in_array($ext, ['mp4', 'webm']) ? 'video' : 'gambar');
    }
}
