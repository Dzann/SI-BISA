<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Sastrawi\Stemmer\StemmerFactory;

class TranslateController extends Controller
{
    // POST /api/translate  {text, confidence?, session_id?}
    public function translate(Request $r)
    {
        $r->validate(['text' => 'required|string|max:1000', 'confidence' => 'nullable|numeric']);
        $start = microtime(true);

        $stemmer = (new StemmerFactory())->createStemmer();
        $clean   = mb_strtolower(preg_replace('/[^\p{L}\p{N}\s]/u', '', $r->text));
        $words   = preg_split('/\s+/', $clean, -1, PREG_SPLIT_NO_EMPTY);

        $items = [];
        foreach ($words as $i => $w) {
            $base  = $stemmer->stem($w);
            $kamus = DB::table('kamus_isyarat')->where('kata_dasar', $base)->where('is_active', 1)->first();
            $items[] = [
                'urutan' => $i + 1, 'kata_asli' => $w, 'kata_dasar' => $base,
                'found' => (bool) $kamus, 'isyarat_id' => $kamus->id ?? null,
                'tipe_media' => $kamus->tipe_media ?? null,
                'url_media' => $kamus ? asset('storage/' . $kamus->url_media) : null,
                'durasi_ms' => $kamus->durasi_ms ?? 2000,
            ];
        }
        $ms = (int) ((microtime(true) - $start) * 1000);

        $sesiId = $r->session_id; $terjId = null;
        if ($r->boolean('simpan', true)) { // pengaturan "Simpan hasil terjemahan"
            $sesiId = $sesiId ?: DB::table('sesi_terjemah')->insertGetId([
                'user_id' => optional($r->user())->id,
                'browser' => substr($r->userAgent() ?? '', 0, 255),
            ]);
            $terjId = DB::table('terjemahan')->insertGetId([
                'sesi_id' => $sesiId, 'teks_asli' => $r->text,
                'teks_stemming' => implode(' ', array_column($items, 'kata_dasar')),
                'confidence' => $r->confidence, 'waktu_proses_ms' => $ms,
            ]);
            if ($items) {
                DB::table('detail_kata')->insert(array_map(fn ($x) => [
                    'terjemahan_id' => $terjId, 'isyarat_id' => $x['isyarat_id'], 'urutan' => $x['urutan'],
                    'kata_asli' => $x['kata_asli'], 'kata_dasar' => $x['kata_dasar'], 'status_match' => $x['found'],
                ], $items));
            }
        }

        return response()->json([
            'session_id' => $sesiId, 'terjemahan_id' => $terjId, 'waktu_proses_ms' => $ms,
            'items' => $items,
            'tidak_ditemukan' => array_values(array_column(array_filter($items, fn ($x) => !$x['found']), 'kata_dasar')),
        ]);
    }

    // GET /api/riwayat?session_id=..&q=..
    public function riwayat(Request $r)
    {
        $q = DB::table('terjemahan')->orderByDesc('created_at')->limit(50);
        if ($r->session_id) $q->where('sesi_id', $r->session_id);
        if ($r->q) $q->where('teks_asli', 'like', '%' . $r->q . '%');
        return $q->get();
    }

    // GET /api/riwayat/{id}
    public function detail($id)
    {
        $t = DB::table('terjemahan')->find($id);
        abort_unless($t, 404);
        $d = DB::table('detail_kata as d')->leftJoin('kamus_isyarat as k', 'k.id', '=', 'd.isyarat_id')
            ->where('d.terjemahan_id', $id)->orderBy('d.urutan')
            ->get(['d.*', 'k.tipe_media', 'k.url_media', 'k.durasi_ms']);
        return ['terjemahan' => $t, 'detail' => $d];
    }
}
