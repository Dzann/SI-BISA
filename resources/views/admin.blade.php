@extends('layouts.app')
@section('bare','1')
@section('title','Dashboard Admin - SI-BISA')
@section('content')

<!-- Header Bar Admin -->
<header style="background: var(--bg-card); border-bottom: 1px solid var(--border-color); padding: 1rem 1.8rem; box-shadow: var(--shadow-sm);">
  <div style="max-width: 1100px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between;">
    <a class="brand-logo" href="/" style="margin-bottom:0; padding:0; display:flex; align-items:center; gap:0.6rem;">
      <img src="{{ asset('storage/Logo/PACKAGES-04.png') }}" alt="SI-BISA" style="height: 42px; width: auto; object-fit: contain;">
      <span style="font-size:0.7em; background:var(--primary-light); color:var(--primary); padding:0.2rem 0.6rem; border-radius:var(--radius-pill); font-weight:700;">ADMIN</span>
    </a>

    <div class="row" style="gap: 0.8rem;">
      <a href="/" class="btn sec sm" style="border-radius: var(--radius-pill); text-decoration: none;">
        🌐 Landing Page
      </a>
      <a href="/app" class="btn sec sm" style="border-radius: var(--radius-pill); text-decoration: none;">
        🏠 Buka Penerjemah
      </a>
      <button id="keluar" class="btn sm red" style="border-radius: var(--radius-pill);">
        Keluar
      </button>
    </div>
  </div>
</header>

<main style="max-width: 1100px; margin: 2rem auto 4rem; padding: 0 1.2rem;">
  <div style="margin-bottom: 1.8rem;">
    <h2 style="font-size: 1.8em; font-weight: 700; color: var(--txt-main); margin-bottom: 0.3rem;">
      Kelola Kamus Isyarat BISINDO
    </h2>
    <p style="color: var(--txt-sub); font-size: 0.95em;">
      Tambah, perbarui, dan kelola pustaka video atau gambar bahasa isyarat untuk pencocokan kata dasar.
    </p>
  </div>

  <!-- Form Tambah / Edit Isyarat -->
  <div class="card" style="padding: 1.8rem; border-radius: var(--radius-xl); margin-bottom: 2rem;">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
      <b id="judulForm" style="font-size: 1.15em; color: var(--txt-main);">
        ➕ Tambah Isyarat Baru
      </b>
      <span id="editBadge" style="display:none; font-size: 0.8em; background: #fef3c7; color: #b45309; padding: 0.2rem 0.6rem; border-radius: var(--radius-pill); font-weight: 700;">
        MODE EDIT
      </span>
    </div>

    <input type="hidden" id="editId">

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; align-items: flex-end;">
      <div>
        <label style="margin-top:0">Kata Dasar (Sastrawi)</label>
        <input id="kata" type="text" placeholder="mis. makan, pagi, kamu" class="input-pill">
      </div>
      <div style="max-width: 160px;">
        <label style="margin-top:0">Durasi (ms)</label>
        <input id="durasi" type="number" value="2000" class="input-pill">
      </div>
      <div>
        <label style="margin-top:0">Berkas Media (GIF/PNG/JPG/MP4/WEBM)</label>
        <input id="media" type="file" accept=".gif,.png,.jpg,.jpeg,.mp4,.webm" style="border-radius: var(--radius-pill); font-size: 0.85em;">
      </div>
    </div>

    <!-- Pratinjau Berkas Media Interaktif (Real-time Preview) -->
    <div id="previewContainer" style="display: none; margin-top: 1.4rem; padding: 1.2rem; background: var(--bg-subtle); border: 1.5px dashed var(--border-color); border-radius: var(--radius-lg);">
      <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.8rem; flex-wrap: wrap; gap: 0.5rem;">
        <b style="font-size: 0.95em; color: var(--txt-main); display: flex; align-items: center; gap: 0.4rem;">
          👁️ Pratinjau Media: <span id="previewLabel" style="font-weight: 600; color: var(--primary);"></span>
        </b>
        <button type="button" id="btnHapusMedia" class="btn sec sm" style="border-radius: var(--radius-pill); font-size: 0.8em; padding: 0.3rem 0.8rem;">
          ✕ Batalkan Berkas
        </button>
      </div>

      <div style="display: flex; gap: 1.4rem; align-items: center; flex-wrap: wrap;">
        <!-- Layar Pratinjau -->
        <div id="previewMediaStage" style="min-width: 140px; max-width: 260px; max-height: 200px; border-radius: var(--radius-md); overflow: hidden; background: #000000; display: flex; align-items: center; justify-content: center; box-shadow: var(--shadow-sm);">
        </div>

        <!-- Metadata Berkas -->
        <div id="previewInfo" style="font-size: 0.9em; line-height: 1.8; color: var(--txt-sub);">
          <div><b>Nama Berkas:</b> <span id="previewFileName" style="color: var(--txt-main); font-weight: 600;">-</span></div>
          <div><b>Ukuran:</b> <span id="previewFileSize">-</span></div>
          <div><b>Format:</b> <span id="previewFileType" style="background: var(--bg-card); padding: 0.15rem 0.5rem; border-radius: var(--radius-pill); font-weight: 700; color: var(--primary); text-transform: uppercase;">-</span></div>
        </div>
      </div>
    </div>

    <div class="row" style="margin-top: 1.2rem;">
      <button id="simpan" class="btn" style="border-radius: var(--radius-pill); padding: 0.6rem 1.6rem;">
        💾 Simpan Isyarat
      </button>
      <button id="batal" class="btn sec" style="display:none; border-radius: var(--radius-pill);">
        Batal
      </button>
    </div>

    <div id="msg" class="warn" style="margin-top: 0.8rem;"></div>
  </div>

  <!-- Tabel Data Kamus Isyarat -->
  <div class="card" style="padding: 1.6rem; border-radius: var(--radius-xl); overflow: auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.2rem; flex-wrap: wrap; gap: 0.8rem;">
      <div style="position: relative; width: 100%; max-width: 320px;">
        <span style="position: absolute; left: 1.1rem; top: 50%; transform: translateY(-50%); color: var(--txt-muted); display:flex; align-items:center;">
          🔍
        </span>
        <input type="text" id="cariKamus" placeholder="Cari kata dasar isyarat..." class="input-pill" style="padding-left: 2.6rem;">
      </div>
      <div id="totalBadge" style="font-size: 0.85em; color: var(--txt-sub); font-weight: 600;">
        Memuat...
      </div>
    </div>

    <table>
      <thead>
        <tr style="color: var(--txt-sub); font-size: 0.9em;">
          <th style="width: 100px;">Pratinjau</th>
          <th>Kata Dasar</th>
          <th>Tipe Media</th>
          <th>Durasi</th>
          <th>Status</th>
          <th style="text-align: right;">Aksi</th>
        </tr>
      </thead>
      <tbody id="tbody">
        <tr>
          <td colspan="6" style="text-align: center; color: var(--txt-muted); padding: 2rem;">
            Memuat data kamus...
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</main>

@push('scripts')
<script>
let rows = [];

const say = (t, ok) => {
  $('msg').className = ok ? 'ok' : 'warn';
  $('msg').textContent = t;
};

const prev = r => {
  if (r.tipe_media === 'video') {
    return `<video src="/storage/${esc(r.url_media)}" style="width:72px; height:54px; object-fit:cover; border-radius:8px; background:var(--bg-subtle)" muted></video>`;
  }
  return `<img src="/storage/${esc(r.url_media)}" style="width:72px; height:54px; object-fit:cover; border-radius:8px; background:var(--bg-subtle)">`;
};

async function muat(){
  try {
    rows = await api('/api/kamus');
    $('totalBadge').textContent = `Total: ${rows.length} isyarat`;
    renderTable();
  } catch(e) {
    say('Gagal memuat kamus: ' + e.message);
  }
}

function renderTable(){
  const q = ($('cariKamus').value || '').trim().toLowerCase();
  const filtered = q ? rows.filter(r => (r.kata_dasar || '').toLowerCase().includes(q)) : rows;
  
  if (!filtered.length) {
    $('tbody').innerHTML = '<tr><td colspan="6" style="text-align:center; padding:2rem; color:var(--txt-muted)">Tidak ada data isyarat ditemukan.</td></tr>';
    return;
  }

  $('tbody').innerHTML = filtered.map(r => `
    <tr>
      <td>${prev(r)}</td>
      <td><b style="font-size:1.05em; color:var(--txt-main);">${esc(r.kata_dasar)}</b></td>
      <td><span style="font-size:0.85em; background:var(--bg-subtle); padding:0.2rem 0.6rem; border-radius:var(--radius-pill); text-transform:uppercase; font-weight:600; color:var(--txt-sub);">${esc(r.tipe_media)}</span></td>
      <td>${r.durasi_ms} ms</td>
      <td>
        <label style="display:inline-flex; align-items:center; gap:0.4rem; cursor:pointer; margin:0;">
          <input type="checkbox" ${r.is_active ? 'checked' : ''} style="width:auto" onchange="toggle(${r.id}, this.checked)">
          <span style="font-size:0.85em; color:var(--txt-sub);">${r.is_active ? 'Aktif' : 'Nonaktif'}</span>
        </label>
      </td>
      <td style="text-align: right;">
        <button class="btn sec sm" style="border-radius: var(--radius-pill); margin-right: 0.3rem;" onclick="edit(${r.id})">Ubah</button>
        <button class="btn red sm" style="border-radius: var(--radius-pill);" onclick="hapus(${r.id})">Hapus</button>
      </td>
    </tr>
  `).join('');
}

$('cariKamus').oninput = renderTable;

// --- Pengelolaan Pratinjau Berkas Media ---
let currentBlobUrl = null;

function formatBytes(bytes) {
  if (!bytes || bytes === 0) return '0 B';
  const k = 1024;
  const dm = 2;
  const sizes = ['B', 'KB', 'MB', 'GB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
}

function showPreviewFromFile(file) {
  if (currentBlobUrl) {
    URL.revokeObjectURL(currentBlobUrl);
    currentBlobUrl = null;
  }
  if (!file) return;

  const isVideo = file.type.startsWith('video/') || /\.(mp4|webm)$/i.test(file.name);
  currentBlobUrl = URL.createObjectURL(file);

  $('previewLabel').textContent = 'Berkas Baru yang Dipilih';
  $('previewFileName').textContent = file.name;
  $('previewFileSize').textContent = formatBytes(file.size);
  $('previewFileType').textContent = file.type ? file.type.split('/')[1] : file.name.split('.').pop();

  if (isVideo) {
    $('previewMediaStage').innerHTML = `<video src="${currentBlobUrl}" controls autoplay muted playsinline style="max-width:100%; max-height:180px; object-fit:contain;"></video>`;
  } else {
    $('previewMediaStage').innerHTML = `<img src="${currentBlobUrl}" alt="Pratinjau" style="max-width:100%; max-height:180px; object-fit:contain;">`;
  }
  $('previewContainer').style.display = 'block';
}

function showPreviewFromUrl(urlMedia, tipeMedia, filename = '') {
  if (currentBlobUrl) {
    URL.revokeObjectURL(currentBlobUrl);
    currentBlobUrl = null;
  }
  if (!urlMedia) return;

  $('previewLabel').textContent = 'Media Saat Ini di Database';
  $('previewFileName').textContent = filename || urlMedia.split('/').pop();
  $('previewFileSize').textContent = 'Tersimpan di Server';
  $('previewFileType').textContent = tipeMedia || 'Media';

  const fullUrl = '/storage/' + urlMedia;
  if (tipeMedia === 'video') {
    $('previewMediaStage').innerHTML = `<video src="${fullUrl}" controls autoplay muted playsinline style="max-width:100%; max-height:180px; object-fit:contain;"></video>`;
  } else {
    $('previewMediaStage').innerHTML = `<img src="${fullUrl}" alt="Pratinjau" style="max-width:100%; max-height:180px; object-fit:contain;">`;
  }
  $('previewContainer').style.display = 'block';
}

$('media').onchange = () => {
  const f = $('media').files[0];
  if (f) {
    showPreviewFromFile(f);
  } else {
    const id = $('editId').value;
    if (id) {
      const r = rows.find(x => x.id == id);
      if (r && r.url_media) {
        showPreviewFromUrl(r.url_media, r.tipe_media);
        return;
      }
    }
    $('previewContainer').style.display = 'none';
  }
};

$('btnHapusMedia').onclick = () => {
  $('media').value = '';
  const id = $('editId').value;
  if (id) {
    const r = rows.find(x => x.id == id);
    if (r && r.url_media) {
      showPreviewFromUrl(r.url_media, r.tipe_media);
      return;
    }
  }
  $('previewContainer').style.display = 'none';
};

function reset(){
  $('editId').value = '';
  $('kata').value = '';
  $('durasi').value = 2000;
  $('media').value = '';
  $('judulForm').textContent = '➕ Tambah Isyarat Baru';
  $('editBadge').style.display = 'none';
  $('batal').style.display = 'none';
  $('msg').textContent = '';
  if (currentBlobUrl) {
    URL.revokeObjectURL(currentBlobUrl);
    currentBlobUrl = null;
  }
  $('previewContainer').style.display = 'none';
}

function edit(id){
  const r = rows.find(x => x.id === id);
  if (!r) return;
  $('editId').value = id;
  $('kata').value = r.kata_dasar;
  $('durasi').value = r.durasi_ms;
  $('media').value = '';
  $('judulForm').textContent = '✏️ Ubah Isyarat BISINDO';
  $('editBadge').style.display = 'inline-block';
  $('batal').style.display = 'inline-block';

  if (r.url_media) {
    showPreviewFromUrl(r.url_media, r.tipe_media);
  } else {
    $('previewContainer').style.display = 'none';
  }

  window.scrollTo({top: 0, behavior: 'smooth'});
}

$('batal').onclick = reset;

$('simpan').onclick = async () => {
  const id = $('editId').value;
  const kata = $('kata').value.trim();
  if (!kata) {
    say('Kata dasar wajib diisi.');
    return;
  }

  const fd = new FormData();
  fd.append('kata_dasar', kata);
  fd.append('durasi_ms', $('durasi').value);
  if ($('media').files[0]) {
    fd.append('media', $('media').files[0]);
  }
  if (id) {
    fd.append('_method', 'PUT');
  }

  $('simpan').disabled = true;
  $('simpan').textContent = 'Menyimpan...';

  try {
    const d = await api(id ? '/api/kamus/' + id : '/api/kamus', {method: 'POST', body: fd});
    say(d.message, true);
    reset();
    muat();
  } catch(e) {
    say(e.data?.errors ? Object.values(e.data.errors).flat().join(' ') : e.message);
  } finally {
    $('simpan').disabled = false;
    $('simpan').textContent = '💾 Simpan Isyarat';
  }
};

async function hapus(id){
  if (!confirm('Apakah Anda yakin ingin menghapus isyarat ini dari kamus?')) return;
  try {
    await api('/api/kamus/' + id, {method: 'DELETE'});
    say('Isyarat berhasil dihapus', true);
    muat();
  } catch(e) {
    say('Gagal menghapus: ' + e.message);
  }
}

async function toggle(id, v){
  const fd = new FormData();
  fd.append('_method', 'PUT');
  fd.append('is_active', v ? 1 : 0);
  try {
    await api('/api/kamus/' + id, {method: 'POST', body: fd});
  } catch(e) {
    say('Gagal mengubah status: ' + e.message);
  }
}

$('keluar').onclick = async () => {
  await api('/api/logout', {method: 'POST'});
  location.href = '/login';
};

muat();
</script>
@endpush
@endsection
