@extends('layouts.app')

@section('title', 'APAL AI – Dashboard Sesi')

@push('styles')
<style>
    .batch-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 1.5rem;
        margin-top: 2rem;
    }
    .batch-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 1.25rem;
        padding: 1.75rem;
        text-decoration: none;
        color: inherit;
        display: block;
        transition: all .25s;
        position: relative;
        overflow: hidden;
    }
    .batch-card::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(99,102,241,.06) 0%, transparent 60%);
        opacity: 0;
        transition: opacity .25s;
    }
    .batch-card:hover { border-color: rgba(99,102,241,.4); transform: translateY(-3px); box-shadow: 0 16px 32px -8px rgba(0,0,0,.4); }
    .batch-card:hover::before { opacity: 1; }

    .batch-subject {
        font-size: 1.15rem;
        font-weight: 800;
        margin-bottom: .35rem;
        letter-spacing: -.01em;
    }
    .batch-topic {
        font-size: .875rem;
        color: var(--text-muted);
        margin-bottom: 1.25rem;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .batch-meta {
        display: flex;
        gap: .65rem;
        flex-wrap: wrap;
    }
    .meta-chip {
        padding: .25rem .7rem;
        border-radius: .45rem;
        font-size: .7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .05em;
    }
    .chip-indigo { background: rgba(99,102,241,.12); color: #818cf8; }
    .chip-green  { background: rgba(16,185,129,.12);  color: #34d399; }
    .chip-amber  { background: rgba(245,158,11,.12);  color: #fbbf24; }

    .batch-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 1.25rem;
        padding-top: 1.25rem;
        border-top: 1px solid var(--border);
        font-size: .8rem;
        color: var(--text-muted);
    }

    .new-btn-hero {
        display: inline-flex;
        align-items: center;
        gap: .6rem;
        padding: .9rem 1.75rem;
        border-radius: .9rem;
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: white;
        font-weight: 700;
        font-size: 1rem;
        text-decoration: none;
        transition: all .2s;
        box-shadow: 0 4px 24px -4px rgba(99,102,241,.4);
    }
    .new-btn-hero:hover { transform: translateY(-2px); box-shadow: 0 10px 32px -4px rgba(99,102,241,.5); }

    .empty-state {
        text-align: center;
        padding: 8rem 0;
        color: var(--text-muted);
    }
    .empty-icon { font-size: 4rem; margin-bottom: 1.5rem; }
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

{{-- Header --}}
<div style="display:flex;justify-content:space-between;align-items:flex-end;flex-wrap:wrap;gap:1.25rem;margin-bottom:2.5rem;">
    <div>
        <h1 style="font-size:2rem;font-weight:800;letter-spacing:-.02em;">Dashboard Sesi</h1>
        <p style="color:var(--text-muted);margin-top:.3rem;">Pilih sesi yang ada atau buat sesi evaluasi baru.</p>
    </div>
    <a href="{{ route('batches.create') }}" class="new-btn-hero">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Buat Sesi Baru
    </a>
</div>

{{-- Batch Grid --}}
@forelse($batches as $batch)
    @if($loop->first)
        <div class="batch-grid">
    @endif

    <a href="{{ route('batches.show', $batch->id) }}" class="batch-card">
        <div class="batch-subject">{{ $batch->subject }}</div>
        <div class="batch-topic">{{ $batch->topic }}</div>
        <div class="batch-meta">
            <span class="meta-chip chip-indigo">{{ $batch->school_level }}</span>
            <span class="meta-chip chip-amber">{{ $batch->class_name }}</span>
            @php $groupCount = $batch->questionGroups()->count(); @endphp
            <span class="meta-chip chip-green">{{ $groupCount }} Kelompok</span>
        </div>
        <div class="batch-footer">
            <span>👤 {{ $batch->teacher_name }}</span>
            <span>{{ $batch->created_at->diffForHumans() }}</span>
        </div>
    </a>

    @if($loop->last)
        </div>
    @endif

@empty
    <div class="empty-state">
        <div class="empty-icon">📋</div>
        <h3 style="font-size:1.25rem;font-weight:700;margin-bottom:.75rem;">Belum Ada Sesi</h3>
        <p style="margin-bottom:2rem;">Buat sesi evaluasi pertamamu dan mulai generate soal dengan AI.</p>
        <a href="{{ route('batches.create') }}" class="new-btn-hero">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Buat Sesi Pertama
        </a>
    </div>
@endforelse

@endsection
