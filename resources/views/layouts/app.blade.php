<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'SI-BISA - Penerjemah Suara ke Bahasa Isyarat')</title>
  
  <!-- Favicon Tab Browser (SI-BISA WEB) -->
  <link rel="icon" type="image/png" href="{{ asset('logo/SI-BISA WEB.png') }}?v=1">
  <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v=1">
  <link rel="apple-touch-icon" href="{{ asset('logo/SI-BISA WEB.png') }}?v=1">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  
  <style>
    :root {
      --bg-page: #f8fafc;
      --bg-card: #ffffff;
      --bg-subtle: #f1f5f9;
      --bg-input: #ffffff;
      --txt-main: #1e293b;
      --txt-sub: #64748b;
      --txt-muted: #94a3b8;
      --border-color: #e2e8f0;
      --border-focus: #3b82f6;
      --primary: #2563eb;
      --primary-hover: #1d4ed8;
      --primary-light: #eff6ff;
      --danger: #ef4444;
      --success: #10b981;
      --warning: #f59e0b;
      --radius-sm: 8px;
      --radius-md: 14px;
      --radius-lg: 20px;
      --radius-xl: 24px;
      --radius-pill: 9999px;
      --shadow-sm: 0 1px 3px rgba(0,0,0,0.04);
      --shadow-md: 0 4px 14px rgba(0,0,0,0.06);
      --shadow-btn: 0 4px 14px rgba(37,99,235,0.25);
      --fs: 16px;
    }

    html[data-theme="gelap"] {
      --bg-page: #0f172a;
      --bg-card: #1e293b;
      --bg-subtle: #334155;
      --bg-input: #0f172a;
      --txt-main: #f8fafc;
      --txt-sub: #cbd5e1;
      --txt-muted: #94a3b8;
      --border-color: #334155;
      --border-focus: #60a5fa;
      --primary: #3b82f6;
      --primary-hover: #2563eb;
      --primary-light: #1e3a8a;
      --shadow-sm: 0 1px 3px rgba(0,0,0,0.3);
      --shadow-md: 0 4px 14px rgba(0,0,0,0.3);
      --shadow-btn: 0 4px 14px rgba(59,130,246,0.3);
    }

    /* Logo Switching untuk Mode Terang & Mode Gelap */
    .logo-dark {
      display: none !important;
    }
    .logo-light {
      display: inline-block !important;
    }

    html[data-theme="gelap"] .logo-dark {
      display: inline-block !important;
    }
    html[data-theme="gelap"] .logo-light {
      display: none !important;
    }

    /* Styling Landing Page Khusus Dark Mode */
    .landing-hero {
      background: linear-gradient(135deg, #eff6ff 0%, #f0fdf4 100%);
      border: 1px solid #dbeafe;
      border-radius: var(--radius-xl);
      padding: 3.5rem 3rem;
      margin-bottom: 2.5rem;
      box-shadow: var(--shadow-sm);
      transition: all 0.3s ease;
    }
    .landing-hero h1 {
      font-size: 3.2rem;
      font-weight: 800;
      color: #1e293b;
      line-height: 1.1;
      margin-bottom: 0.6rem;
    }
    .landing-hero .hero-sub {
      font-size: 1.2rem;
      font-weight: 600;
      color: #475569;
      margin-bottom: 1.2rem;
    }
    .landing-hero .hero-desc {
      font-size: 1.05rem;
      color: #64748b;
      max-width: 620px;
      line-height: 1.6;
      margin-bottom: 2.2rem;
    }

    html[data-theme="gelap"] .landing-hero {
      background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%) !important;
      border-color: #334155 !important;
      box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.5), 0 0 25px rgba(59, 130, 246, 0.12) !important;
    }
    html[data-theme="gelap"] .landing-hero h1 {
      color: #f8fafc !important;
    }
    html[data-theme="gelap"] .landing-hero .hero-sub {
      color: #93c5fd !important;
    }
    html[data-theme="gelap"] .landing-hero .hero-desc {
      color: #cbd5e1 !important;
    }

    .feature-icon-circle {
      width: 48px;
      height: 48px;
      border-radius: 50%;
      background: #eff6ff;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.4em;
      flex-shrink: 0;
      transition: background 0.2s;
    }
    html[data-theme="gelap"] .feature-icon-circle {
      background: rgba(59, 130, 246, 0.18) !important;
      color: #93c5fd !important;
    }

    .step-icon-circle {
      width: 44px;
      height: 44px;
      border-radius: 50%;
      background: #f1f5f9;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.3em;
      margin-bottom: 1rem;
      transition: background 0.2s;
    }
    html[data-theme="gelap"] .step-icon-circle {
      background: #334155 !important;
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
      font-size: var(--fs);
      line-height: 1.5;
      background: var(--bg-page);
      color: var(--txt-main);
      min-height: 100vh;
    }

    a { color: var(--primary); text-decoration: none; transition: all 0.2s; }
    a:hover { opacity: 0.85; }

    /* Buttons */
    .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
      padding: 0.65rem 1.4rem;
      border: 0;
      border-radius: var(--radius-pill);
      background: var(--primary);
      color: #ffffff;
      font-weight: 600;
      font-size: 0.95em;
      cursor: pointer;
      box-shadow: var(--shadow-btn);
      transition: all 0.2s ease;
      text-decoration: none;
    }
    .btn:hover { background: var(--primary-hover); transform: translateY(-1px); }
    .btn:active { transform: translateY(0); }
    .btn:disabled { opacity: 0.55; cursor: not-allowed; transform: none; box-shadow: none; }
    .btn.sec {
      background: var(--bg-subtle);
      color: var(--txt-main);
      box-shadow: none;
      border: 1px solid var(--border-color);
    }
    .btn.sec:hover { background: var(--border-color); }
    .btn.red { background: var(--danger); box-shadow: 0 4px 14px rgba(239,68,68,0.25); color: #fff; }
    .btn.sm { padding: 0.4rem 0.9rem; font-size: 0.85em; }

    /* Inputs */
    input[type="text"], input[type="email"], input[type="password"], input[type="number"], select, textarea {
      width: 100%;
      padding: 0.75rem 1.1rem;
      border: 1.5px solid var(--border-color);
      border-radius: var(--radius-md);
      background: var(--bg-input);
      color: var(--txt-main);
      font-size: 0.95em;
      font-family: inherit;
      outline: none;
      transition: border-color 0.2s, box-shadow 0.2s;
    }
    input:focus, select:focus, textarea:focus {
      border-color: var(--border-focus);
      box-shadow: 0 0 0 3px rgba(37,99,235,0.12);
    }
    input:disabled, textarea:disabled {
      background: var(--bg-subtle);
      color: var(--txt-sub);
      cursor: not-allowed;
    }
    .input-pill {
      border-radius: var(--radius-pill) !important;
    }

    label {
      display: block;
      margin: 0.9rem 0 0.35rem;
      font-weight: 600;
      font-size: 0.9em;
      color: var(--txt-sub);
    }

    /* Cards */
    .card {
      background: var(--bg-card);
      border: 1px solid var(--border-color);
      border-radius: var(--radius-lg);
      padding: 1.4rem;
      margin: 1rem 0;
      box-shadow: var(--shadow-sm);
    }

    /* Layout Shell */
    .app-shell {
      display: flex;
      min-height: 100vh;
      max-width: 1400px;
      margin: 0 auto;
    }

    .app-sidebar {
      width: 260px;
      padding: 1.8rem 1.2rem;
      background: var(--bg-card);
      border-right: 1px solid var(--border-color);
      display: flex;
      flex-direction: column;
      gap: 0.5rem;
    }

    .brand-logo {
      display: flex;
      align-items: center;
      gap: 0.6rem;
      font-weight: 800;
      font-size: 1.35em;
      color: var(--txt-main);
      margin-bottom: 2rem;
      padding: 0 0.5rem;
      text-decoration: none;
    }
    .brand-logo span { color: var(--primary); }

    .nav-link {
      display: flex;
      align-items: center;
      gap: 0.85rem;
      padding: 0.85rem 1.2rem;
      border-radius: var(--radius-pill);
      color: var(--txt-sub);
      font-weight: 600;
      font-size: 0.95em;
      transition: all 0.2s;
    }
    .nav-link:hover {
      background: var(--bg-subtle);
      color: var(--txt-main);
    }
    .nav-link.active {
      background: var(--bg-subtle);
      color: var(--primary);
      font-weight: 700;
    }
    .nav-link svg {
      width: 20px;
      height: 20px;
      stroke-width: 2;
    }

    .app-main {
      flex: 1;
      padding: 2rem;
      max-width: 960px;
    }

    .app-header-bar {
      display: flex;
      justify-content: flex-end;
      align-items: center;
      margin-bottom: 1.5rem;
    }

    .user-avatar-btn {
      width: 44px;
      height: 44px;
      border-radius: 50%;
      background: var(--bg-subtle);
      border: 1px solid var(--border-color);
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--txt-main);
      font-size: 1.2em;
      text-decoration: none;
      transition: transform 0.2s, background 0.2s;
    }
    .user-avatar-btn:hover {
      background: var(--border-color);
      transform: scale(1.05);
    }

    /* Tabs */
    .tab-bar {
      display: inline-flex;
      background: var(--bg-subtle);
      padding: 4px;
      border-radius: var(--radius-pill);
      margin-bottom: 1.2rem;
      border: 1px solid var(--border-color);
    }
    .tab-btn {
      background: transparent;
      border: 0;
      padding: 0.55rem 1.2rem;
      font-size: 0.9em;
      font-weight: 600;
      color: var(--txt-sub);
      cursor: pointer;
      border-radius: var(--radius-pill);
      transition: all 0.2s;
      font-family: inherit;
    }
    .tab-btn.active {
      background: var(--bg-card);
      color: var(--primary);
      box-shadow: var(--shadow-sm);
    }

    /* Video / Animation Stage */
    .stage {
      min-height: 320px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: var(--bg-subtle);
      border: 1px solid var(--border-color);
      border-radius: var(--radius-lg);
      overflow: hidden;
      margin: 1rem 0;
      position: relative;
    }
    .stage img, .stage video {
      max-width: 100%;
      max-height: 340px;
      border-radius: var(--radius-md);
      object-fit: contain;
    }

    /* Lists & Tables */
    .item-card {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1.1rem 1.3rem;
      background: var(--bg-card);
      border: 1px solid var(--border-color);
      border-radius: var(--radius-md);
      margin: 0.7rem 0;
      box-shadow: var(--shadow-sm);
      transition: transform 0.15s, box-shadow 0.15s;
    }
    .item-card:hover {
      transform: translateY(-2px);
      box-shadow: var(--shadow-md);
    }

    .row { display: flex; gap: 0.8rem; align-items: center; flex-wrap: wrap; }
    .warn { color: var(--danger); font-size: 0.9em; margin: 0.6rem 0; font-weight: 500; }
    .ok { color: var(--success); font-size: 0.9em; margin: 0.6rem 0; font-weight: 500; }
    table { width: 100%; border-collapse: collapse; }
    td, th { padding: 0.75rem 0.6rem; border-bottom: 1px solid var(--border-color); text-align: left; }

    /* Responsive */
    @media (max-width: 820px) {
      .app-shell { flex-direction: column; }
      .app-sidebar {
        width: 100%;
        padding: 0.8rem 1rem;
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
        border-right: 0;
        border-bottom: 1px solid var(--border-color);
      }
      .brand-logo { margin-bottom: 0; }
      .app-main { padding: 1.2rem; }
      .nav-links-wrap {
        display: flex;
        gap: 0.4rem;
      }
      .nav-link span { display: none; }
      .nav-link { padding: 0.5rem; }
    }
  </style>

  <script>
    // Pengaturan konfigurasi aplikasi (disimpan di browser)
    const DEF = {
      theme: (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) ? 'gelap' : 'terang',
      size: 'sedang',
      speed: 1,
      autoplay: true,
      repeat: false,
      simpan: true
    };
    const S = Object.assign({}, DEF, (()=>{try{return JSON.parse(localStorage.getItem('sibisa'))||{}}catch(e){return {}}})());
    const saveS = () => localStorage.setItem('sibisa', JSON.stringify(S));
    function applyS(){
      const isDark = S.theme === 'gelap';
      document.documentElement.dataset.theme = isDark ? 'gelap' : 'terang';
      document.documentElement.style.setProperty('--fs', {kecil:'14px',sedang:'16px',besar:'18px'}[S.size]);
      
      // Sinkronisasi teks / ikon tombol toggle tema jika ada
      document.querySelectorAll('.theme-toggle-btn').forEach(btn => {
        btn.innerHTML = isDark ? '☀️' : '🌙';
        btn.setAttribute('aria-label', isDark ? 'Ganti ke Tema Terang' : 'Ganti ke Tema Gelap');
        btn.title = isDark ? 'Ganti ke Tema Terang' : 'Ganti ke Tema Gelap';
      });
    }
    function toggleTheme(){
      S.theme = S.theme === 'gelap' ? 'terang' : 'gelap';
      saveS();
      applyS();
      // Sinkronisasi radio di halaman pengaturan jika sedang dibuka
      document.querySelectorAll('input[name="theme"]').forEach(r => {
        r.checked = (r.value === S.theme);
      });
    }
    applyS();
  </script>
</head>
<body>

@hasSection('bare')
  @yield('content')
@else
  <div class="app-shell">
    <!-- Sidebar Navigation -->
    <aside class="app-sidebar">
      <a class="brand-logo" href="/" style="display: inline-flex; align-items: center; margin-bottom: 2rem; padding: 0 0.2rem;">
        <img src="{{ asset('logo/PACKAGES-04.png') }}" alt="SI-BISA" class="logo-light" style="height: 46px; width: auto; max-width: 180px; object-fit: contain;">
        <img src="{{ asset('logo/SI-BISA Black.png') }}?v=2" alt="SI-BISA" class="logo-dark" style="height: 46px; width: auto; max-width: 180px; object-fit: contain;">
      </a>

      <div class="nav-links-wrap" style="display:flex; flex-direction:column; gap:0.4rem; width:100%">
        <a href="/app" class="nav-link {{ request()->is('app') ? 'active' : '' }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
          <span>Beranda</span>
        </a>
        <a href="/riwayat" class="nav-link {{ request()->is('riwayat*') ? 'active' : '' }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
          <span>Riwayat</span>
        </a>
        <a href="/pengaturan" class="nav-link {{ request()->is('pengaturan') ? 'active' : '' }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
          <span>Pengaturan</span>
        </a>
      </div>

      <!-- Footer Sidebar Navigasi -->
      <div style="margin-top: auto; padding-top: 1.5rem; border-top: 1px solid var(--border-color); display: flex; flex-direction: column; gap: 0.6rem;">
        <a href="/" class="nav-link" style="color: var(--txt-sub);" title="Kembali ke Landing Page">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
          <span>Landing Page</span>
        </a>
        @auth
          @if(auth()->user()->role === 'admin')
            <a href="/admin" class="btn sm" style="border-radius: var(--radius-pill); font-size: 0.85em; text-decoration: none;">
              🛠️ Panel Admin
            </a>
          @endif
        @else
          <a href="/login" class="btn sec sm" style="border-radius: var(--radius-pill); font-size: 0.85em; text-decoration: none;">
            🔐 Login Admin
          </a>
        @endauth
      </div>
    </aside>

    <!-- Main Workspace -->
    <main class="app-main">
      <div class="app-header-bar" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <!-- Tombol Kembali ke Landing Page -->
        <a href="/" class="btn sec sm" style="border-radius: var(--radius-pill); font-size: 0.85em; text-decoration: none; display: inline-flex; align-items: center; gap: 0.4rem;">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg>
          <span>Landing Page</span>
        </a>

        <!-- Bar Kanan: Tombol Switch Tema + Tombol Login Admin + Avatar Profil -->
        <div class="row" style="gap: 0.8rem;">
          <button type="button" class="theme-toggle-btn btn sec sm" onclick="toggleTheme()" title="Ganti Tema" style="border-radius: var(--radius-pill); padding: 0.45rem 0.8rem; font-size: 1.05em; display: inline-flex; align-items: center; justify-content: center; cursor: pointer;">
            🌓
          </button>
          @guest
            <a href="/login" class="btn sec sm" style="border-radius: var(--radius-pill); font-size: 0.85em; text-decoration: none;">
              🔐 Login Admin
            </a>
          @else
            @if(auth()->user()->role === 'admin')
              <a href="/admin" class="btn sec sm" style="border-radius: var(--radius-pill); font-size: 0.85em; text-decoration: none;">
                🛠️ Panel Admin
              </a>
            @endif
          @endguest
          <a href="/akun" class="user-avatar-btn" title="Akun Saya">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
          </a>
        </div>
      </div>
      @yield('content')
    </main>
  </div>
@endif

<script>
  const $ = id => document.getElementById(id);
  const csrf = document.querySelector('meta[name=csrf-token]')?.content || '';
  const esc = t => String(t ?? '').replace(/[&<>"]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]));
  
  async function api(url, o = {}){
    const h = {'Accept':'application/json','X-CSRF-TOKEN':csrf};
    if (o.json){ h['Content-Type']='application/json'; o.body = JSON.stringify(o.json); delete o.json; }
    const r = await fetch(url, {credentials:'same-origin', ...o, headers:{...h, ...(o.headers||{})}});
    let d = null; try { d = await r.json(); } catch(e){}
    if (!r.ok) throw Object.assign(new Error(d?.message || 'Terjadi kesalahan sistem'), {status:r.status, data:d});
    return d;
  }
  
  const fmtTgl = s => {
    try {
      return new Date(s.replace(' ','T')+'Z').toLocaleString('id-ID',{dateStyle:'long',timeStyle:'short'}) + ' WIB';
    } catch(e){
      return s;
    }
  };

  // ---- Pemutar urutan isyarat visual BISINDO ----
  function Player(stage, label, counter){
    const P = {items:[], i:-1, t:null, playing:false};
    
    const render = () => {
      clearTimeout(P.t);
      const it = P.items[P.i];
      if (!it) return;
      
      if (label) label.textContent = `${it.kata_asli} → ${it.kata_dasar}`;
      if (counter) counter.textContent = `Gerakan ${P.i+1} dari ${P.items.length}`;
      
      stage.innerHTML = it.tipe_media === 'video'
        ? `<video src="${esc(it.url_media)}" autoplay muted playsinline></video>`
        : `<img src="${esc(it.url_media)}" alt="${esc(it.kata_dasar)}">`;
        
      const v = stage.querySelector('video');
      if (v) v.playbackRate = S.speed;
      
      if (P.playing) {
        P.t = setTimeout(P.next, (it.durasi_ms || 2000) / S.speed);
      }
    };
    
    P.add = list => {
      const was = P.items.length;
      P.items.push(...list.filter(x => x.url_media));
      if (P.items.length && (!P.playing || was === 0) && S.autoplay){
        P.playing = true;
        P.i = Math.min(was, P.items.length - 1);
        render();
      } else if (P.i < 0 && P.items.length){
        P.i = 0;
        render();
      }
    };
    
    P.next = () => {
      if (P.i < P.items.length - 1){
        P.i++;
        render();
      } else if (S.repeat && P.items.length){
        P.i = 0;
        render();
      } else {
        P.playing = false;
        clearTimeout(P.t);
      }
    };
    
    P.prev = () => {
      if (P.i > 0){
        P.i--;
        render();
      }
    };
    
    P.clear = () => {
      clearTimeout(P.t);
      P.items = [];
      P.i = -1;
      P.playing = false;
      stage.textContent = '—';
      if (label) label.textContent = '';
      if (counter) counter.textContent = 'Gerakan 0 dari 0';
    };
    
    P.toggle = () => {
      P.playing = !P.playing;
      if (P.playing){
        if (P.i >= P.items.length - 1 && !S.repeat) P.i = 0;
      }
      render();
      if (!P.playing) clearTimeout(P.t);
    };
    
    return P;
  }
</script>
@stack('scripts')
</body>
</html>
