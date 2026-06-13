@forelse($nowPlayingFilms as $film)
    @include('partials.customer_film_card', ['film' => $film])
@empty
    <div class="cx-empty">
        <iconify-icon icon="lucide:film"></iconify-icon>
        <p class="mb-0 fw-semibold">Tidak ada film yang sedang tayang saat ini.</p>
    </div>
@endforelse
