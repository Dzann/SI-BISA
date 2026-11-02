@extends('layouts.app')
@section('title','Beranda - SI-BISA')
@section('content')
<h2>Penerjemah Suara ke Bahasa Isyarat</h2>
<p style="color:var(--mut)">Tekan tombol mikrofon dan mulai berbicara.</p>
<div class="card">
  <label style="margin-top:0">Bahasa input</label><input value="Bahasa Indonesia" disabled>
  <div class="row" style="margin-top:1rem">
    <button id="mulai" class="btn">▶ Mulai</button>
    <button id="stop" class="btn sec" disabled>⏹ Stop</button>
    <span id="status" style="color:var(--mut)">Siap mendengarkan ...</span>
  </div>
  <label>Teks yang dikenali</label>
  <div id="teks" class="card" style="margin:0;min-height:48px;color:var(--mut)">Hasil ucapan akan muncul di sini ...</div>
</div>

<h3>Hasil Terjemahan</h3>
<div class="card">
  <div id="kata" style="font-weight:600;min-height:1.5em"></div>
  <div id="stage" class="stage">—</div>
  <div class="row" style="justify-content:center;margin-top:.8rem">
    <button id="prev" class="btn sec">‹</button>
    <button id="play" class="btn sec">⏯</button>
    <span id="hitung">Gerakan 0 dari 0</span>
    <button id="next" class="btn sec">›</button>
  </div>
</div>
<div id="warn" class="warn"></div>

@push('scripts')
<script>
const SR = window.SpeechRecognition || window.webkitSpeechRecognition;
const P = Player($('stage'), $('kata'), $('hitung'));
let sessionId = null, rec = null, queue = Promise.resolve();
$('prev').onclick = P.prev; $('next').onclick = () => { P.playing = false; P.next(); }; $('play').onclick = P.toggle;

function setUI(on){ $('mulai').disabled = on; $('stop').disabled = !on; $('status').textContent = on ? 'Mendengarkan ...' : 'Siap mendengarkan ...'; }

if (!SR){ $('warn').textContent = 'Browser tidak mendukung Web Speech API. Gunakan Google Chrome.'; $('mulai').disabled = true; }
else {
  rec = new SR(); rec.lang = 'id-ID'; rec.continuous = true; rec.interimResults = true;
  rec.onresult = e => {
    let interim = '';
    for (let i = e.resultIndex; i < e.results.length; i++){
      const r = e.results[i];
      if (r.isFinal){ const t = r[0].transcript.trim(); $('teks').textContent = t; queue = queue.then(() => kirim(t, r[0].confidence)); }
      else interim += r[0].transcript;
    }
    if (interim) $('teks').textContent = interim + ' ...';
  };
  rec.onerror = e => {
    $('warn').textContent = ['not-allowed','service-not-allowed'].includes(e.error)
      ? 'Izin mikrofon ditolak. Aktifkan izin mikrofon pada browser.' : 'Error pengenalan suara: ' + e.error;
    setUI(false);
  };
  rec.onend = () => setUI(false);
  $('mulai').onclick = () => { $('warn').textContent = ''; rec.start(); setUI(true); };
  $('stop').onclick = () => rec.stop();
}

async function kirim(text, confidence){
  try {
    const d = await api('/api/translate', {method:'POST', json:{text, confidence, session_id:sessionId, simpan:S.simpan}});
    sessionId = d.session_id;
    $('warn').textContent = d.tidak_ditemukan.length ? 'Kata yang tidak ditemukan: ' + d.tidak_ditemukan.join(', ') : '';
    P.add(d.items.filter(i => i.found));
  } catch(e){ $('warn').textContent = 'Gagal memproses teks: ' + e.message; }
}
</script>
@endpush
@endsection
