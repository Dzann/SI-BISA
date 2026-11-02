@extends('layouts.app')
@section('bare','1')
@section('title','Login - SI-BISA')
@section('content')
<div style="max-width:380px;margin:12vh auto;padding:0 1rem;text-align:center">
  <div style="font-size:3em">✋</div>
  <h2>Selamat datang di SI-<span style="color:var(--pri)">BISA</span></h2>
  <div style="text-align:left">
    <label for="email">Email</label><input id="email" type="email" autocomplete="username">
    <label for="password">Kata Sandi</label><input id="password" type="password" autocomplete="current-password">
    <label style="font-weight:400"><input id="remember" type="checkbox" style="width:auto"> Ingat saya</label>
  </div>
  <div id="warn" class="warn"></div>
  <button id="masuk" class="btn" style="width:100%;margin-top:.8rem">MASUK</button>
  <p><a href="/">← Kembali</a></p>
</div>
@push('scripts')
<script>
async function login(){
  $('warn').textContent = '';
  try {
    const d = await api('/api/login', {method:'POST', json:{email:$('email').value, password:$('password').value, remember:$('remember').checked}});
    location.href = d.role === 'admin' ? '/admin' : '/app';
  } catch(e){ $('warn').textContent = e.data?.errors ? Object.values(e.data.errors).flat()[0] : e.message; }
}
$('masuk').onclick = login;
document.addEventListener('keydown', e => e.key === 'Enter' && login());
</script>
@endpush
@endsection
