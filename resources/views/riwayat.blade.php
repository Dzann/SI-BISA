@extends('layouts.app')
@section('title','Riwayat - SI-BISA')
@section('content')

<!-- Header Riwayat Sesuai Figma -->
<div style="margin-bottom: 1.5rem;">
  <h2 style="font-size: 1.7em; font-weight: 700; color: var(--txt-main); margin-bottom: 0.25rem;">
    Riwayat Terjemahan
  </h2>
  <p style="color: var(--txt-sub); font-size: 0.95em;">
    Daftar rekaman konversi suara dan teks ke bahasa isyarat BISINDO.
  </p>
</div>

<!-- Search Input Capsule Sesuai Figma -->
<div class="card" style="padding: 1.2rem; border-radius: var(--radius-xl); margin-bottom: 1.5rem;">
  <div style="position: relative;">
    <span style="position: absolute; left: 1.2rem; top: 50%; transform: translateY(-50%); color: var(--txt-muted); display:flex; align-items:center;">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
    </span>
    <input id="q" type="text" placeholder="Cari kata atau tanggal..." class="input-pill" style="padding-left: 2.9rem;">
  </div>
</div>

<!-- Container Daftar Riwayat -->
<div id="list">
  <div class="card" style="text-align: center; color: var(--txt-muted); padding: 2.5rem;">
    Memuat riwayat...
  </div>
</div>

@push('scripts')
<script>
let timer;
const sessionId = localStorage.getItem('sibisa_session_id');

async function muat(){
  const q = $('q').value.trim();
  const params = new URLSearchParams();
  if (q) params.set('q', q);
  if (sessionId) params.set('session_id', sessionId);

  try {
    const rows = await api('/api/riwayat?' + params.toString());
    
    if (!rows.length) {
      $('list').innerHTML = `
        <div class="card" style="text-align: center; padding: 3rem 1.5rem; border-radius: var(--radius-xl);">
          <div style="font-size: 2.5rem; margin-bottom: 0.6rem;">🕘</div>
          <b style="font-size: 1.1em; color: var(--txt-main); display: block; margin-bottom: 0.3rem;">Riwayat masih kosong</b>
          <p style="color: var(--txt-sub); font-size: 0.95em; margin-bottom: 1.2rem;">Belum ada hasil terjemahan yang disimpan untuk sesi ini.</p>
          <a class="btn sm" href="/app">Mulai Terjemahkan di Beranda</a>
        </div>
      `;
      return;
    }

    $('list').innerHTML = rows.map(x => `
      <div class="item-card" style="border-radius: var(--radius-lg); padding: 1.1rem 1.4rem;">
        <div style="display: flex; align-items: center; gap: 1rem;">
          <!-- Document Icon Container from Figma -->
          <div style="width: 44px; height: 44px; border-radius: 50%; background: var(--bg-subtle); border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: center; color: var(--primary); flex-shrink: 0;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
          </div>
          <div>
            <b style="font-size: 1.05em; color: var(--txt-main); display: block; margin-bottom: 0.2rem;">
              ${esc(x.teks_asli)}
            </b>
            <span style="color: var(--txt-muted); font-size: 0.85em;">
              ${fmtTgl(x.created_at)}
            </span>
          </div>
        </div>
        <!-- Pill Button Lihat from Figma -->
        <a class="btn sec sm" href="/riwayat/${x.id}" style="padding: 0.45rem 1.3rem; border-radius: var(--radius-pill); font-weight: 600;">
          Lihat
        </a>
      </div>
    `).join('');

  } catch(e) {
    $('list').innerHTML = `
      <div class="card warn" style="text-align: center; padding: 2rem;">
        Gagal memuat riwayat: ${esc(e.message)}
      </div>
    `;
  }
}

$('q').oninput = () => {
  clearTimeout(timer);
  timer = setTimeout(muat, 300);
};

muat();
</script>
@endpush
@endsection
