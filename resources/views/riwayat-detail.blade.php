@extends('layouts.app')
@section('title','Detail Riwayat - SI-BISA')
@section('content')

<!-- Back Link Button from Figma -->
<div style="margin-bottom: 1.2rem;">
  <a href="/riwayat" class="row" style="color: var(--txt-sub); font-weight: 600; text-decoration: none; display: inline-flex; gap: 0.5rem; align-items: center;">
    <span style="width: 32px; height: 32px; border-radius: 50%; background: var(--bg-subtle); border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: center;">
      ‹
    </span>
    <span>Kembali ke Riwayat</span>
  </a>
</div>

<!-- Header Detail -->
<div style="margin-bottom: 1.5rem;">
  <h2 style="font-size: 1.7em; font-weight: 700; color: var(--txt-main); margin-bottom: 0.25rem;">
    Detail Riwayat Terjemahan
  </h2>
  <div id="tgl" style="color: var(--txt-muted); font-size: 0.9em;">Memuat data...</div>
</div>

<!-- Teks yang Dikenali Card -->
<div class="card" style="padding: 1.6rem; border-radius: var(--radius-xl);">
  <label style="margin-top:0; color: var(--txt-sub);">Teks yang Dikenali</label>
  <div id="teks" style="font-size: 1.2em; font-weight: 700; color: var(--txt-main); margin-bottom: 0.6rem;">
    Memuat kalimat...
  </div>
  <div style="display: inline-block; font-size: 0.8em; background: var(--bg-subtle); color: var(--txt-sub); padding: 0.25rem 0.8rem; border-radius: var(--radius-pill); font-weight: 600; border: 1px solid var(--border-color);">
    Bahasa input: Bahasa Indonesia
  </div>
</div>

<!-- Hasil Bahasa Isyarat BISINDO -->
<div style="margin-top: 1.8rem;">
  <h3 style="font-size: 1.3em; font-weight: 700; color: var(--txt-main); margin-bottom: 0.8rem;">
    Hasil Bahasa Isyarat
  </h3>

  <div class="card" style="padding: 1.6rem; border-radius: var(--radius-xl);">
    <div id="kata" style="font-weight: 700; min-height: 1.6em; text-align: center; color: var(--primary); font-size: 1.1em; margin-bottom: 0.5rem;"></div>

    <!-- Stage Visual BISINDO -->
    <div id="stage" class="stage" style="border-radius: var(--radius-lg);">
      <div style="color: var(--txt-muted); text-align: center; padding: 2rem;">
        <div style="font-size: 2.2rem; margin-bottom: 0.5rem;">✋</div>
        Memuat video / animasi peragaan isyarat BISINDO...
      </div>
    </div>

    <!-- Sequential Controls from Figma -->
    <div style="background: var(--bg-subtle); border: 1px solid var(--border-color); border-radius: var(--radius-pill); padding: 0.6rem 1.2rem; margin-top: 1rem; display: flex; align-items: center; justify-content: space-between; gap: 0.8rem;">
      <button id="play" class="btn sm sec" style="width: 36px; height: 36px; padding: 0; border-radius: 50%;" title="Putar / Jeda">⏯</button>

      <div class="row" style="gap: 0.6rem;">
        <button id="prev" class="btn sm sec" style="width: 36px; height: 36px; padding: 0; border-radius: 50%;" title="Sebelumnya">‹</button>
        <span id="hitung" style="font-size: 0.9em; font-weight: 600; color: var(--txt-main);">Gerakan 0 dari 0</span>
        <button id="next" class="btn sm sec" style="width: 36px; height: 36px; padding: 0; border-radius: 50%;" title="Selanjutnya">›</button>
      </div>
    </div>

    <div id="warn" class="warn" style="margin-top: 0.8rem; text-align: center;"></div>
  </div>
</div>

<!-- Informasi Penerjemahan Table Card -->
<div class="card" style="padding: 1.6rem; border-radius: var(--radius-xl); margin-top: 1.5rem;">
  <b style="font-size: 1.05em; color: var(--txt-main); display: block; margin-bottom: 0.8rem;">Informasi Penerjemahan</b>
  <table>
    <tr>
      <td style="color: var(--txt-sub); width: 40%;">Metode input</td>
      <td style="font-weight: 600; color: var(--txt-main);">Web Speech API / Teks Manual</td>
    </tr>
    <tr>
      <td style="color: var(--txt-sub);">Pemrosesan teks</td>
      <td style="font-weight: 600; color: var(--txt-main);">Algoritma Stemming Sastrawi</td>
    </tr>
    <tr>
      <td style="color: var(--txt-sub);">Bahasa isyarat</td>
      <td style="font-weight: 600; color: var(--txt-main);">BISINDO (Bahasa Isyarat Indonesia)</td>
    </tr>
  </table>
</div>

@push('scripts')
<script>
const P = Player($('stage'), $('kata'), $('hitung'));
$('prev').onclick = P.prev;
$('next').onclick = () => { P.playing = false; P.next(); };
$('play').onclick = P.toggle;

(async () => {
  try {
    const sessionId = localStorage.getItem('sibisa_session_id');
    const url = '/api/riwayat/{{ (int) $id }}' + (sessionId ? '?session_id=' + encodeURIComponent(sessionId) : '');
    const d = await api(url);

    $('tgl').textContent = fmtTgl(d.terjemahan.created_at);
    $('teks').textContent = '“' + d.terjemahan.teks_asli + '”';

    const hilang = (d.detail || []).filter(x => !x.url_media).map(x => x.kata_dasar);
    if (hilang.length) {
      $('warn').textContent = 'Kata yang belum tersedia di kamus: ' + hilang.join(', ');
    }

    const items = (d.detail || []).filter(x => x.url_media);
    if (items.length) {
      P.add(items);
    } else {
      $('stage').innerHTML = '<div style="color:var(--txt-muted); padding:2rem;">Semua kata belum memiliki rekaman isyarat</div>';
    }
  } catch(e) {
    $('teks').textContent = 'Riwayat tidak ditemukan atau akses ditolak.';
    $('warn').textContent = e.message;
  }
})();
</script>
@endpush
@endsection
