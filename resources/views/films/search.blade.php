@extends('layouts.app')

@section('content')
<section class="py-5 bg-light-gray min-vh-100">
    <div class="container">
        <!-- Search Header -->
        <div class="row mb-5" data-aos="fade-up">
            <div class="col-lg-8">
                <h1 class="display-6 fw-bold mb-2">Cari Film Favorit Anda</h1>
                <p class="text-muted">Jelajahi koleksi film terbaru dan pesan tiket dengan mudah.</p>
            </div>
            <div class="col-lg-4 d-flex align-items-center">
                <form action="{{ route('films.search') }}" method="GET" class="w-100">
                    <div class="input-group shadow-sm rounded-pill overflow-hidden bg-white">
                        <span class="input-group-text bg-white border-0 ps-4">
                            <iconify-icon icon="lucide:search" class="text-muted"></iconify-icon>
                        </span>
                        <input type="text" name="q" value="{{ $query }}" class="form-control border-0 py-3 ps-2" placeholder="Judul film, genre, atau aktor...">
                        <button class="btn btn-primary px-4 py-3" type="submit">Cari</button>
                    </div>
                </form>
            </div>
        </div>

        <div id="search-results-section">
            @if($query)
                <div class="mb-4" data-aos="fade-in">
                    <h5 class="text-muted">Hasil pencarian untuk: <span class="text-primary fw-bold">"{{ $query }}"</span></h5>
                </div>
            @endif

            @if($films->count() > 0)
                <div class="row g-4">
                    @foreach($films as $film)
                        <div class="col-sm-6 col-md-4 col-lg-3" data-aos="fade-up" data-aos-delay="{{ ($loop->index % 4) * 100 }}">
                            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden film-card">
                                <div class="position-relative">
                                    <img src="{{ $film->cover_url }}" class="card-img-top" alt="{{ $film->title }}" style="aspect-ratio: 2/3; object-fit: fill;">
                                    <div class="card-img-overlay d-flex flex-column justify-content-end p-0">
                                        <div class="bg-dark bg-opacity-75 text-white p-3 backdrop-blur">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="badge bg-primary">{{ $film->classification }}</span>
                                                <div class="hstack gap-1 text-warning small">
                                                    <iconify-icon icon="solar:star-bold"></iconify-icon>
                                                    <span>{{ number_format($film->reviews_avg_rating ?? 0, 1) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body p-4">
                                    <h5 class="card-title fw-bold mb-1 text-truncate" title="{{ $film->title }}">{{ $film->title }}</h5>
                                    <p class="card-text text-muted small mb-3">
                                        @foreach($film->genres as $genre)
                                            {{ $genre->genre_name }}{{ !$loop->last ? ', ' : '' }}
                                        @endforeach
                                    </p>
                                    <a href="{{ route('films.detail', $film) }}" class="btn btn-outline-primary rounded-pill w-100 fw-bold">Lihat Detail</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-10" data-aos="fade-up">
                    {{ $films->appends(['q' => $query])->links() }}
                </div>
            @else
                <div class="text-center py-10" data-aos="zoom-in">
                    <iconify-icon icon="solar:videocamera-off-broken" class="display-1 text-muted opacity-25 mb-4"></iconify-icon>
                    <h3 class="fw-bold">Film tidak ditemukan</h3>
                    <p class="text-muted mb-4">Maaf, kami tidak menemukan film yang sesuai dengan kriteria Anda.</p>
                    <a href="{{ route('films.search') }}" class="btn btn-primary rounded-pill px-5">Lihat Semua Film</a>
                </div>
            @endif
        </div>
    </div>
</section>

<style>
    .film-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .film-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 1rem 3rem rgba(0,0,0,0.1) !important;
    }
    .backdrop-blur {
        backdrop-filter: blur(5px);
    }
    .mt-10 {
        margin-top: 5rem;
    }
    .bg-light-gray {
        background-color: #f8f9fa;
    }
</style>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const searchInput = document.querySelector('input[name="q"]');
        if (searchInput) {
            let searchTimeout;
            searchInput.setAttribute('autocomplete', 'off');

            const inputGroup = searchInput.closest('.input-group');
            if (inputGroup) {
                const toggleClearBtn = () => {
                    let btn = inputGroup.querySelector('.btn-clear-js');
                    if (searchInput.value.trim() !== '') {
                        if (!btn) {
                            btn = document.createElement('button');
                            btn.type = 'button';
                            btn.className = 'btn btn-light border-0 d-flex align-items-center btn-clear-js px-3';
                            btn.innerHTML = '<iconify-icon icon="lucide:x" class="text-muted"></iconify-icon>';
                            btn.style.zIndex = '5';
                            // Insert before the submit button
                            const submitBtn = inputGroup.querySelector('button[type="submit"]');
                            inputGroup.insertBefore(btn, submitBtn);
                            btn.addEventListener('click', clearAndSearch);
                        } else {
                            btn.style.display = 'flex';
                        }
                    } else {
                        if (btn) {
                            btn.remove();
                        }
                    }
                };

                function clearAndSearch() {
                    searchInput.value = '';
                    toggleClearBtn();
                    searchInput.dispatchEvent(new Event('input'));
                    searchInput.focus();
                }

                searchInput.addEventListener('input', toggleClearBtn);
                toggleClearBtn(); // Run immediately on load
            }

            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                const query = this.value;
                const form = this.closest('form');
                const url = new URL(form.action || window.location.href);
                url.searchParams.set('q', query);
                url.searchParams.set('page', 1);

                const searchIconSpan = inputGroup ? inputGroup.querySelector('.input-group-text') : null;

                searchTimeout = setTimeout(() => {
                    if (searchIconSpan) {
                        searchIconSpan.innerHTML = '<div class="spinner-border spinner-border-sm text-primary" role="status" style="width: 1.15rem; height: 1.15rem; border-width: 0.15em;"></div>';
                    }

                    fetch(url)
                        .then(response => response.text())
                        .then(html => {
                            const parser = new DOMParser();
                            const doc = parser.parseFromString(html, 'text/html');
                            
                            const newResults = doc.getElementById('search-results-section');
                            const oldResults = document.getElementById('search-results-section');
                            if (newResults && oldResults) {
                                oldResults.innerHTML = newResults.innerHTML;
                                
                                // Reinitialize AOS if it's imported
                                if (typeof AOS !== 'undefined') {
                                    AOS.refresh();
                                }
                            }
                            
                            // Update URL in address bar without reload
                            window.history.pushState({ path: url.href }, '', url.href);
                        })
                        .catch(err => {
                            console.error('Error fetching search results:', err);
                        })
                        .finally(() => {
                            if (searchIconSpan) {
                                searchIconSpan.innerHTML = '<iconify-icon icon="lucide:search" class="text-muted"></iconify-icon>';
                            }
                        });
                }, 250); // 250ms debounce
            });

            // Intercept pagination clicks in customer search results for AJAX loading
            const resultsSection = document.getElementById('search-results-section');
            if (resultsSection) {
                resultsSection.addEventListener('click', function(e) {
                    const pageLink = e.target.closest('.pagination a, a.page-link');
                    if (pageLink) {
                        e.preventDefault();
                        const url = new URL(pageLink.href);
                        
                        resultsSection.style.opacity = '0.4';
                        resultsSection.style.transition = 'opacity 0.15s ease';
                        
                        fetch(url)
                            .then(response => response.text())
                            .then(html => {
                                const parser = new DOMParser();
                                const doc = parser.parseFromString(html, 'text/html');
                                const newResults = doc.getElementById('search-results-section');
                                if (newResults) {
                                    resultsSection.innerHTML = newResults.innerHTML;
                                    resultsSection.style.opacity = '1';
                                    window.scrollTo({ top: resultsSection.offsetTop - 120, behavior: 'smooth' });
                                    if (typeof AOS !== 'undefined') {
                                        AOS.refresh();
                                    }
                                }
                                window.history.pushState({ path: url.href }, '', url.href);
                            })
                            .catch(err => {
                                console.error('Error fetching paginated search results:', err);
                                resultsSection.style.opacity = '1';
                            });
                    }
                });
            }
        }
    });
</script>
@endpush
