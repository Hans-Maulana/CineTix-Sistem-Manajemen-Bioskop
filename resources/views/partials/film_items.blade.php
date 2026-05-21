@forelse($nowPlayingFilms as $film)
  <div class="item">
    <div class="portfolio d-flex flex-column gap-6">
      <div class="portfolio-img position-relative overflow-hidden">
        <img src="{{ $film->cover_url }}" alt="{{ $film->title }}"
          class="img-fluid w-100 object-fit-cover shadow-sm rounded-3" style="aspect-ratio: 2/3;">
        <div class="portfolio-overlay">
          <a href="{{ route('films.detail', $film) }}"
            class="position-absolute top-50 start-50 translate-middle bg-primary round-64 rounded-circle hstack justify-content-center">
            <iconify-icon icon="lucide:arrow-up-right" class="fs-8 text-dark"></iconify-icon>
          </a>
        </div>
      </div>
      <div class="portfolio-details d-flex flex-column gap-3">
        <h3 class="mb-0">{{ $film->title }}</h3>
        <div class="hstack gap-2">
          @foreach($film->genres as $genre)
            <span class="badge text-dark border">{{ $genre->genre_name }}</span>
          @endforeach
        </div>
      </div>
    </div>
  </div>
@empty
  <div class="w-100 text-center py-5">
    <p class="fs-5 text-muted mb-0">Tidak ada film yang sedang tayang saat ini.</p>
  </div>
@endforelse
