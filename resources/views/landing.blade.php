@extends('layouts.app')
@section('bare','1')
@section('title','SI-BISA - Penerjemah Suara ke Bahasa Isyarat')
@section('content')
<div class="top">
  <a class="logo" href="/">✋ SI-<span>BISA</span></a>
  <div class="row"><a href="/">Beranda</a><a href="#tentang">Tentang</a><a href="#cara">Bantuan</a><a class="btn" href="/app">Mulai</a></div>
</div>
<section style="background:var(--card);padding:3rem 1.2rem">
  <div style="max-width:860px;margin:auto">
    <h1 style="font-size:2.6em;margin:0">SI-<span>BISA</span></h1>
    <p style="color:var(--mut);font-weight:600">Penerjemah Suara ke Bahasa Isyarat</p>
    <p>Ubah suara menjadi gerakan bahasa isyarat secara instan langsung dari perangkat Anda. Tanpa alat tambahan, mudah diakses siapa saja dan di mana saja.</p>
    <a class="btn" href="/app">Mulai Menerjemahkan</a>
  </div>
</section>
<section id="tentang" style="max-width:860px;margin:2rem auto;padding:0 1.2rem">
  <div class="row" style="align-items:stretch">
    <div class="card" style="flex:1"><b>💻 Mudah Digunakan</b><br><small>di perangkat Anda</small></div>
    <div class="card" style="flex:1"><b>🤟 Mendukung</b><br><small>Bahasa Isyarat BISINDO</small></div>
    <div class="card" style="flex:1"><b>👥 Untuk Masyarakat</b><br><small>yang lebih inklusif</small></div>
  </div>
  <h2 id="cara">Cara Menggunakan</h2>
  <div class="row" style="align-items:stretch">
    <div class="card" style="flex:1">🎤 1. Tekan tombol mikrofon untuk mulai berbicara</div>
    <div class="card" style="flex:1">🔤 2. Sistem akan mengubah suara menjadi teks</div>
    <div class="card" style="flex:1">✋ 3. Teks diproses dan ditampilkan dalam bentuk isyarat</div>
  </div>
  <p><small>Gunakan Google Chrome dan koneksi internet agar pengenalan suara berfungsi.</small></p>
  <p><a href="/login">Login Admin</a></p>
</section>
@endsection
