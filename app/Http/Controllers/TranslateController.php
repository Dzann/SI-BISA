<?php

namespace App\Http\Controllers;

use App\Models\DetailKata;
use App\Models\KamusIsyarat;
use App\Models\SesiTerjemah;
use App\Models\Terjemahan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Sastrawi\Stemmer\StemmerInterface;

class TranslateController extends Controller
{
    protected StemmerInterface $stemmer;

    public function __construct(StemmerInterface $stemmer)
    {
        $this->stemmer = $stemmer;
    }

    /**
     * POST /api/translate
     * Parameter: text, confidence?, session_id?, simpan?
     */
    public function translate(Request $r): JsonResponse
    {
        $r->validate([
            'text' => 'required|string|max:1000',
            'confidence' => 'nullable|numeric',
            'session_id' => 'nullable|integer',
            'simpan' => 'nullable|boolean',
        ]);

        $start = microtime(true);

        // Preprocessing teks: hapus karakter non-alfanumerik dan ubah ke huruf kecil
        $clean = mb_strtolower(preg_replace('/[^\p{L}\p{N}\s]/u', '', $r->text));
        $words = preg_split('/\s+/', $clean, -1, PREG_SPLIT_NO_EMPTY);

        // 1. Stemming seluruh kata menggunakan Sastrawi Singleton
        $stemmedMap = [];
        foreach ($words as $w) {
            if (!isset($stemmedMap[$w])) {
                $stemmedMap[$w] = $this->stemmer->stem($w);
            }
        }

        // 2. Query batch ke database (hindari N+1 query)
        $uniqueBases = array_values(array_unique($stemmedMap));
        $kamusMap = KamusIsyarat::whereIn('kata_dasar', $uniqueBases)
            ->where('is_active', true)
            ->get()
            ->keyBy('kata_dasar');

        // 3. Susun data hasil terjemahan
        $items = [];
        foreach ($words as $i => $w) {
            $base = $stemmedMap[$w];
            $kamus = $kamusMap->get($base);

            $items[] = [
                'urutan' => $i + 1,
                'kata_asli' => $w,
                'kata_dasar' => $base,
                'found' => (bool) $kamus,
                'isyarat_id' => $kamus?->id,
                'tipe_media' => $kamus?->tipe_media,
                'url_media' => $kamus ? asset('storage/' . $kamus->url_media) : null,
                'durasi_ms' => $kamus?->durasi_ms ?? 2000,
            ];
        }

        $ms = (int) ((microtime(true) - $start) * 1000);

        // 4. Simpan ke database jika opsi simpan aktif
        $sesiId = $r->session_id;
        $terjId = null;

        if ($r->boolean('simpan', true)) {
            $sesi = null;
            if ($sesiId) {
                $sesi = SesiTerjemah::find($sesiId);
            }

            if (!$sesi) {
                $sesi = SesiTerjemah::create([
                    'user_id' => $r->user()?->id,
                    'browser' => substr($r->userAgent() ?? '', 0, 255),
                    'mulai' => now(),
                ]);
                $sesiId = $sesi->id;
            }

            $terjemahan = Terjemahan::create([
                'sesi_id' => $sesiId,
                'teks_asli' => $r->text,
                'teks_stemming' => implode(' ', array_column($items, 'kata_dasar')),
                'confidence' => $r->confidence,
                'waktu_proses_ms' => $ms,
                'created_at' => now(),
            ]);
            $terjId = $terjemahan->id;

            if (!empty($items)) {
                $details = array_map(fn ($x) => [
                    'terjemahan_id' => $terjId,
                    'isyarat_id' => $x['isyarat_id'],
                    'urutan' => $x['urutan'],
                    'kata_asli' => $x['kata_asli'],
                    'kata_dasar' => $x['kata_dasar'],
                    'status_match' => $x['found'],
                ], $items);

                DetailKata::insert($details);
            }
        }

        $tidakDitemukan = array_values(array_column(array_filter($items, fn ($x) => !$x['found']), 'kata_dasar'));

        return response()->json([
            'session_id' => $sesiId,
            'terjemahan_id' => $terjId,
            'waktu_proses_ms' => $ms,
            'items' => $items,
            'tidak_ditemukan' => $tidakDitemukan,
        ]);
    }

    /**
     * GET /api/riwayat?session_id=..&q=..
     */
    public function riwayat(Request $r): JsonResponse
    {
        $query = Terjemahan::query()->orderByDesc('created_at')->limit(50);

        if ($r->user()) {
            $query->whereHas('sesi', function ($q) use ($r) {
                $q->where('user_id', $r->user()->id);
                if ($r->filled('session_id')) {
                    $q->where('id', $r->session_id);
                }
            });
        } elseif ($r->filled('session_id')) {
            $query->where('sesi_id', $r->session_id);
        } else {
            // Anonim tanpa session_id: lindungi privasi, jangan tampilkan data global
            return response()->json([]);
        }

        if ($r->filled('q')) {
            $query->where('teks_asli', 'like', '%' . $r->q . '%');
        }

        return response()->json($query->get());
    }

    /**
     * GET /api/riwayat/{id}
     */
    public function detail(Request $r, $id): JsonResponse
    {
        $terjemahan = Terjemahan::with('sesi')->find($id);
        abort_unless($terjemahan, 404, 'Riwayat tidak ditemukan');

        // Proteksi IDOR jika bukan admin
        if ($r->user()?->role !== 'admin') {
            if ($r->user()) {
                abort_if($terjemahan->sesi?->user_id !== $r->user()->id, 403, 'Akses ditolak');
            } elseif ($r->filled('session_id')) {
                abort_if((int) $terjemahan->sesi_id !== (int) $r->session_id, 403, 'Akses ditolak');
            }
        }

        $detail = DetailKata::query()
            ->leftJoin('kamus_isyarat as k', 'k.id', '=', 'detail_kata.isyarat_id')
            ->where('detail_kata.terjemahan_id', $id)
            ->orderBy('detail_kata.urutan')
            ->get([
                'detail_kata.*',
                'k.tipe_media',
                'k.url_media',
                'k.durasi_ms',
            ]);

        $detail->transform(function ($item) {
            if ($item->url_media) {
                $item->url_media = asset('storage/' . $item->url_media);
            }
            return $item;
        });

        return response()->json([
            'terjemahan' => $terjemahan,
            'detail' => $detail,
        ]);
    }
}
