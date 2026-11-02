@extends('layouts.app')
@section('bare','1')
@section('title','Dashboard Admin - SI-BISA')
@section('content')
<div class="top">
  <a class="logo" href="/">✋ SI-<span>BISA</span> <small style="font-weight:400">Admin</small></a>
  <div class="row"><a href="/app">Beranda</a><button id="keluar" class="btn sec">Keluar</button></div>
</div>
<div style="max-width:960px;margin:1rem auto;padding:0 1rem">
  <h2>Kelola Kamus Isyarat BISINDO</h2>
  <div class="card">
    <b id="judulForm">Tambah Isyarat</b>
    <input type="hidden" id="editId">
    <div class="row" style="align-items:flex-end">
      <div style="flex:1;min-width:160px"><label>Kata dasar</label><input id="kata" placeholder="mis. makan"></div>
      <div style="width:130px"><label>Durasi (ms)</label><input id="durasi" type="number" value="2000"></div>
      <div style="flex:1;min-width:200px"><label>File (GIF/PNG/JPG/MP4/WEBM, maks 10MB)</label><input id="media" type="file" accept=".gif,.png,.jpg,.jpeg,.mp4,.webm"></div>
    </div>
    <div class="row" style="margin-top:.8rem">
      <button id="simpan" class="btn">Simpan</button><button id="batal" class="btn sec" style="display:none">Batal</button>
    </div>
    <div id="msg" class="warn"></div>
  </div>
  <div class="card" style="overflow:auto">
    <table><thead><tr><th>Pratinjau</th><th>Kata dasar</th><th>Tipe</th><th>Durasi</th><th>Aktif</th><th></th></tr></thead><tbody id="tbody"></tbody></table>
  </div>
</div>
@push('scripts')
<script>
let rows = [];
const say = (t, ok) => { $('msg').className = ok ? 'ok' : 'warn'; $('msg').textContent = t; };
const prev = r => r.tipe_media === 'video' ? `<video src="/storage/${esc(r.url_media)}" width="80" muted></video>` : `<img src="/storage/${esc(r.url_media)}" width="80">`;

async function muat(){
  rows = await api('/api/kamus');
  $('tbody').innerHTML = rows.length ? rows.map(r => `<tr>
    <td>${prev(r)}</td><td><b>${esc(r.kata_dasar)}</b></td><td>${r.tipe_media}</td><td>${r.durasi_ms} ms</td>
    <td><input type="checkbox" ${r.is_active ? 'checked' : ''} style="width:auto" onchange="toggle(${r.id}, this.checked)"></td>
    <td class="row"><button class="btn sec" onclick="edit(${r.id})">Ubah</button><button class="btn red" onclick="hapus(${r.id})">Hapus</button></td></tr>`).join('')
    : '<tr><td colspan="6">Belum ada data.</td></tr>';
}
function reset(){ $('editId').value=''; $('kata').value=''; $('durasi').value=2000; $('media').value=''; $('judulForm').textContent='Tambah Isyarat'; $('batal').style.display='none'; }
function edit(id){ const r = rows.find(x => x.id === id); $('editId').value = id; $('kata').value = r.kata_dasar; $('durasi').value = r.durasi_ms;
  $('judulForm').textContent = 'Ubah Isyarat (file kosong = tidak diganti)'; $('batal').style.display = 'inline-block'; scrollTo(0,0); }
$('batal').onclick = reset;

$('simpan').onclick = async () => {
  const id = $('editId').value, fd = new FormData();
  fd.append('kata_dasar', $('kata').value); fd.append('durasi_ms', $('durasi').value);
  if ($('media').files[0]) fd.append('media', $('media').files[0]);
  if (id) fd.append('_method', 'PUT');
  try {
    const d = await api(id ? '/api/kamus/' + id : '/api/kamus', {method:'POST', body:fd});
    say(d.message, true); reset(); muat();
  } catch(e){ say(e.data?.errors ? Object.values(e.data.errors).flat().join(' ') : e.message); } // format file tidak sesuai, dll.
};
async function hapus(id){ if (!confirm('Hapus isyarat ini?')) return; await api('/api/kamus/' + id, {method:'DELETE'}); muat(); }
async function toggle(id, v){ const fd = new FormData(); fd.append('_method','PUT'); fd.append('is_active', v ? 1 : 0); await api('/api/kamus/' + id, {method:'POST', body:fd}); }
$('keluar').onclick = async () => { await api('/api/logout', {method:'POST'}); location.href = '/login'; };
muat();
</script>
@endpush
@endsection
