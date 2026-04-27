@extends('layouts.app')

@section('title', 'APAL AI – Buat Sesi Baru')

@push('styles')
<style>
    .create-wrap {
        max-width: 800px;
        margin: 0 auto;
    }
    .form-grid-3 {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.25rem;
    }
    .span-full { grid-column: 1 / -1; }

    .step-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2.2rem; height: 2.2rem;
        border-radius: .6rem;
        background: rgba(99,102,241,.15);
        color: var(--primary-light);
        font-weight: 800;
        font-size: .95rem;
        margin-right: .6rem;
        flex-shrink: 0;
    }
    .section-row {
        display: flex;
        align-items: center;
        font-size: 1rem;
        font-weight: 700;
        color: var(--text-muted);
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid var(--border);
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
        margin-bottom: 2rem;
    }
    .back-link:hover { color: white; }
</style>
@endpush

@section('content')

<div class="create-wrap">

    <a href="{{ route('batches.index') }}" class="back-link">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
        Kembali ke Dashboard
    </a>

    <h1 style="font-size:2rem;font-weight:800;letter-spacing:-.02em;margin-bottom:.35rem;">Buat Sesi Evaluasi Baru</h1>
    <p style="color:var(--text-muted);margin-bottom:2.5rem;">Isi informasi sesi terlebih dahulu. Setelah sesi dibuat, kamu bisa menambahkan kelompok soal.</p>

    <div class="glass-card">
        <div class="section-row">
            <span class="step-badge">1</span>
            Identitas Sekolah & Guru
        </div>

        <form action="{{ route('batches.store') }}" method="POST">
            @csrf
            <div class="form-grid-3">
                <div>
                    <label class="form-label">Nama Sekolah</label>
                    <input name="school_name" class="form-input" placeholder="SMA Negeri 1 Malang"
                           value="{{ old('school_name') }}" required>
                    @error('school_name') <p style="color:#f87171;font-size:.8rem;margin-top:.4rem;">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">Kelas / Rombel</label>
                    <input name="class_name" class="form-input" placeholder="XI IPA 3"
                           value="{{ old('class_name') }}" required>
                    @error('class_name') <p style="color:#f87171;font-size:.8rem;margin-top:.4rem;">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">Nama Guru</label>
                    <input name="teacher_name" class="form-input" placeholder="Budi Santoso, S.Pd"
                           value="{{ old('teacher_name') }}" required>
                    @error('teacher_name') <p style="color:#f87171;font-size:.8rem;margin-top:.4rem;">{{ $message }}</p> @enderror
                </div>
            </div>

            <div style="margin-top:2rem;padding-top:2rem;border-top:1px solid var(--border);">
                <div class="section-row">
                    <span class="step-badge">2</span>
                    Mata Pelajaran & Materi
                </div>
                <div class="form-grid-3">
                    <div>
                        <label class="form-label">Mata Pelajaran</label>
                        <input name="subject" class="form-input" placeholder="Matematika"
                               value="{{ old('subject') }}" required>
                        @error('subject') <p style="color:#f87171;font-size:.8rem;margin-top:.4rem;">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="form-label">Topik Materi</label>
                        <input name="topic" class="form-input" placeholder="Integral Tentu"
                               value="{{ old('topic') }}" required>
                        @error('topic') <p style="color:#f87171;font-size:.8rem;margin-top:.4rem;">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="form-label">Jenjang</label>
                        <select name="school_level" class="form-input">
                            <option value="SD"  {{ old('school_level') === 'SD'  ? 'selected' : '' }}>SD</option>
                            <option value="SMP" {{ old('school_level') === 'SMP' ? 'selected' : '' }}>SMP</option>
                            <option value="SMA" {{ old('school_level', 'SMA') === 'SMA' ? 'selected' : '' }}>SMA</option>
                        </select>
                    </div>
                    <div class="span-full">
                        <label class="form-label">Batasan Materi <span style="font-weight:400;opacity:.6;">(opsional)</span></label>
                        <textarea name="material_scope" class="form-input" rows="3"
                                  placeholder="Contoh: Bab 5 – Integral hal. 120-145. Tidak mencakup integral parsial.">{{ old('material_scope') }}</textarea>
                    </div>
                </div>
            </div>

            <div style="margin-top:2rem;padding-top:1.5rem;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:1rem;">
                <a href="{{ route('batches.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    Buat Sesi &amp; Lanjut ke Soal →
                </button>
            </div>
        </form>
    </div>

</div>

@endsection
