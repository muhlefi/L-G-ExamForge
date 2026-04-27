@extends('layouts.app')

@section('title', 'APAL AI – ' . $batch->subject)

@push('styles')
<style>
    /* ── Group Cards ─────────────────────────────────── */
    .group-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 1.25rem;
        padding: 2rem;
        margin-bottom: 2.5rem;
        position: relative;
        transition: border-color .3s;
    }
    .group-card:hover { border-color: rgba(99,102,241,.3); }

    .group-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.75rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .group-title {
        font-size: 1.2rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: .75rem;
        flex-wrap: wrap;
    }

    .pill {
        padding: .3rem .85rem;
        border-radius: .5rem;
        font-size: .7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
    }
    .pill-indigo { background: rgba(99,102,241,.12); color: #818cf8; }
    .pill-green  { background: rgba(16,185,129,.12);  color: #34d399; }
    .pill-amber  { background: rgba(245,158,11,.12);  color: #fbbf24; }
    .pill-red    { background: rgba(239,68,68,.12);   color: #f87171; }

    /* ── Question Rows ───────────────────────────────── */
    .q-row {
        background: rgba(255,255,255,.025);
        border: 1px solid var(--border);
        border-radius: .9rem;
        padding: 1.25rem 1.5rem;
        margin-bottom: 1rem;
        display: grid;
        grid-template-columns: auto 1fr auto;
        gap: 1rem;
        align-items: start;
        transition: background .2s;
    }
    .q-row:hover { background: rgba(255,255,255,.04); }
    .q-num {
        width: 2.25rem; height: 2.25rem;
        border-radius: .5rem;
        background: rgba(99,102,241,.1);
        color: #818cf8;
        font-weight: 800;
        font-size: .85rem;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
        margin-top: .2rem;
    }
    .q-options-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: .5rem;
        margin: .75rem 0;
    }
    .q-option {
        padding: .5rem .85rem;
        background: rgba(255,255,255,.03);
        border: 1px solid var(--border);
        border-radius: .5rem;
        font-size: .85rem;
        color: var(--text-muted);
    }
    .q-answer  { font-size: .9rem; font-weight: 700; color: #34d399; margin-top: .5rem; }
    .q-explanation {
        margin-top: .75rem;
        padding: .9rem 1rem;
        background: rgba(99,102,241,.07);
        border-left: 3px solid var(--primary);
        border-radius: 0 .5rem .5rem 0;
        font-size: .85rem;
        color: var(--text-muted);
    }
    .q-img { width: 180px; border-radius: .6rem; border: 1px solid var(--border); margin-top: .75rem; }

    /* ── Inline Edit Form ────────────────────────────── */
    .edit-panel {
        display: none;
        margin-top: 1.25rem;
        padding-top: 1.25rem;
        border-top: 1px solid var(--border);
    }
    .edit-panel .form-input { margin-bottom: .75rem; }

    /* ── Add-Group Panel ─────────────────────────────── */
    .add-group-card {
        border: 2px dashed rgba(99,102,241,.25);
        border-radius: 1.25rem;
        padding: 2rem;
        margin-bottom: 2.5rem;
        background: rgba(99,102,241,.03);
    }
    .add-group-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.25rem;
    }

    /* ── Print Modal ─────────────────────────────────── */
    .print-modal {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,.85);
        backdrop-filter: blur(8px);
        z-index: 2000;
        overflow-y: auto;
        padding: 2rem;
    }
    .paper-a4 {
        background: #fff;
        color: #1a1a1a;
        font-family: 'Times New Roman', serif;
        width: 210mm;
        margin: 0 auto;
        padding: 25mm 20mm;
        min-height: 297mm;
        box-shadow: 0 25px 50px -12px rgba(0,0,0,.6);
    }
    .paper-header-box { text-align:center; border-bottom: 3px double #000; padding-bottom: 1.5rem; margin-bottom: 2rem; }
    .paper-meta-row { display:flex; justify-content:space-between; border-bottom:1px solid #000; padding-bottom:.75rem; margin-bottom:2rem; font-size:1rem; }
    .paper-group-title { font-size:1.1rem; font-weight:bold; text-decoration:underline; margin: 2rem 0 1rem; }
    .paper-q { margin-bottom:1.5rem; break-inside:avoid; }
    .paper-q-text { font-weight:600; margin-bottom:.5rem; }
    .paper-options { margin-left:2rem; }
    .paper-options li { margin-bottom:.3rem; }

    @media print {
        body * { visibility:hidden; }
        .print-modal, .print-modal * { visibility:visible; }
        .print-modal { position:absolute; top:0; left:0; background:none; padding:0; overflow:visible; }
        .paper-a4 { box-shadow:none; width:100%; padding:0; }
        .no-print { display:none!important; }
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        color: var(--text-muted);
        font-size: .875rem;
        text-decoration: none;
        font-weight: 600;
        transition: color .2s;
        margin-bottom: 1.5rem;
    }
    .back-link:hover { color: white; }
</style>
@endpush

@section('content')

{{-- Flash --}}
@if(session('success'))
    <div style="background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.25);color:#34d399;padding:1rem 1.5rem;border-radius:.9rem;margin-bottom:2rem;font-weight:600;display:flex;align-items:center;gap:.75rem;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
        {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div style="background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.25);color:#f87171;padding:1rem 1.5rem;border-radius:.9rem;margin-bottom:2rem;font-weight:600;">
        ⚠️ {{ session('error') }}
    </div>
@endif

{{-- Back link --}}
<a href="{{ route('batches.index') }}" class="back-link">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
    Semua Sesi
</a>

{{-- Page Header --}}
<div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:2.5rem;flex-wrap:wrap;gap:1rem;">
    <div>
        <h1 style="font-size:2rem;font-weight:800;letter-spacing:-.02em;">{{ $batch->subject }}</h1>
        <p style="color:var(--text-muted);margin-top:.25rem;">
            {{ $batch->teacher_name }} · {{ $batch->class_name }} · <span style="color:#818cf8;">{{ $batch->school_level }}</span>
        </p>
        <p style="color:var(--text-muted);font-size:.85rem;margin-top:.15rem;">📚 {{ $batch->topic }}</p>
    </div>
    <div style="display:flex;gap:.75rem;flex-wrap:wrap;">
        <button onclick="document.getElementById('printModal').style.display='block'" class="btn btn-secondary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9V2h12v7"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
            Pratinjau &amp; Cetak
        </button>
    </div>
</div>

{{-- ══ ADD QUESTION GROUP ══════════════════════════════════════ --}}
<div class="add-group-card">
    <h3 style="font-size:1.1rem;font-weight:700;margin-bottom:.4rem;">+ Tambah Kelompok Soal</h3>
    <p style="color:var(--text-muted);font-size:.85rem;margin-bottom:1.75rem;">
        Tambahkan satu kelompok soal ke sesi <strong>{{ $batch->subject }}</strong>.
        Kamu bisa tambah beberapa kelompok (Pilgan 20, Esai 5, dll).
    </p>

    <form action="{{ route('groups.store', $batch->id) }}" method="POST">
        @csrf
        <div class="add-group-grid">
            <div>
                <label class="form-label">Nama Kelompok</label>
                <input name="name" class="form-input" placeholder="Kelompok A" required>
            </div>
            <div>
                <label class="form-label">Tipe Soal</label>
                <select name="type" class="form-input" id="typeSelect" onchange="togglePilihanOption()">
                    <option value="pilgan">Pilihan Ganda</option>
                    <option value="isian">Isian Singkat</option>
                    <option value="esai">Esai</option>
                </select>
            </div>
            <div>
                <label class="form-label">Jumlah Soal</label>
                <input type="number" name="amount" class="form-input" value="10" min="1" max="30" required>
            </div>
            <div id="optionsCountWrap">
                <label class="form-label">Jumlah Pilihan</label>
                <select name="options_count" class="form-input">
                    <option value="3">3 Pilihan (A, B, C)</option>
                    <option value="4" selected>4 Pilihan (A, B, C, D)</option>
                    <option value="5">5 Pilihan (A, B, C, D, E)</option>
                </select>
            </div>
            <div>
                <label class="form-label">Level Bloom</label>
                <select name="cognitive_level" class="form-input">
                    <option value="C1">C1 – Mengingat</option>
                    <option value="C2">C2 – Memahami</option>
                    <option value="C3" selected>C3 – Menerapkan</option>
                    <option value="C4">C4 – Menganalisis</option>
                    <option value="C5">C5 – Mengevaluasi</option>
                    <option value="C6">C6 – Mencipta</option>
                </select>
            </div>
            <div style="display:flex;align-items:flex-end;padding-bottom:.1rem;">
                <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;font-size:.9rem;">
                    <input type="checkbox" name="with_explanation" value="1" style="width:1.1rem;height:1.1rem;accent-color:var(--primary);">
                    Sertakan Pembahasan
                </label>
            </div>
        </div>
        <div style="margin-top:1.5rem;display:flex;justify-content:flex-end;">
            <button type="submit" class="btn btn-primary">+ Tambah Kelompok</button>
        </div>
    </form>
</div>

{{-- ══ QUESTION GROUPS LIST ══════════════════════════════════════ --}}
@if($batch->questionGroups->count() > 0)
    @foreach($batch->questionGroups as $group)
        <div class="group-card">
            <div class="group-header">
                <div class="group-title">
                    <span style="font-size:1.5rem;">📋</span>
                    {{ $group->name }}
                    <span class="pill pill-indigo">{{ $group->group_label }}</span>
                    <span class="pill pill-amber">{{ $group->amount }} Soal</span>
                    @if($group->type === 'pilgan')
                        <span class="pill" style="background:rgba(16,185,129,.08);color:#86efac;">{{ $group->options_count }} Opsi</span>
                    @endif
                    <span class="pill {{ $group->status === 'done' ? 'pill-green' : 'pill-red' }}">
                        {{ $group->status === 'done' ? '✓ Done' : '○ Pending' }}
                    </span>
                </div>
                <div style="display:flex;gap:.75rem;align-items:center;">
                    <form action="{{ route('groups.generate', $group->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary" style="padding:.6rem 1.25rem;font-size:.9rem;"
                                onclick="this.disabled=true;this.innerText='Generating…';this.form.submit();">
                            🤖 {{ $group->status === 'done' ? 'Re-Generate' : 'Generate Sekarang' }}
                        </button>
                    </form>
                    <form action="{{ route('groups.destroy', $group->id) }}" method="POST" onsubmit="return confirm('Hapus kelompok ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn" style="padding:.6rem;background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.2);color:var(--danger);">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                        </button>
                    </form>
                </div>
            </div>

            {{-- Questions --}}
            @if($group->questions->count() > 0)
                @foreach($group->questions as $qi => $q)
                    <div class="q-row" id="q-{{ $q->id }}">
                        <div class="q-num">{{ $qi + 1 }}</div>
                        <div>
                            <div style="font-weight:600;font-size:1rem;line-height:1.5;">{{ $q->question_text }}</div>
                            @if($q->image_url)
                                <img src="{{ $q->image_url }}" class="q-img" alt="Visual AI">
                            @endif
                            @if($q->options)
                                <div class="q-options-grid">
                                    @foreach($q->options as $oi => $opt)
                                        <div class="q-option">{{ chr(65 + $oi) }}. {{ $opt }}</div>
                                    @endforeach
                                </div>
                            @endif
                            <div class="q-answer">✅ {{ $q->correct_answer }}</div>
                            @if($q->explanation)
                                <div class="q-explanation"><strong>💡 Pembahasan:</strong> {{ $q->explanation }}</div>
                            @endif

                            {{-- Inline Edit Form --}}
                            <div class="edit-panel" id="edit-{{ $q->id }}">
                                <form action="{{ route('questions.update', $q->id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <label class="form-label">Teks Soal</label>
                                    <textarea name="question_text" class="form-input" rows="3">{{ $q->question_text }}</textarea>
                                    <label class="form-label">Jawaban Benar</label>
                                    <input type="text" name="correct_answer" class="form-input" value="{{ $q->correct_answer }}">
                                    <label class="form-label">Pembahasan (opsional)</label>
                                    <textarea name="explanation" class="form-input" rows="2">{{ $q->explanation }}</textarea>
                                    <div style="display:flex;gap:.75rem;margin-top:1rem;">
                                        <button type="submit" class="btn btn-primary" style="padding:.6rem 1.25rem;font-size:.9rem;">Simpan</button>
                                        <button type="button" onclick="toggleEdit({{ $q->id }})" class="btn btn-secondary" style="padding:.6rem 1.25rem;font-size:.9rem;">Batal</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div style="display:flex;flex-direction:column;gap:.5rem;flex-shrink:0;">
                            <button onclick="toggleEdit({{ $q->id }})" class="btn btn-secondary" style="padding:.5rem;">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4L18.5 2.5z"/></svg>
                            </button>
                            <form action="{{ route('questions.destroy', $q->id) }}" method="POST" onsubmit="return confirm('Hapus soal?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn" style="padding:.5rem;background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.2);color:var(--danger);">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            @else
                <div style="text-align:center;padding:3rem;color:var(--text-muted);border:1px dashed var(--border);border-radius:.75rem;">
                    <p>Soal belum digenerate. Klik tombol <strong>"Generate Sekarang"</strong>.</p>
                </div>
            @endif
        </div>
    @endforeach
@else
    <div style="text-align:center;padding:5rem;color:var(--text-muted);border:1px dashed var(--border);border-radius:1.25rem;">
        <div style="font-size:3rem;margin-bottom:1rem;">📁</div>
        <p>Belum ada kelompok soal. Tambahkan kelompok di atas.</p>
    </div>
@endif

@endsection

@section('modals')
{{-- Print Modal --}}
<div id="printModal" class="print-modal">
    <div class="no-print" style="position:sticky;top:0;z-index:10;display:flex;justify-content:flex-end;gap:1rem;padding:1rem;background:rgba(0,0,0,.5);">
        <button onclick="window.print()" class="btn btn-primary">🖨️ Cetak ke PDF</button>
        <button onclick="document.getElementById('printModal').style.display='none'" class="btn btn-secondary">Tutup</button>
    </div>

    <div class="paper-a4">
        {{-- Kop Surat --}}
        <div class="paper-header-box">
            <h1 style="font-size:1.6rem;letter-spacing:.05em;text-transform:uppercase;">Lembar Evaluasi Siswa</h1>
            <p style="font-size:1.1rem;font-weight:bold;">{{ $batch->school_name }}</p>
            <p style="font-size:.95rem;">Tahun Pelajaran 2024/2025</p>
        </div>

        <div class="paper-meta-row">
            <div><strong>Mata Pelajaran :</strong> {{ $batch->subject }}</div>
            <div><strong>Topik :</strong> {{ $batch->topic }}</div>
            <div><strong>Kelas :</strong> {{ $batch->class_name }}</div>
        </div>
        <div class="paper-meta-row">
            <div><strong>Guru :</strong> {{ $batch->teacher_name }}</div>
            <div><strong>Jenjang :</strong> {{ $batch->school_level }}</div>
            <div><strong>Hari/Tgl :</strong> .........................................</div>
        </div>
        <div style="display:flex;justify-content:space-between;margin-bottom:2.5rem;font-size:1rem;">
            <div>Nama Siswa: ........................................</div>
            <div>No. Absen: ..............</div>
        </div>

        @if($batch->material_scope)
            <div style="border:1px solid #000;padding:1rem;margin-bottom:2rem;font-size:.9rem;">
                <strong>Batasan Materi:</strong> {{ $batch->material_scope }}
            </div>
        @endif

        {{-- Kelompok Soal --}}
        @foreach($batch->questionGroups as $group)
            @if($group->questions->count() > 0)
                <div class="paper-group-title">{{ strtoupper($group->name) }} – {{ strtoupper($group->group_label) }}</div>
                @foreach($group->questions as $qi => $q)
                    <div class="paper-q">
                        <div class="paper-q-text">{{ $qi + 1 }}. {{ $q->question_text }}</div>
                        @if($q->image_url)
                            <img src="{{ $q->image_url }}" style="max-width:220px;display:block;margin:1rem 0;border:1px solid #ccc;filter:grayscale(100%);">
                        @endif
                        @if($q->options)
                            <ul class="paper-options" style="list-style:none;">
                                @foreach($q->options as $oi => $opt)
                                    <li>{{ chr(65 + $oi) }}. {{ $opt }}</li>
                                @endforeach
                            </ul>
                        @else
                            <div style="height:50px;border-bottom:1px dotted #ccc;margin:1rem 0;"></div>
                        @endif
                    </div>
                @endforeach
            @endif
        @endforeach
    </div>
</div>
@endsection

@push('scripts')
<script>
    function toggleEdit(id) {
        const el = document.getElementById('edit-' + id);
        el.style.display = (el.style.display === 'none' || !el.style.display) ? 'block' : 'none';
    }

    function togglePilihanOption() {
        const type = document.getElementById('typeSelect').value;
        document.getElementById('optionsCountWrap').style.display = type === 'pilgan' ? 'block' : 'none';
    }

    togglePilihanOption(); // Init on load
</script>
@endpush
