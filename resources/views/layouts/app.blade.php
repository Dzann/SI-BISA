<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'SI-BISA')</title>
  <style>
    :root{--bg:#fff;--card:#f1f5f9;--txt:#1e293b;--mut:#64748b;--pri:#2563eb;--bd:#e2e8f0;--fs:16px}
    html[data-theme=gelap]{--bg:#0f172a;--card:#1e293b;--txt:#e2e8f0;--mut:#94a3b8;--bd:#334155}
    *{box-sizing:border-box} body{margin:0;font:var(--fs)/1.5 system-ui,sans-serif;background:var(--bg);color:var(--txt)}
    a{color:var(--pri);text-decoration:none}
    h1 span,.logo span{color:var(--pri)}
    .btn{display:inline-block;padding:.6rem 1.2rem;border:0;border-radius:10px;background:var(--pri);color:#fff;cursor:pointer;font-size:1em}
    .btn.sec{background:var(--card);color:var(--txt)} .btn.red{background:#dc2626} .btn:disabled{opacity:.5}
    input,select{width:100%;padding:.65rem .8rem;border:1px solid var(--bd);border-radius:10px;background:var(--bg);color:var(--txt);font-size:1em}
    label{display:block;margin:.8rem 0 .25rem;font-weight:600}
    .card{background:var(--card);border-radius:14px;padding:1rem;margin:.8rem 0}
    .top{display:flex;align-items:center;justify-content:space-between;padding:.8rem 1.2rem;border-bottom:1px solid var(--bd)}
    .logo{font-weight:800;font-size:1.3em;color:var(--txt)}
    .shell{display:flex;min-height:calc(100vh - 62px)} .side{width:210px;padding:1rem;border-right:1px solid var(--bd)}
    .side a{display:block;padding:.7rem 1rem;border-radius:10px;color:var(--txt);margin-bottom:.3rem}
    .side a.on{background:var(--card);font-weight:700} .main{flex:1;padding:1.2rem;max-width:860px}
    .warn{color:#b45309;margin:.5rem 0} .ok{color:#15803d}
    .stage{min-height:280px;display:flex;align-items:center;justify-content:center;background:var(--card);border-radius:12px;overflow:hidden}
    .stage img,.stage video{max-width:100%;max-height:300px}
    .row{display:flex;gap:.6rem;align-items:center;flex-wrap:wrap}
    .item{display:flex;justify-content:space-between;align-items:center;padding:.8rem 1rem;background:var(--card);border-radius:12px;margin:.5rem 0}
    table{width:100%;border-collapse:collapse} td,th{padding:.5rem;border-bottom:1px solid var(--bd);text-align:left}
    @media(max-width:700px){.shell{flex-direction:column}.side{width:100%;display:flex;gap:.3rem;border:0;padding:.5rem}.side a{margin:0;flex:1;text-align:center}}
  </style>
  <script>
    // ---- pengaturan (disimpan di browser) ----
    const DEF = {theme:'terang', size:'sedang', speed:1, autoplay:true, repeat:false, simpan:true};
    const S = Object.assign({}, DEF, (()=>{try{return JSON.parse(localStorage.getItem('sibisa'))||{}}catch(e){return {}}})());
    const saveS = () => localStorage.setItem('sibisa', JSON.stringify(S));
    function applyS(){
      document.documentElement.dataset.theme = S.theme === 'gelap' ? 'gelap' : 'terang';
      document.documentElement.style.setProperty('--fs', {kecil:'14px',sedang:'16px',besar:'19px'}[S.size]);
    }
    applyS();
  </script>
</head>
<body>
@hasSection('bare')
  @yield('content')
@else
  <div class="top">
    <a class="logo" href="/">✋ SI-<span>BISA</span></a>
    <a href="/akun" title="Akun">👤</a>
  </div>
  <div class="shell">
    <nav class="side">
      <a href="/app"        class="{{ request()->is('app') ? 'on' : '' }}">🏠 Beranda</a>
      <a href="/riwayat"   class="{{ request()->is('riwayat*') ? 'on' : '' }}">🕘 Riwayat</a>
      <a href="/pengaturan" class="{{ request()->is('pengaturan') ? 'on' : '' }}">⚙️ Pengaturan</a>
    </nav>
    <main class="main">@yield('content')</main>
  </div>
@endif

<script>
  const $ = id => document.getElementById(id);
  const csrf = document.querySelector('meta[name=csrf-token]').content;
  const esc = t => String(t ?? '').replace(/[&<>"]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]));
  async function api(url, o = {}){
    const h = {'Accept':'application/json','X-CSRF-TOKEN':csrf};
    if (o.json){ h['Content-Type']='application/json'; o.body = JSON.stringify(o.json); delete o.json; }
    const r = await fetch(url, {credentials:'same-origin', ...o, headers:{...h, ...(o.headers||{})}});
    let d = null; try { d = await r.json(); } catch(e){}
    if (!r.ok) throw Object.assign(new Error(d?.message || 'Terjadi kesalahan'), {status:r.status, data:d});
    return d;
  }
  const fmtTgl = s => new Date(s.replace(' ','T')+'Z').toLocaleString('id-ID',{dateStyle:'long',timeStyle:'short'});

  // ---- pemutar urutan isyarat (dipakai Beranda & Detail Riwayat) ----
  function Player(stage, label, counter){
    const P = {items:[], i:-1, t:null, playing:false};
    const render = () => {
      clearTimeout(P.t);
      const it = P.items[P.i]; if (!it) return;
      label.textContent = `${it.kata_asli} → ${it.kata_dasar}`;
      counter.textContent = `Gerakan ${P.i+1} dari ${P.items.length}`;
      stage.innerHTML = it.tipe_media === 'video'
        ? `<video src="${esc(it.url_media)}" autoplay muted playsinline></video>`
        : `<img src="${esc(it.url_media)}" alt="${esc(it.kata_dasar)}">`;
      const v = stage.querySelector('video'); if (v) v.playbackRate = S.speed;
      if (P.playing) P.t = setTimeout(P.next, (it.durasi_ms || 2000) / S.speed);
    };
    P.add = list => { const was = P.items.length; P.items.push(...list.filter(x => x.url_media));
      if (P.items.length && (!P.playing || was === 0) && S.autoplay){ P.playing = true; P.i = Math.min(was, P.items.length-1); render(); } else if (P.i < 0 && P.items.length){ P.i = 0; render(); } };
    P.next = () => { if (P.i < P.items.length-1){ P.i++; render(); }
                     else if (S.repeat && P.items.length){ P.i = 0; render(); } else { P.playing = false; clearTimeout(P.t); } };
    P.prev = () => { if (P.i > 0){ P.i--; render(); } };
    P.toggle = () => { P.playing = !P.playing; if (P.playing){ if (P.i >= P.items.length-1 && !S.repeat) P.i = 0; } render(); if(!P.playing) clearTimeout(P.t); };
    return P;
  }
</script>
@stack('scripts')
</body>
</html>
