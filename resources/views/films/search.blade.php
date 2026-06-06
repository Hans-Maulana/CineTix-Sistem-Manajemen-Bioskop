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
                        <button class="btn btn-primary text-white px-4 py-3" type="submit">Cari</button>
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
                            <div class="portfolio d-flex flex-column gap-6">
                                <div class="portfolio-img position-relative overflow-hidden">
                                    <img src="{{ $film->cover_url }}" alt="{{ $film->title }}"
                                        class="img-fluid w-100 shadow-sm rounded-3" style="aspect-ratio: 2/3; object-fit: fill;">
                                    <div class="portfolio-overlay">
                                        <a href="{{ route('films.detail', $film) }}" class="btn-detail-playing">
                                            <iconify-icon icon="lucide:ticket" class="fs-5"></iconify-icon>
                                            <span>Pesan Tiket</span>
                                        </a>
                                    </div>
                                </div>
                                <div class="portfolio-details d-flex flex-column gap-3">
                                    <h3 class="mb-0 text-truncate fs-5 fw-bold" title="{{ $film->title }}">{{ $film->title }}</h3>
                                    <div class="d-flex align-items-center justify-content-between gap-2 mt-1">
                                        <div class="hstack gap-2 flex-wrap">
                                            @foreach($film->genres as $genre)
                                                <span class="badge text-dark border">{{ $genre->genre_name }}</span>
                                            @endforeach
                                        </div>
                                        <a href="{{ route('films.detail', $film) }}" class="btn-pesan flex-shrink-0">
                                            <iconify-icon icon="lucide:ticket" class="fs-5"></iconify-icon>
                                            <span>Pesan Sekarang</span>
                                        </a>
                                    </div>
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
    /* Portfolio Layout from Landing Page */
    .portfolio {
        display: flex;
        flex-direction: column;
        gap: 1.5rem; /* Matches gap-6 */
    }
    .portfolio-img {
        position: relative;
        overflow: hidden;
        border-radius: 0.5rem; /* Matches rounded-3 */
        box-shadow: 0 .125rem .25rem rgba(0,0,0,.075) !important; /* Matches shadow-sm */
    }
    .portfolio-img > img {
        object-fit: fill !important; /* Stretches the image to fill the box completely without cropping */
        transition: transform 0.5s ease;
    }
    .portfolio-img:hover > img {
        transform: scale(1.05);
    }
    .portfolio-img .portfolio-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(26, 25, 83, 0.85) !important; /* CineTix deep purple-blue with transparency */
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease, visibility 0.3s ease;
        z-index: 2;
        transform: none !important;
    }
    .portfolio-img:hover .portfolio-overlay {
        opacity: 1 !important;
        visibility: visible !important;
    }
    .portfolio-overlay .btn-detail-playing {
        background: #ffffff;
        color: #1A1953;
        font-weight: 700;
        padding: 10px 20px;
        border-radius: 50px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        transition: all 0.3s ease;
        transform: translateY(15px);
    }
    .portfolio-img:hover .portfolio-overlay .btn-detail-playing {
        transform: translateY(0);
    }
    .portfolio-overlay .btn-detail-playing:hover {
        background: #1A1953;
        color: #ffffff;
        transform: scale(1.05);
    }

    /* Pesan Button Styling */
    .btn-pesan {
        background-color: #1A1953;
        color: #ffffff;
        font-weight: 700;
        font-size: 0.8rem;
        padding: 6px 14px;
        border-radius: 50px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.3s ease;
        border: 1px solid #1A1953;
    }
    .btn-pesan:hover {
        background-color: #ffffff;
        color: #1A1953;
        border-color: #1A1953;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(26, 25, 83, 0.2);
    }
    .btn-pesan iconify-icon {
        font-size: 0.95rem;
    }

    .portfolio-details h3 {
        font-size: 1.15rem !important;
        font-weight: 700 !important;
        margin-bottom: 8px !important;
        color: #1F2A2E !important;
    }

    .mt-10 {
        margin-top: 5rem;
    }
    .bg-light-gray {
        background-color: #f5f7fb;
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
