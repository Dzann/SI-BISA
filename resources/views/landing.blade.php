@extends('layouts.app')
@section('bare','1')
@section('title','SI-BISA - Penerjemah Suara ke Bahasa Isyarat')
@section('content')

<!-- Header Navigasi Landing Page -->
<header style="max-width: 1100px; margin: 0 auto; padding: 1.2rem 1.5rem; display: flex; align-items: center; justify-content: space-between;">
  <a class="brand-logo" href="/" style="margin-bottom:0; padding:0; display: inline-flex; align-items: center;">
    <img src="{{ asset('storage/Logo/PACKAGES-04.png') }}" alt="SI-BISA" style="height: 48px; width: auto; object-fit: contain;">
  </a>

  <div class="row" style="gap: 1.2rem; align-items: center;">
    <a href="#tentang" style="color:var(--txt-sub); font-weight:500; font-size: 0.95em">Tentang</a>
    <a href="#cara" style="color:var(--txt-sub); font-weight:500; font-size: 0.95em">Bantuan</a>
    @guest
      <a href="/login" class="btn sec sm" style="border-radius: var(--radius-pill); font-size: 0.9em; padding: 0.5rem 1.1rem; text-decoration: none;">
        🔐 Login Admin
      </a>
    @else
      <a href="{{ auth()->user()->role === 'admin' ? '/admin' : '/app' }}" class="btn sec sm" style="border-radius: var(--radius-pill); font-size: 0.9em; padding: 0.5rem 1.1rem; text-decoration: none;">
        {{ auth()->user()->role === 'admin' ? '🛠️ Panel Admin' : '👤 Akun Saya' }}
      </a>
    @endguest
    <a class="btn sm" href="/app" style="padding: 0.5rem 1.4rem;">
      Mulai Menerjemahkan
    </a>
  </div>
</header>

<!-- Main Container -->
<main style="max-width: 1040px; margin: 1rem auto 3rem; padding: 0 1.2rem;">
  <!-- Hero Card -->
  <section style="background: linear-gradient(135deg, #eff6ff 0%, #f0fdf4 100%); border: 1px solid #dbeafe; border-radius: var(--radius-xl); padding: 3.5rem 3rem; margin-bottom: 2.5rem; box-shadow: var(--shadow-sm);">
    <h1 style="font-size: 3.2rem; font-weight: 800; color: #1e293b; line-height: 1.1; margin-bottom: 0.6rem;">
      SI-<span style="color:var(--primary)">BISA</span>
    </h1>
    <p style="font-size: 1.2rem; font-weight: 600; color: #475569; margin-bottom: 1.2rem;">
      Penerjemah Suara ke Bahasa Isyarat
    </p>
    <p style="font-size: 1.05rem; color: #64748b; max-width: 620px; line-height: 1.6; margin-bottom: 2.2rem;">
      Ubah suara menjadi gerakan bahasa isyarat secara instan langsung dari perangkat Anda. Tanpa alat tambahan, mudah diakses siapa saja dan di mana saja.
    </p>
    <a class="btn" href="/app" style="padding: 0.9rem 2.2rem; font-size: 1.05em;">
      Mulai Menerjemahkan
    </a>
  </section>

  <!-- 3 Fitur Unggulan -->
  <section id="tentang" style="margin-bottom: 3.5rem;">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.2rem;">
      <div class="card" style="margin:0; padding: 1.4rem; display: flex; align-items: center; gap: 1rem;">
        <div style="width: 48px; height: 48px; border-radius: 50%; background: #eff6ff; display: flex; align-items: center; justify-content: center; font-size: 1.4em; flex-shrink: 0;">
          💻
        </div>
        <div>
          <b style="font-size: 1.05em; display: block; color: var(--txt-main);">Mudah Digunakan</b>
          <span style="color: var(--txt-sub); font-size: 0.9em;">di Perangkat Anda</span>
        </div>
      </div>

      <div class="card" style="margin:0; padding: 1.4rem; display: flex; align-items: center; gap: 1rem;">
        <div style="width: 48px; height: 48px; border-radius: 50%; background: #eff6ff; display: flex; align-items: center; justify-content: center; font-size: 1.4em; flex-shrink: 0;">
          🤟
        </div>
        <div>
          <b style="font-size: 1.05em; display: block; color: var(--txt-main);">Mendukung</b>
          <span style="color: var(--txt-sub); font-size: 0.9em;">Bahasa Isyarat BISINDO</span>
        </div>
      </div>

      <div class="card" style="margin:0; padding: 1.4rem; display: flex; align-items: center; gap: 1rem;">
        <div style="width: 48px; height: 48px; border-radius: 50%; background: #eff6ff; display: flex; align-items: center; justify-content: center; font-size: 1.4em; flex-shrink: 0;">
          👥
        </div>
        <div>
          <b style="font-size: 1.05em; display: block; color: var(--txt-main);">Untuk Masyarakat</b>
          <span style="color: var(--txt-sub); font-size: 0.9em;">yang Lebih Inklusif</span>
        </div>
      </div>
    </div>
  </section>

  <!-- Cara Menggunakan -->
  <section id="cara" style="margin-bottom: 3.5rem;">
    <h2 style="font-size: 1.6em; font-weight: 700; margin-bottom: 1.4rem;">Cara Menggunakan</h2>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.2rem;">
      <div class="card" style="margin:0; padding: 1.6rem;">
        <div style="width: 44px; height: 44px; border-radius: 50%; background: #f1f5f9; display: flex; align-items: center; justify-content: center; font-size: 1.3em; margin-bottom: 1rem;">
          🎤
        </div>
        <b style="display:block; margin-bottom: 0.4rem; color: var(--txt-main);">1. Mulai Berbicara / Ketik</b>
        <p style="color: var(--txt-sub); font-size: 0.95em; line-height: 1.5;">
          Tekan tombol mikrofon dan sampaikan kalimat lisan, atau gunakan tab input teks manual jika mikrofon nonaktif.
        </p>
      </div>

      <div class="card" style="margin:0; padding: 1.6rem;">
        <div style="width: 44px; height: 44px; border-radius: 50%; background: #f1f5f9; display: flex; align-items: center; justify-content: center; font-size: 1.3em; margin-bottom: 1rem;">
          🔤
        </div>
        <b style="display:block; margin-bottom: 0.4rem; color: var(--txt-main);">2. Konversi & Stemming</b>
        <p style="color: var(--txt-sub); font-size: 0.95em; line-height: 1.5;">
          Web Speech API mengubah suara jadi teks, lalu Algoritma Sastrawi menyederhanakan kata berimbuhan menjadi kata dasar.
        </p>
      </div>

      <div class="card" style="margin:0; padding: 1.6rem;">
        <div style="width: 44px; height: 44px; border-radius: 50%; background: #f1f5f9; display: flex; align-items: center; justify-content: center; font-size: 1.3em; margin-bottom: 1rem;">
          ✋
        </div>
        <b style="display:block; margin-bottom: 0.4rem; color: var(--txt-main);">3. Peragaan Isyarat BISINDO</b>
        <p style="color: var(--txt-sub); font-size: 0.95em; line-height: 1.5;">
          Sistem mencocokkan kata dasar dengan pustaka aset gerakan BISINDO dan memutarnya berurutan di layar.
        </p>
      </div>
    </div>
  </section>

  <!-- Banner Komunitas Inklusif Sesuai Mockup Figma -->
  <section class="card" style="background: var(--bg-subtle); text-align: center; padding: 2rem; border-radius: var(--radius-lg); margin-bottom: 2rem;">
    <div style="font-size: 2.2rem; margin-bottom: 0.5rem;">🤝</div>
    <b style="font-size: 1.2em; display:block; margin-bottom: 0.3rem;">Bersama wujudkan komunitas yang lebih inklusif.</b>
    <p style="color: var(--txt-sub); font-size: 0.95em; max-width: 500px; margin: 0 auto 1.2rem;">
      Membantu mempermudah interaksi komunikasi sehari-hari antara Teman Tuli dan masyarakat umum.
    </p>
    <a class="btn sm" href="/app">Buka Aplikasi SI-BISA</a>
  </section>

  <!-- Footer Catatan -->
  <footer style="text-align: center; color: var(--txt-muted); font-size: 0.85em; padding-top: 1rem;">
    <p style="margin-bottom: 0.4rem;">Gunakan Google Chrome atau Microsoft Edge untuk pengalaman pengenalan suara optimal.</p>
    <p><a href="/login" style="color: var(--txt-sub); font-weight: 600;">Login Admin</a></p>
  </footer>
</main>
@endsection
