@extends('layouts.app')
@section('title','Detail Riwayat - SI-BISA')
@section('content')
<a href="/riwayat">← Kembali ke Riwayat</a>
<h2>Detail Riwayat Terjemahan</h2>
<div id="tgl" style="color:var(--mut)"></div>
<h3>Teks yang dikenali</h3>
<div id="teks" class="card"></div>
<div id="info"></div>
<h3>Hasil Bahasa Isyarat</h3>
<div class="card">
  <div id="kata" style="font-weight:600;min-height:1.5em"></div>
  <div id="stage" class="stage">—</div>
  <div class="row" style="justify-content:center;margin-top:.8rem">
    <button id="prev" class="btn sec">‹</button><button id="play" class="btn sec">⏯</button>
    <span id="hitung">Gerakan 0 dari 0</span><button id="next" class="btn sec">›</button>
  </div>
</div>
<div id="warn" class="warn"></div>
<div class="card"><b>Informasi penerjemahan</b>
  <table><tr><td>Metode input</td><td>Web Speech API</td></tr><tr><td>Pemrosesan teks</td><td>Stemming Sastrawi</td></tr><tr><td>Bahasa isyarat</td><td>BISINDO</td></tr></table>
</div>
@push('scripts')
<script>
const P = Player($('stage'), $('kata'), $('hitung'));
$('prev').onclick = P.prev; $('next').onclick = () => { P.playing = false; P.next(); }; $('play').onclick = P.toggle;
(async () => {
  try {
    const d = await api('/api/riwayat/{{ (int) $id }}');
    $('tgl').textContent = fmtTgl(d.terjemahan.created_at);
    $('teks').textContent = '“' + d.terjemahan.teks_asli + '”';
    const hilang = d.detail.filter(x => !x.url_media).map(x => x.kata_dasar);
    if (hilang.length) $('warn').textContent = 'Kata yang tidak ditemukan: ' + hilang.join(', ');
    P.add(d.detail.filter(x => x.url_media));
  } catch(e){ $('teks').textContent = 'Riwayat tidak ditemukan.'; }
})();
</script>
@endpush
@endsection
