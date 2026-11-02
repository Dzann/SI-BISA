@extends('layouts.app')
@section('title','Riwayat - SI-BISA')
@section('content')
<h2>Riwayat Terjemahan</h2>
<input id="q" placeholder="🔍 Cari kata atau tanggal ...">
<div id="list"></div>
@push('scripts')
<script>
let timer;
async function muat(){
  const q = $('q').value.trim();
  const rows = await api('/api/riwayat' + (q ? '?q=' + encodeURIComponent(q) : ''));
  $('list').innerHTML = rows.length ? rows.map(x => `
    <div class="item"><div><b>${esc(x.teks_asli)}</b><br><small style="color:var(--mut)">${fmtTgl(x.created_at)}</small></div>
    <a class="btn sec" href="/riwayat/${x.id}">Lihat</a></div>`).join('')
    : '<div class="card">Riwayat masih kosong.</div>';
}
$('q').oninput = () => { clearTimeout(timer); timer = setTimeout(muat, 300); };
muat();
</script>
@endpush
@endsection
