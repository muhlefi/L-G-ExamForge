<aside class="sidebar">
    <div class="logo">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary-light)"><path d="M12 2L2 7l10 5 10-5-10-5z"></path><path d="M2 17l10 5 10-5"></path><path d="M2 12l10 5 10-5"></path></svg>
        APAL AI
    </div>

    <div class="sidebar-title">Riwayat Sesi</div>
    <ul class="nav-list">
        @forelse($batches as $batch)
            <li class="nav-item">
                <a href="?batch_id={{ $batch->id }}" class="nav-link {{ (isset($currentBatch) && $currentBatch->id == $batch->id) ? 'active' : '' }}">
                    <div style="font-weight: 700; font-size: 0.85rem; margin-bottom: 2px;">{{ $batch->subject }}</div>
                    <div style="font-size: 0.75rem; opacity: 0.6;">{{ $batch->topic }}</div>
                    <div style="font-size: 0.65rem; opacity: 0.4; margin-top: 4px;">{{ $batch->created_at->diffForHumans() }}</div>
                </a>
            </li>
        @empty
            <li style="text-align: center; color: var(--text-muted); font-size: 0.8rem; padding: 2rem 0;">
                Belum ada sesi
            </li>
        @endforelse
    </ul>

    <div style="margin-top: 2rem; border-top: 1px solid var(--border); padding-top: 2rem;">
        <a href="#" class="nav-link" style="opacity: 0.6;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20v-6M9 20v-10M6 20v-4M15 20v-8M18 20v-12"></path></svg>
            Statistik Penggunaan
        </a>
    </div>
</aside>
