@extends('layouts.app')
@section('title','Pengaturan - SI-BISA')
@section('content')
<h2>Pengaturan</h2>
<div class="card"><b>Bahasa</b>
  <label>Bahasa input suara</label><input value="Bahasa Indonesia ✓" disabled>
  <label>Bahasa isyarat</label><input value="BISINDO (Aktif)" disabled>
</div>
<div class="card"><b>Tampilan</b>
  <label>Tema aplikasi</label>
  <select id="theme"><option value="terang">Terang</option><option value="gelap">Gelap</option></select>
  <label>Ukuran teks</label>
  <select id="size"><option value="kecil">Kecil</option><option value="sedang">Sedang</option><option value="besar">Besar</option></select>
</div>
<div class="card"><b>Pemutaran Isyarat</b>
  <label>Kecepatan video</label>
  <select id="speed"><option value="0.5">0,5x</option><option value="0.75">0,75x</option><option value="1">1x</option></select>
  <label style="font-weight:400"><input type="checkbox" id="autoplay" style="width:auto"> Putar otomatis (urutan gerakan tanpa klik satu per satu)</label>
  <label style="font-weight:400"><input type="checkbox" id="repeat" style="width:auto"> Ulangi gerakan (ulangi urutan dari awal setelah selesai)</label>
</div>
<div class="card"><b>Riwayat dan Privasi</b>
  <label style="font-weight:400"><input type="checkbox" id="simpan" style="width:auto"> Simpan hasil terjemahan untuk dilihat kembali</label>
</div>
<button id="reset" class="btn sec">Kembalikan ke default</button>
@push('scripts')
<script>
const ids = ['theme','size','speed','autoplay','repeat','simpan'];
function isi(){ ids.forEach(k => { const el = $(k); typeof S[k] === 'boolean' ? el.checked = S[k] : el.value = S[k]; }); }
ids.forEach(k => $(k).onchange = () => {
  S[k] = typeof DEF[k] === 'boolean' ? $(k).checked : (k === 'speed' ? parseFloat($(k).value) : $(k).value);
  saveS(); applyS();
});
$('reset').onclick = () => { Object.assign(S, DEF); saveS(); applyS(); isi(); };
isi();
</script>
@endpush
@endsection
