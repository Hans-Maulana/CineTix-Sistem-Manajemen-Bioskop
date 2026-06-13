@php
    $rating = round($film->reviews->avg('rating') ?? (float) ($film->rating ?? 0), 1);
    $status = $film->status ?? 'now_playing';
    $statusLabel = match ($status) {
        'now_playing' => 'Sedang Tayang',
        'coming_soon' => 'Segera Tayang',
        default => ucfirst(str_replace('_', ' ', $status)),
    };
    $isNowPlaying = $status === 'now_playing';
    $rank = $rank ?? null;
    $ticketsSold = $ticketsSold ?? ($film->tickets_sold ?? null);
    $rankClass = match ((int) $rank) {
        1 => 'rank-1',
        2 => 'rank-2',
        3 => 'rank-3',
        default => 'rank-x',
    };
@endphp

<div class="cx-film-card {{ $rank ? 'cx-film-card--ranked' : '' }}" data-aos="fade-up" data-aos-duration="800">
    @if($rank)
        <span class="cx-rank-badge {{ $rankClass }}" aria-label="Peringkat {{ $rank }}">{{ $rank }}</span>
    @endif
    <div class="cx-film-poster">
        <img src="{{ $film->cover_url }}" alt="{{ $film->title }}" loading="lazy">
        @if($film->classification)
            <span class="cx-classification">{{ strtoupper($film->classification) }}</span>
        @endif
        <div class="cx-poster-overlay">
            <div class="cx-poster-bottom">
                <span class="cx-status-badge {{ $isNowPlaying ? 'cx-status-now' : 'cx-status-soon' }}">
                    <iconify-icon icon="{{ $isNowPlaying ? 'lucide:play' : 'lucide:clock' }}"></iconify-icon>
                    {{ $statusLabel }}
                </span>
                @if($rating > 0)
                    <span class="cx-rating">
                        <iconify-icon icon="lucide:star"></iconify-icon>
                        {{ number_format($rating, 1) }}
                    </span>
                @endif
            </div>
        </div>
        <a href="{{ route('films.detail', $film) }}" class="cx-poster-link" aria-label="Detail {{ $film->title }}"></a>
    </div>

    <div class="cx-film-body">
        <h3 class="cx-film-title" title="{{ $film->title }}">{{ $film->title }}</h3>

        <div class="cx-film-meta">
            @if($film->duration)
                <span><iconify-icon icon="lucide:clock"></iconify-icon>{{ $film->duration }} mnt</span>
            @endif
            @if($ticketsSold !== null && $ticketsSold > 0)
                <span><iconify-icon icon="lucide:ticket"></iconify-icon>{{ number_format($ticketsSold, 0, ',', '.') }} tiket</span>
            @endif
        </div>

        <div class="cx-film-genres">
            @forelse($film->genres->take(3) as $genre)
                <span class="cx-genre-tag">{{ $genre->genre_name }}</span>
            @empty
                <span class="cx-genre-tag cx-genre-muted">Film</span>
            @endforelse
            @if($film->genres->count() > 3)
                <span class="cx-genre-tag">+{{ $film->genres->count() - 3 }}</span>
            @endif
        </div>

        <div class="cx-film-actions">
            @if($isNowPlaying)
                <a href="{{ route('films.detail', $film) }}" class="cx-btn-book">
                    <iconify-icon icon="lucide:ticket"></iconify-icon>
                    Pesan Sekarang
                </a>
            @else
                <a href="{{ route('films.detail', $film) }}" class="cx-btn-book cx-btn-outline">
                    <iconify-icon icon="lucide:eye"></iconify-icon>
                    Lihat Detail
                </a>
            @endif
        </div>
    </div>
</div>
