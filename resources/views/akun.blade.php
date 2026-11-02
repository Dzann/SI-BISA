@extends('layouts.app')
@section('title','Akun - SI-BISA')
@section('content')
<h2>Akun</h2>
<div class="card" id="profil">Memuat ...</div>
<a class="item" href="/pengaturan"><span>⚙️ Pengaturan<br><small style="color:var(--mut)">Bahasa, tampilan, pemutaran, riwayat dan privasi.</small></span>›</a>
<a class="item" id="adminLink" href="/admin" style="display:none"><span>🛠 Kelola Kamus Isyarat<br><small style="color:var(--mut)">CRUD video/foto BISINDO.</small></span>›</a>
<div class="item" id="keluar" style="cursor:pointer;display:none"><span>🚪 Keluar<br><small style="color:var(--mut)">Keluar dari akun.</small></span>›</div>
@push('scripts')
<script>
(async () => {
  const u = await api('/api/me');
  if (!u){ $('profil').innerHTML = '<b>Pengguna SI-BISA</b><br><small>Anda belum login.</small><br><br><a class="btn" href="/login">Login</a>'; return; }
  $('profil').innerHTML = `<b>${esc(u.name)}</b><br><small>${esc(u.email)} · ${u.role === 'admin' ? 'Admin' : 'Pengguna SI-BISA'}</small>`;
  if (u.role === 'admin') $('adminLink').style.display = 'flex';
  $('keluar').style.display = 'flex';
  $('keluar').onclick = async () => { await api('/api/logout', {method:'POST'}); location.href = '/'; };
})();
</script>
@endpush
@endsection
