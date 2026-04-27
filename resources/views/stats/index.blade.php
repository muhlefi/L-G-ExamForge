@extends('layouts.app')

@section('title', 'APAL AI - Statistik Penggunaan')

@push('styles')
<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .stat-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 1rem;
        padding: 1.25rem;
    }
    .stat-label {
        color: var(--text-muted);
        font-size: .8rem;
        margin-bottom: .5rem;
        text-transform: uppercase;
        letter-spacing: .05em;
    }
    .stat-value {
        font-size: 1.65rem;
        font-weight: 800;
        letter-spacing: -.02em;
    }
    .panel-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
        margin-bottom: 1rem;
    }
    .panel-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 1rem;
        padding: 1.25rem;
    }
    .panel-title {
        font-weight: 700;
        margin-bottom: 1rem;
    }
    .row-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: .6rem 0;
        border-bottom: 1px solid var(--border);
        font-size: .92rem;
    }
    .row-item:last-child { border-bottom: none; }
    .muted { color: var(--text-muted); }
    .badge {
        background: rgba(99, 102, 241, .14);
        color: #a5b4fc;
        border-radius: .5rem;
        padding: .2rem .6rem;
        font-size: .75rem;
        font-weight: 700;
    }
    @media (max-width: 1100px) {
        .stats-grid { grid-template-columns: repeat(2, 1fr); }
        .panel-grid { grid-template-columns: 1fr; }
    }
    @media (max-width: 640px) {
        .stats-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
<div style="margin-bottom:1.5rem;">
    <h1 style="font-size:2rem;font-weight:800;letter-spacing:-.02em;">Statistik Penggunaan</h1>
    <p class="muted" style="margin-top:.35rem;">Ringkasan aktivitas sesi, kelompok soal, dan hasil generasi soal AI.</p>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">Total Sesi</div>
        <div class="stat-value">{{ number_format($totals['batches']) }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total Kelompok</div>
        <div class="stat-value">{{ number_format($totals['groups']) }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total Soal</div>
        <div class="stat-value">{{ number_format($totals['questions']) }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Kelompok Done</div>
        <div class="stat-value" style="color:#34d399;">{{ number_format($totals['groups_done']) }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Kelompok Pending</div>
        <div class="stat-value" style="color:#f87171;">{{ number_format($totals['groups_pending']) }}</div>
    </div>
</div>

<div class="panel-grid">
    <div class="panel-card">
        <div class="panel-title">Distribusi Tipe Soal</div>
        @forelse($questionTypeBreakdown as $type => $total)
            <div class="row-item">
                <span class="muted">{{ strtoupper($type) }}</span>
                <span class="badge">{{ number_format($total) }}</span>
            </div>
        @empty
            <p class="muted">Belum ada data soal.</p>
        @endforelse
    </div>

    <div class="panel-card">
        <div class="panel-title">Distribusi Level Bloom</div>
        @forelse($cognitiveBreakdown as $level => $total)
            <div class="row-item">
                <span class="muted">{{ strtoupper($level) }}</span>
                <span class="badge">{{ number_format($total) }}</span>
            </div>
        @empty
            <p class="muted">Belum ada data level kognitif.</p>
        @endforelse
    </div>
</div>

<div class="panel-grid">
    <div class="panel-card">
        <div class="panel-title">Top Mata Pelajaran</div>
        @forelse($topSubjects as $item)
            <div class="row-item">
                <span class="muted">{{ $item->subject }}</span>
                <span class="badge">{{ number_format($item->total) }}</span>
            </div>
        @empty
            <p class="muted">Belum ada data mata pelajaran.</p>
        @endforelse
    </div>

    <div class="panel-card">
        <div class="panel-title">5 Sesi Terbaru</div>
        @forelse($recentBatches as $batch)
            <div class="row-item">
                <div>
                    <div style="font-weight:600;">{{ $batch->subject }} - {{ $batch->topic }}</div>
                    <div class="muted" style="font-size:.8rem;">{{ $batch->created_at->diffForHumans() }}</div>
                </div>
                <span class="badge">{{ $batch->question_groups_count }}G / {{ $batch->questions_count }}Q</span>
            </div>
        @empty
            <p class="muted">Belum ada sesi.</p>
        @endforelse
    </div>
</div>
@endsection
