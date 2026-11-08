@extends('layouts.app')
@section('bare','1')
@section('title','Login - SI-BISA')
@section('content')
<div style="min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 1.5rem;">
  <div class="card" style="width: 100%; max-width: 420px; padding: 2.5rem 2rem; border-radius: var(--radius-xl); box-shadow: var(--shadow-md); text-align: center;">
    
    <!-- Official Brand Logo -->
    <div style="margin: 0 auto 1.4rem; text-align: center;">
      <img src="{{ asset('logo/PACKAGES-04.png') }}" alt="SI-BISA" class="logo-light" style="height: 64px; width: auto; max-width: 220px; object-fit: contain;">
      <img src="{{ asset('logo/SI-BISA Black.png') }}?v=2" alt="SI-BISA" class="logo-dark" style="height: 64px; width: auto; max-width: 220px; object-fit: contain;">
    </div>

    <h2 style="font-size: 1.35em; font-weight: 700; color: var(--txt-main); margin-bottom: 1.8rem;">
      Selamat Datang
    </h2>

    <div style="text-align: left;">
      <!-- Input Email / Nama Pengguna -->
      <div style="position: relative; margin-bottom: 1.1rem;">
        <span style="position: absolute; left: 1.1rem; top: 50%; transform: translateY(-50%); color: var(--txt-muted); display:flex; align-items:center;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
        </span>
        <input id="email" type="email" placeholder="Nama Pengguna atau Email" class="input-pill" style="padding-left: 2.8rem;" autocomplete="username">
      </div>

      <!-- Input Kata Sandi -->
      <div style="position: relative; margin-bottom: 1.1rem;">
        <span style="position: absolute; left: 1.1rem; top: 50%; transform: translateY(-50%); color: var(--txt-muted); display:flex; align-items:center;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
        </span>
        <input id="password" type="password" placeholder="Kata Sandi" class="input-pill" style="padding-left: 2.8rem;" autocomplete="current-password">
      </div>

      <!-- Remember & Forgot Password Row -->
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; font-size: 0.9em;">
        <label style="display: inline-flex; align-items: center; gap: 0.5rem; margin: 0; cursor: pointer; font-weight: 500; color: var(--txt-sub);">
          <input id="remember" type="checkbox" style="width: auto; cursor: pointer;"> Ingat saya
        </label>
        <span style="color: var(--txt-muted); cursor: pointer;" onclick="alert('Silakan hubungi administrator sistem untuk mereset kata sandi Anda.')">
          Lupa Kata sandi?
        </span>
      </div>
    </div>

    <div id="warn" class="warn" style="margin-bottom: 1rem; text-align: center;"></div>

    <!-- Submit Button (Full Width Pill from Figma) -->
    <button id="masuk" class="btn" style="width: 100%; padding: 0.95rem; font-size: 1.05em; letter-spacing: 0.5px; border-radius: var(--radius-pill); margin-bottom: 1.2rem;">
      MASUK
    </button>

    <div style="display: flex; justify-content: center; gap: 0.8rem; margin-top: 1.2rem; flex-wrap: wrap;">
      <a href="/" class="btn sec sm" style="text-decoration: none; border-radius: var(--radius-pill); font-size: 0.85em;">
        🌐 Kembali ke Landing Page
      </a>
      <a href="/app" class="btn sec sm" style="text-decoration: none; border-radius: var(--radius-pill); font-size: 0.85em;">
        🏠 Buka Beranda Penerjemah
      </a>
    </div>
  </div>
</div>

@push('scripts')
<script>
async function login(){
  $('warn').textContent = '';
  const email = $('email').value.trim();
  const password = $('password').value;
  if (!email || !password) {
    $('warn').textContent = 'Silakan isi email dan kata sandi Anda.';
    return;
  }
  
  $('masuk').disabled = true;
  $('masuk').textContent = 'Memverifikasi...';

  try {
    const d = await api('/api/login', {
      method: 'POST',
      json: {
        email: email,
        password: password,
        remember: $('remember').checked
      }
    });
    location.href = d.role === 'admin' ? '/admin' : '/app';
  } catch(e) {
    $('warn').textContent = e.data?.message || (e.data?.errors ? Object.values(e.data.errors).flat()[0] : e.message);
  } finally {
    $('masuk').disabled = false;
    $('masuk').textContent = 'MASUK';
  }
}

$('masuk').onclick = login;
document.addEventListener('keydown', e => {
  if (e.key === 'Enter') login();
});
</script>
@endpush
@endsection
