@extends('layouts.app')
@section('title','Pengaturan - SI-BISA')
@section('content')

<!-- Header Pengaturan Sesuai Figma -->
<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
  <div style="display: flex; align-items: center; gap: 0.8rem;">
    <a href="/app" style="width: 36px; height: 36px; border-radius: 50%; background: var(--bg-card); border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: center; color: var(--txt-main); text-decoration: none;">
      ‹
    </a>
    <h2 style="font-size: 1.7em; font-weight: 700; color: var(--txt-main); margin: 0;">
      Pengaturan
    </h2>
  </div>
  <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--bg-subtle); display: flex; align-items: center; justify-content: center; color: var(--txt-sub);">
    ⚙️
  </div>
</div>

<!-- Card 1: Bahasa Sesuai Figma -->
<div class="card" style="padding: 1.6rem; border-radius: var(--radius-xl);">
  <b style="font-size: 1.1em; color: var(--txt-main); display: block; margin-bottom: 1rem;">Bahasa</b>
  
  <div style="margin-bottom: 1rem;">
    <label style="margin-top:0">Bahasa input suara</label>
    <div style="position: relative;">
      <input type="text" value="Bahasa Indonesia" class="input-pill" disabled style="padding-right: 2.5rem; font-weight: 600; background: var(--bg-subtle);">
      <span style="position: absolute; right: 1.1rem; top: 50%; transform: translateY(-50%); color: var(--success); font-weight: bold;">✓</span>
    </div>
  </div>

  <div>
    <label>Bahasa isyarat</label>
    <div style="position: relative;">
      <input type="text" value="BISINDO" class="input-pill" disabled style="font-weight: 600; background: var(--bg-subtle);">
      <span style="position: absolute; right: 1.1rem; top: 50%; transform: translateY(-50%); font-size: 0.8em; background: var(--primary-light); color: var(--primary); padding: 0.2rem 0.6rem; border-radius: var(--radius-pill); font-weight: 700;">AKTIF</span>
    </div>
  </div>
</div>

<!-- Card 2: Tampilan Sesuai Figma -->
<div class="card" style="padding: 1.6rem; border-radius: var(--radius-xl);">
  <b style="font-size: 1.1em; color: var(--txt-main); display: block; margin-bottom: 1rem;">Tampilan</b>
  
  <div style="margin-bottom: 1.2rem;">
    <label style="margin-top:0">Tema aplikasi</label>
    <div class="row" style="gap: 1.5rem; margin-top: 0.4rem;">
      <label style="display:inline-flex; align-items:center; gap:0.4rem; cursor:pointer; margin:0; font-weight:500;">
        <input type="radio" name="theme" value="terang" style="width:auto"> Terang
      </label>
      <label style="display:inline-flex; align-items:center; gap:0.4rem; cursor:pointer; margin:0; font-weight:500;">
        <input type="radio" name="theme" value="gelap" style="width:auto"> Gelap
      </label>
    </div>
  </div>

  <div>
    <label>Ukuran teks</label>
    <div class="row" style="gap: 1.5rem; margin-top: 0.4rem;">
      <label style="display:inline-flex; align-items:center; gap:0.4rem; cursor:pointer; margin:0; font-weight:500;">
        <input type="radio" name="size" value="kecil" style="width:auto"> Kecil
      </label>
      <label style="display:inline-flex; align-items:center; gap:0.4rem; cursor:pointer; margin:0; font-weight:500;">
        <input type="radio" name="size" value="sedang" style="width:auto"> Sedang
      </label>
      <label style="display:inline-flex; align-items:center; gap:0.4rem; cursor:pointer; margin:0; font-weight:500;">
        <input type="radio" name="size" value="besar" style="width:auto"> Besar
      </label>
    </div>
  </div>
</div>

<!-- Card 3: Pemutaran Isyarat Sesuai Figma -->
<div class="card" style="padding: 1.6rem; border-radius: var(--radius-xl);">
  <b style="font-size: 1.1em; color: var(--txt-main); display: block; margin-bottom: 1rem;">Pemutaran Isyarat</b>
  
  <div style="margin-bottom: 1.2rem;">
    <label style="margin-top:0">Kecepatan video</label>
    <div class="row" style="gap: 1.5rem; margin-top: 0.4rem;">
      <label style="display:inline-flex; align-items:center; gap:0.4rem; cursor:pointer; margin:0; font-weight:500;">
        <input type="radio" name="speed" value="0.5" style="width:auto"> 0,5x
      </label>
      <label style="display:inline-flex; align-items:center; gap:0.4rem; cursor:pointer; margin:0; font-weight:500;">
        <input type="radio" name="speed" value="0.75" style="width:auto"> 0,75x
      </label>
      <label style="display:inline-flex; align-items:center; gap:0.4rem; cursor:pointer; margin:0; font-weight:500;">
        <input type="radio" name="speed" value="1" style="width:auto"> 1x
      </label>
    </div>
  </div>

  <div style="margin-bottom: 0.8rem;">
    <label style="display:flex; align-items:flex-start; gap:0.6rem; cursor:pointer; margin:0; font-weight:500;">
      <input type="checkbox" id="autoplay" style="width:auto; margin-top: 0.25rem;">
      <div>
        <b style="color:var(--txt-main); font-size:0.95em;">Putar otomatis</b>
        <span style="display:block; color:var(--txt-sub); font-size:0.85em;">Putar urutan gerakan tanpa klik satu per satu.</span>
      </div>
    </label>
  </div>

  <div>
    <label style="display:flex; align-items:flex-start; gap:0.6rem; cursor:pointer; margin:0; font-weight:500;">
      <input type="checkbox" id="repeat" style="width:auto; margin-top: 0.25rem;">
      <div>
        <b style="color:var(--txt-main); font-size:0.95em;">Ulangi gerakan</b>
        <span style="display:block; color:var(--txt-sub); font-size:0.85em;">Ulangi urutan gerakan dari awal setelah selesai.</span>
      </div>
    </label>
  </div>
</div>

<!-- Card 4: Riwayat dan Privasi Sesuai Figma -->
<div class="card" style="padding: 1.6rem; border-radius: var(--radius-xl);">
  <b style="font-size: 1.1em; color: var(--txt-main); display: block; margin-bottom: 0.8rem;">Riwayat dan Privasi</b>
  <label style="display:flex; align-items:flex-start; gap:0.6rem; cursor:pointer; margin:0; font-weight:500;">
    <input type="checkbox" id="simpan" style="width:auto; margin-top: 0.25rem;">
    <div>
      <b style="color:var(--txt-main); font-size:0.95em;">Simpan hasil terjemahan</b>
      <span style="display:block; color:var(--txt-sub); font-size:0.85em;">Simpan hasil percakapan terjemahan untuk dilihat kembali pada menu Riwayat.</span>
    </div>
  </label>
</div>

<div style="margin-top: 1.5rem;">
  <button id="reset" class="btn sec" style="border-radius: var(--radius-pill);">
    Kembalikan ke Default
  </button>
</div>

@push('scripts')
<script>
function syncUI(){
  // Radio Theme
  document.querySelectorAll('input[name="theme"]').forEach(r => {
    r.checked = (r.value === S.theme);
    r.onchange = () => { S.theme = r.value; saveS(); applyS(); };
  });

  // Radio Size
  document.querySelectorAll('input[name="size"]').forEach(r => {
    r.checked = (r.value === S.size);
    r.onchange = () => { S.size = r.value; saveS(); applyS(); };
  });

  // Radio Speed
  document.querySelectorAll('input[name="speed"]').forEach(r => {
    r.checked = (parseFloat(r.value) === S.speed);
    r.onchange = () => { S.speed = parseFloat(r.value); saveS(); };
  });

  // Checkboxes
  $('autoplay').checked = S.autoplay;
  $('autoplay').onchange = () => { S.autoplay = $('autoplay').checked; saveS(); };

  $('repeat').checked = S.repeat;
  $('repeat').onchange = () => { S.repeat = $('repeat').checked; saveS(); };

  $('simpan').checked = S.simpan;
  $('simpan').onchange = () => { S.simpan = $('simpan').checked; saveS(); };
}

$('reset').onclick = () => {
  Object.assign(S, DEF);
  saveS();
  applyS();
  syncUI();
};

syncUI();
</script>
@endpush
@endsection
