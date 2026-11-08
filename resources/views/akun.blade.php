@extends('layouts.app')
@section('title','Akun - SI-BISA')
@section('content')

<!-- Header Akun Sesuai Figma -->
<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
  <div style="display: flex; align-items: center; gap: 0.8rem;">
    <a href="/app" style="width: 36px; height: 36px; border-radius: 50%; background: var(--bg-card); border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: center; color: var(--txt-main); text-decoration: none;" title="Kembali ke Beranda">
      ‹
    </a>
    <h2 style="font-size: 1.7em; font-weight: 700; color: var(--txt-main); margin: 0;">
      Akun
    </h2>
  </div>
  <a href="/" class="btn sec sm" style="border-radius: var(--radius-pill); font-size: 0.85em; text-decoration: none;">
    🌐 Landing Page
  </a>
</div>

<!-- Profil Card Sesuai Figma -->
<div class="card" id="profilCard" style="text-align: center; padding: 2.2rem 1.5rem; border-radius: var(--radius-xl); margin-bottom: 1.5rem;">
  <div style="width: 76px; height: 76px; border-radius: 50%; background: var(--bg-subtle); border: 2px solid var(--border-color); margin: 0 auto 0.8rem; display: flex; align-items: center; justify-content: center; font-size: 2.2rem;">
    👤
  </div>
  <b id="userName" style="font-size: 1.25em; color: var(--txt-main); display: block; margin-bottom: 0.2rem;">
    Memuat...
  </b>
  <span id="userRole" style="color: var(--txt-sub); font-size: 0.9em;">
    Memeriksa sesi login...
  </span>
</div>

<!-- List Navigasi Akun Sesuai Figma -->
<div style="display: flex; flex-direction: column; gap: 0.7rem;">
  <div class="item-card" style="border-radius: var(--radius-lg); cursor: pointer;" onclick="alert('Fitur pengubahan profil sedang dalam pengembangan.')">
    <div style="display: flex; align-items: center; gap: 1rem;">
      <div style="width: 42px; height: 42px; border-radius: 50%; background: var(--bg-subtle); display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
        👤
      </div>
      <div>
        <b style="color: var(--txt-main); display: block; font-size: 1em;">Profile Saya</b>
        <span style="color: var(--txt-sub); font-size: 0.85em;">Ubah nama, foto dan informasi akun.</span>
      </div>
    </div>
    <span style="color: var(--txt-muted); font-size: 1.3em;">›</span>
  </div>

  <a class="item-card" href="/pengaturan" style="border-radius: var(--radius-lg); text-decoration: none;">
    <div style="display: flex; align-items: center; gap: 1rem;">
      <div style="width: 42px; height: 42px; border-radius: 50%; background: var(--bg-subtle); display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
        ⚙️
      </div>
      <div>
        <b style="color: var(--txt-main); display: block; font-size: 1em;">Pengaturan</b>
        <span style="color: var(--txt-sub); font-size: 0.85em;">Bahasa, tampilan, pemutaran, riwayat dan privasi.</span>
      </div>
    </div>
    <span style="color: var(--txt-muted); font-size: 1.3em;">›</span>
  </a>

  <a class="item-card" id="adminLink" href="/admin" style="border-radius: var(--radius-lg); text-decoration: none; display: none;">
    <div style="display: flex; align-items: center; gap: 1rem;">
      <div style="width: 42px; height: 42px; border-radius: 50%; background: var(--primary-light); display: flex; align-items: center; justify-content: center; font-size: 1.2rem; color: var(--primary);">
        🛠️
      </div>
      <div>
        <b style="color: var(--txt-main); display: block; font-size: 1em;">Kelola Kamus Isyarat</b>
        <span style="color: var(--txt-sub); font-size: 0.85em;">CRUD video/foto kamus BISINDO (Khusus Admin).</span>
      </div>
    </div>
    <span style="color: var(--txt-muted); font-size: 1.3em;">›</span>
  </a>

  <div class="item-card" id="keluarBtn" style="border-radius: var(--radius-lg); cursor: pointer; display: none;">
    <div style="display: flex; align-items: center; gap: 1rem;">
      <div style="width: 42px; height: 42px; border-radius: 50%; background: #fef2f2; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; color: var(--danger);">
        🚪
      </div>
      <div>
        <b style="color: var(--danger); display: block; font-size: 1em;">Keluar</b>
        <span style="color: var(--txt-sub); font-size: 0.85em;">Keluar dari akun saat ini.</span>
      </div>
    </div>
    <span style="color: var(--txt-muted); font-size: 1.3em;">›</span>
  </div>
</div>

@push('scripts')
<script>
(async () => {
  const u = await api('/api/me');
  if (!u) {
    $('userName').textContent = 'Pengguna Tamu';
    $('userRole').innerHTML = 'Anda belum masuk ke akun. <br><br><a class="btn sm" href="/login">Masuk Akun Admin</a>';
    return;
  }

  $('userName').textContent = u.name;
  $('userRole').textContent = (u.email || '') + ' · ' + (u.role === 'admin' ? 'Administrator' : 'Pengguna SI-BISA');

  if (u.role === 'admin') {
    $('adminLink').style.display = 'flex';
  }

  $('keluarBtn').style.display = 'flex';
  $('keluarBtn').onclick = async () => {
    await api('/api/logout', {method:'POST'});
    location.href = '/';
  };
})();
</script>
@endpush
@endsection
