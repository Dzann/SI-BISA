@extends('layouts.app')
@section('title','Beranda - SI-BISA')
@section('content')

<!-- Judul Halaman Sesuai Figma -->
<div style="margin-bottom: 1.5rem;">
  <h2 style="font-size: 1.7em; font-weight: 700; color: var(--txt-main); margin-bottom: 0.25rem;">
    Penerjemah Suara ke Bahasa Isyarat
  </h2>
  <p style="color: var(--txt-sub); font-size: 0.95em;">
    Tekan tombol mikrofon dan mulai berbicara.
  </p>
</div>

<!-- Card Utama Input -->
<div class="card" style="padding: 1.8rem; border-radius: var(--radius-xl);">
  <!-- Tab Mode Switcher -->
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.2rem; flex-wrap: wrap; gap: 0.8rem;">
    <div class="tab-bar" style="margin-bottom: 0;">
      <button type="button" id="tabVoice" class="tab-btn active">🎙️ Masukan Suara</button>
      <button type="button" id="tabManual" class="tab-btn">⌨️ Ketik Teks Manual</button>
    </div>
    <div style="font-size: 0.85em; color: var(--txt-muted);">
      Sistem Isyarat Berbasis Suara
    </div>
  </div>

  <!-- Pilihan Bahasa Input Sesuai Figma -->
  <div style="margin-bottom: 1.2rem;">
    <label style="margin-top:0">Bahasa Input</label>
    <div style="position: relative;">
      <input type="text" value="Bahasa Indonesia" class="input-pill" disabled style="padding-right: 2.5rem; font-weight: 600; background: var(--bg-subtle);">
      <span style="position: absolute; right: 1.1rem; top: 50%; transform: translateY(-50%); color: var(--success); font-weight: bold;">✓</span>
    </div>
  </div>

  <!-- Panel 1: Suara (Mikrofon) Sesuai Desain Figma -->
  <div id="voiceSection">
    <div style="background: var(--bg-subtle); border: 1.5px solid var(--border-color); border-radius: var(--radius-lg); padding: 2.2rem 1.5rem; text-align: center; margin-bottom: 1.4rem;">
      <!-- Microphone Icon Container -->
      <div id="micRing" style="width: 76px; height: 76px; border-radius: 50%; background: var(--bg-card); border: 2px solid var(--border-color); display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-size: 2.2rem; box-shadow: var(--shadow-sm); transition: all 0.3s ease;">
        <span id="micIcon">🎙️</span>
      </div>

      <div id="status" style="font-weight: 600; color: var(--txt-sub); font-size: 1.05em; margin-bottom: 1.4rem;">
        Siap Mendengarkan ...
      </div>

      <!-- Action Buttons Row from Figma: Mulai, Jeda, Stop -->
      <div class="row" style="justify-content: center; gap: 0.8rem;">
        <button id="mulai" class="btn sec" style="border-radius: var(--radius-pill); padding: 0.6rem 1.4rem;">
          ▶ Mulai
        </button>
        <button id="jeda" class="btn sec" disabled style="border-radius: var(--radius-pill); padding: 0.6rem 1.4rem;">
          ⏸ Jeda
        </button>
        <button id="stop" class="btn sec" disabled style="border-radius: var(--radius-pill); padding: 0.6rem 1.4rem;">
          ⏹ Stop
        </button>
      </div>
    </div>
  </div>

  <!-- Panel 2: Ketik Teks Manual (Fallback UX) -->
  <div id="manualSection" style="display: none; margin-bottom: 1.4rem;">
    <label style="margin-top:0">Ketik Kalimat untuk Diterjemahkan</label>
    <textarea id="manualText" rows="3" placeholder="Ketik kata atau kalimat di sini (contoh: halo selamat pagi)..." style="border-radius: var(--radius-md); padding: 0.9rem; resize: vertical;"></textarea>
    <div class="row" style="margin-top: 0.8rem; justify-content: space-between;">
      <div class="row">
        <button id="btnTerjemahManual" class="btn">
          ▶ Terjemahkan Teks
        </button>
        <button id="btnResetManual" class="btn sec">
          Bersihkan
        </button>
      </div>
      <span style="color: var(--txt-muted); font-size: 0.85em;">Tekan <b>Ctrl + Enter</b> untuk kirim</span>
    </div>
  </div>

  <!-- Teks yang Dikenali (Sesuai Figma) -->
  <div style="margin-bottom: 1.2rem;">
    <label>Teks yang Dikenali</label>
    <div id="teks" style="min-height: 52px; padding: 0.85rem 1.2rem; background: var(--bg-input); border: 1.5px solid var(--border-color); border-radius: var(--radius-md); color: var(--txt-sub); font-size: 0.95em; display: flex; align-items: center;">
      Hasil ucapan akan muncul di sini ...
    </div>
  </div>

  <!-- Pilihan Bahasa Isyarat (Sesuai Figma) -->
  <div>
    <label>Pilihan Bahasa Isyarat</label>
    <div style="position: relative;">
      <input type="text" value="BISINDO" class="input-pill" disabled style="font-weight: 600; background: var(--bg-subtle);">
      <span style="position: absolute; right: 1.1rem; top: 50%; transform: translateY(-50%); font-size: 0.8em; background: var(--primary-light); color: var(--primary); padding: 0.2rem 0.6rem; border-radius: var(--radius-pill); font-weight: 700;">AKTIF</span>
    </div>
  </div>

  <!-- Status Loading Indicator -->
  <div id="loadingIndicator" style="display:none; margin-top: 1rem; color: var(--primary); font-weight: 600; text-align: center;">
    ⏳ Memproses terjemahan & pencocokan BISINDO...
  </div>
</div>

<!-- Hasil Terjemahan (Beranda Hasil Convert Sesuai Figma) -->
<div style="margin-top: 2rem;">
  <h3 style="font-size: 1.4em; font-weight: 700; color: var(--txt-main); margin-bottom: 0.8rem;">
    Hasil Terjemahan
  </h3>

  <div class="card" style="padding: 1.6rem; border-radius: var(--radius-xl);">
    <!-- Bar Teks Hasil Terjemahan -->
    <div id="teksHasilBar" style="padding: 0.75rem 1.2rem; background: var(--bg-subtle); border: 1px solid var(--border-color); border-radius: var(--radius-pill); font-weight: 600; color: var(--txt-main); margin-bottom: 1rem; text-align: center;">
      — Belum ada terjemahan aktif —
    </div>

    <!-- Active Word Mapping Badge -->
    <div id="kata" style="font-weight: 700; min-height: 1.6em; text-align: center; color: var(--primary); font-size: 1.1em; margin-bottom: 0.5rem;"></div>

    <!-- Stage Video/Animasi BISINDO -->
    <div id="stage" class="stage" style="border-radius: var(--radius-lg);">
      <div style="color: var(--txt-muted); text-align: center; padding: 2rem;">
        <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">✋</div>
        Visualisasi bahasa isyarat BISINDO akan ditampilkan di sini
      </div>
    </div>

    <!-- Video Player Controls Bar (Sesuai Figma) -->
    <div style="background: var(--bg-subtle); border: 1px solid var(--border-color); border-radius: var(--radius-pill); padding: 0.6rem 1.2rem; margin-top: 1rem; display: flex; align-items: center; justify-content: space-between; gap: 0.8rem; flex-wrap: wrap;">
      <div class="row" style="gap: 0.5rem;">
        <button id="play" class="btn sm sec" style="width: 36px; height: 36px; padding: 0; border-radius: 50%;" title="Putar / Jeda">⏯</button>
        <span id="videoTime" style="font-size: 0.85em; color: var(--txt-sub); font-variant-numeric: tabular-nums;">0:00 / 0:00</span>
      </div>

      <!-- Step Counter & Navigation -->
      <div class="row" style="gap: 0.6rem;">
        <button id="prev" class="btn sm sec" style="width: 36px; height: 36px; padding: 0; border-radius: 50%;" title="Sebelumnya">‹</button>
        <span id="hitung" style="font-size: 0.9em; font-weight: 600; color: var(--txt-main);">Gerakan 0 dari 0</span>
        <button id="next" class="btn sm sec" style="width: 36px; height: 36px; padding: 0; border-radius: 50%;" title="Selanjutnya">›</button>
      </div>
    </div>

    <!-- Box Kata yang Tidak Ditemukan Sesuai Figma -->
    <div id="notFoundBox" style="display:none; margin-top: 1.2rem; padding: 0.85rem 1.1rem; background: #fffbeb; border: 1.5px solid #fde68a; border-radius: var(--radius-md); color: #92400e; font-size: 0.9em;">
      <b>Kata yang belum tersedia di kamus:</b>
      <span id="notFoundWords" style="margin-left: 0.3rem;"></span>
    </div>

    <div id="warn" class="warn" style="margin-top: 0.8rem; text-align: center;"></div>
  </div>
</div>

@push('scripts')
<script>
const SR = window.SpeechRecognition || window.webkitSpeechRecognition;
const P = Player($('stage'), $('kata'), $('hitung'));

let sessionId = localStorage.getItem('sibisa_session_id') ? parseInt(localStorage.getItem('sibisa_session_id')) : null;
let rec = null;
let isPaused = false;
let queue = Promise.resolve();

// Kontrol Player
$('prev').onclick = P.prev;
$('next').onclick = () => { P.playing = false; P.next(); };
$('play').onclick = P.toggle;

// Tab Selector
const tabVoice = $('tabVoice');
const tabManual = $('tabManual');
const voiceSection = $('voiceSection');
const manualSection = $('manualSection');

function switchTab(mode) {
  if (mode === 'voice') {
    tabVoice.classList.add('active');
    tabManual.classList.remove('active');
    voiceSection.style.display = 'block';
    manualSection.style.display = 'none';
  } else {
    tabManual.classList.add('active');
    tabVoice.classList.remove('active');
    manualSection.style.display = 'block';
    voiceSection.style.display = 'none';
    if (rec && $('stop').disabled === false) {
      rec.stop();
    }
    $('manualText').focus();
  }
}

tabVoice.onclick = () => switchTab('voice');
tabManual.onclick = () => switchTab('manual');

function setVoiceUI(listening, paused = false) {
  $('mulai').disabled = listening && !paused;
  $('jeda').disabled = !listening;
  $('stop').disabled = !listening && !paused;

  const micRing = $('micRing');
  if (listening && !paused) {
    $('status').textContent = 'Mendengarkan ucapan Anda...';
    $('status').style.color = 'var(--primary)';
    $('micIcon').textContent = '🔴';
    micRing.style.borderColor = 'var(--primary)';
    micRing.style.boxShadow = '0 0 0 6px rgba(37,99,235,0.18)';
  } else if (paused) {
    $('status').textContent = 'Perekaman suara dijeda';
    $('status').style.color = 'var(--warning)';
    $('micIcon').textContent = '⏸️';
    micRing.style.borderColor = 'var(--warning)';
    micRing.style.boxShadow = 'none';
  } else {
    $('status').textContent = 'Siap Mendengarkan ...';
    $('status').style.color = 'var(--txt-sub)';
    $('micIcon').textContent = '🎙️';
    micRing.style.borderColor = 'var(--border-color)';
    micRing.style.boxShadow = 'var(--shadow-sm)';
  }
}

// Inisialisasi Web Speech API
if (!SR) {
  $('warn').textContent = 'Catatan: Browser ini tidak mendukung Web Speech API (Gunakan Google Chrome untuk rekaman suara). Mode otomatis dialihkan ke Ketik Teks Manual.';
  $('mulai').disabled = true;
  switchTab('manual');
} else {
  try {
    rec = new SR();
    rec.lang = 'id-ID';
    rec.continuous = true;
    rec.interimResults = true;

    rec.onresult = e => {
      let interim = '';
      for (let i = e.resultIndex; i < e.results.length; i++) {
        const r = e.results[i];
        if (r.isFinal) {
          const t = r[0].transcript.trim();
          $('teks').textContent = t;
          queue = queue.then(() => kirim(t, r[0].confidence));
        } else {
          interim += r[0].transcript;
        }
      }
      if (interim) $('teks').textContent = interim + ' ...';
    };

    rec.onerror = e => {
      if (['not-allowed', 'service-not-allowed'].includes(e.error)) {
        $('warn').textContent = 'Peringatan: Izin mikrofon ditolak. Anda dapat mengaktifkan izin atau menggunakan tab Ketik Teks Manual.';
      } else {
        $('warn').textContent = 'Error pengenalan suara: ' + e.error;
      }
      setVoiceUI(false);
    };

    rec.onend = () => {
      if (!isPaused) setVoiceUI(false);
    };

    $('mulai').onclick = () => {
      $('warn').textContent = '';
      isPaused = false;
      try {
        rec.start();
        setVoiceUI(true);
      } catch (err) {
        setVoiceUI(false);
      }
    };

    $('jeda').onclick = () => {
      if (rec) {
        rec.stop();
        isPaused = true;
        setVoiceUI(true, true);
      }
    };

    $('stop').onclick = () => {
      if (rec) rec.stop();
      isPaused = false;
      setVoiceUI(false);
    };
  } catch (err) {
    switchTab('manual');
  }
}

// Handler Terjemahan Teks Manual
$('btnTerjemahManual').onclick = () => {
  const text = $('manualText').value.trim();
  if (!text) {
    $('warn').textContent = 'Silakan ketik kalimat terlebih dahulu.';
    return;
  }
  $('teks').textContent = text;
  kirim(text, null);
};

$('btnResetManual').onclick = () => {
  $('manualText').value = '';
  $('warn').textContent = '';
  $('notFoundBox').style.display = 'none';
  P.clear();
};

$('manualText').addEventListener('keydown', e => {
  if (e.key === 'Enter' && (e.ctrlKey || !e.shiftKey)) {
    e.preventDefault();
    $('btnTerjemahManual').click();
  }
});

// Pemanggilan Backend API Translate
async function kirim(text, confidence) {
  if (!text) return;
  $('warn').textContent = '';
  $('notFoundBox').style.display = 'none';
  $('loadingIndicator').style.display = 'block';
  $('teksHasilBar').textContent = '“' + text + '”';

  try {
    const d = await api('/api/translate', {
      method: 'POST',
      json: {
        text: text,
        confidence: confidence,
        session_id: sessionId,
        simpan: S.simpan
      }
    });

    if (d.session_id) {
      sessionId = d.session_id;
      localStorage.setItem('sibisa_session_id', sessionId);
    }

    if (d.tidak_ditemukan && d.tidak_ditemukan.length > 0) {
      $('notFoundBox').style.display = 'block';
      $('notFoundWords').textContent = d.tidak_ditemukan.join(', ');
    }

    const foundItems = (d.items || []).filter(i => i.found);
    if (foundItems.length > 0) {
      P.clear();
      P.add(foundItems);
    } else {
      $('stage').innerHTML = '<div style="color:var(--txt-muted); text-align:center; padding:2rem;"><div style="font-size:2rem; margin-bottom:0.5rem">⚠️</div>Kata belum tersedia di basis data BISINDO</div>';
      $('kata').textContent = text;
      $('hitung').textContent = 'Gerakan 0 dari 0';
    }
  } catch (e) {
    $('warn').textContent = 'Gagal memproses terjemahan: ' + e.message;
  } finally {
    $('loadingIndicator').style.display = 'none';
  }
}
</script>
@endpush
@endsection
