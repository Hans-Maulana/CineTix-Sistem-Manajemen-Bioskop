<style>
    :root {
        --cx-primary: #1A1953;
        --cx-primary-light: #2d2b7a;
        --cx-accent: #d4b06a;
        --cx-surface: #ffffff;
        --cx-muted: #8a93a6;
        --cx-border: rgba(26, 25, 83, 0.08);
    }

    .cx-section {
        padding: 3.5rem 0;
    }
    .cx-section-alt {
        background: #eef0f7;
    }

    .cx-section-header {
        margin-bottom: 2rem;
    }
    .cx-section-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: var(--cx-primary);
        background: rgba(26, 25, 83, 0.07);
        padding: 6px 14px;
        border-radius: 999px;
        margin-bottom: 12px;
    }
    .cx-section-title {
        font-size: clamp(1.75rem, 3vw, 2.25rem);
        font-weight: 800;
        color: #1f2533;
        margin-bottom: 0.5rem;
    }
    .cx-section-desc {
        color: var(--cx-muted);
        font-size: 1rem;
        max-width: 620px;
        margin-bottom: 0;
    }

    .cx-hero-panel {
        background: linear-gradient(120deg, #1A1953 0%, #2d2b7a 60%, #3a37a0 100%);
        border-radius: 24px;
        color: #fff;
        padding: 28px 32px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 18px 40px rgba(26, 25, 83, 0.2);
        margin-bottom: 2rem;
    }
    .cx-hero-panel::after {
        content: "";
        position: absolute;
        right: -40px; top: -40px;
        width: 180px; height: 180px;
        background: rgba(212, 176, 106, 0.18);
        border-radius: 50%;
    }
    .cx-hero-panel h2 {
        font-weight: 800;
        margin-bottom: 6px;
        position: relative;
        z-index: 1;
        color: #ffffff !important;
    }
    .cx-hero-panel p {
        color: rgba(255, 255, 255, 0.82) !important;
        margin-bottom: 0;
        position: relative;
        z-index: 1;
    }
    .cx-hero-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(255, 255, 255, 0.14) !important;
        color: #ffffff !important;
        border: 1px solid rgba(255, 255, 255, 0.28);
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        padding: 6px 14px;
        border-radius: 999px;
        margin-bottom: 10px;
        position: relative;
        z-index: 1;
    }
    .cx-hero-btn {
        background: #ffffff !important;
        color: #1A1953 !important;
        border: none !important;
        font-weight: 700;
        position: relative;
        z-index: 1;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }
    .cx-hero-btn:hover {
        background: #f0f1ff !important;
        color: #1A1953 !important;
        transform: translateY(-1px);
    }

    .cx-film-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(330px, 1fr));
        gap: 22px;
    }
    .cx-top-grid {
        grid-template-columns: repeat(5, 1fr);
        padding-top: 8px;
    }
    @media (max-width: 1199px) {
        .cx-top-grid { grid-template-columns: repeat(3, 1fr); }
    }
    @media (max-width: 767px) {
        .cx-top-grid { grid-template-columns: repeat(2, 1fr); }
        .cx-film-grid { grid-template-columns: repeat(2, 1fr); gap: 14px; }
    }
    @media (max-width: 479px) {
        .cx-top-grid, .cx-film-grid { grid-template-columns: 1fr; }
    }

    /* Horizontal rail — geser ke samping, halaman tidak memanjang */
    .cx-rail-wrap {
        position: relative;
    }
    .cx-film-rail {
        overflow-x: auto;
        overflow-y: hidden;
        scroll-snap-type: x mandatory;
        scroll-behavior: smooth;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
        scrollbar-color: rgba(26, 25, 83, 0.22) transparent;
        padding: 12px 2px 10px;
    }
    .cx-film-rail::-webkit-scrollbar {
        height: 6px;
    }
    .cx-film-rail::-webkit-scrollbar-thumb {
        background: rgba(26, 25, 83, 0.22);
        border-radius: 999px;
    }
    .cx-film-rail-track {
        display: flex;
        gap: 20px;
        width: max-content;
        min-width: min(100%, 330px);
    }
    .cx-film-rail .cx-film-card,
    .cx-film-rail-track > .cx-film-card {
        flex: 0 0 330px;
        width: 330px;
        scroll-snap-align: start;
    }
    .cx-film-rail-track > .cx-empty {
        flex: 1 1 100%;
        min-width: 280px;
    }
    .cx-rail-hint {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.78rem;
        color: var(--cx-muted);
        margin-bottom: 10px;
    }
    .cx-rail-hint iconify-icon {
        font-size: 1rem;
        color: var(--cx-primary);
    }
    .cx-rail-btn {
        position: absolute;
        top: 42%;
        transform: translateY(-50%);
        z-index: 4;
        width: 42px;
        height: 42px;
        border-radius: 50%;
        border: 1px solid var(--cx-border);
        background: #fff;
        color: #1A1953;
        box-shadow: 0 8px 24px rgba(26, 25, 83, 0.12);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .cx-rail-btn:hover {
        background: #1A1953;
        color: #fff;
        border-color: #1A1953;
    }
    .cx-rail-prev { left: -18px; }
    .cx-rail-next { right: -18px; }
    @media (max-width: 991px) {
        .cx-rail-prev, .cx-rail-next { display: none; }
    }

    .cx-section-footer {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-top: 1.25rem;
        padding-top: 1rem;
        border-top: 1px dashed #dde1ea;
    }
    .cx-section-footer-meta {
        font-size: 0.88rem;
        color: var(--cx-muted);
        font-weight: 600;
    }
    .cx-section-footer-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: var(--cx-primary);
        font-weight: 700;
        text-decoration: none;
        font-size: 0.9rem;
        padding: 8px 16px;
        border-radius: 999px;
        background: rgba(26, 25, 83, 0.06);
        transition: all 0.2s ease;
    }
    .cx-section-footer-link:hover {
        background: #1A1953;
        color: #fff;
    }

    /* Top 5: grid di desktop, swipe di layar kecil */
    @media (max-width: 991px) {
        .cx-top-grid {
            display: flex;
            overflow-x: auto;
            scroll-snap-type: x mandatory;
            grid-template-columns: unset;
            gap: 20px;
            padding-top: 12px;
            padding-bottom: 8px;
            -webkit-overflow-scrolling: touch;
        }
        .cx-top-grid .cx-film-card {
            flex: 0 0 330px;
            width: 330px;
            scroll-snap-align: start;
        }
    }

    .cx-film-card {
        background: var(--cx-surface);
        border-radius: 18px;
        overflow: hidden;
        border: 1px solid var(--cx-border);
        box-shadow: 0 10px 28px rgba(26, 25, 83, 0.05);
        transition: transform 0.25s ease, box-shadow 0.25s ease;
        display: flex;
        flex-direction: column;
        height: 100%;
        position: relative;
    }
    .cx-film-card--ranked {
        margin-top: 18px;
        overflow: visible;
    }
    .cx-film-card--ranked .cx-rank-badge {
        position: absolute;
        top: -16px;
        left: 14px;
        z-index: 5;
    }
    .cx-film-card--ranked .cx-film-poster {
        border-radius: 18px 18px 0 0;
    }
    .cx-film-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 18px 40px rgba(26, 25, 83, 0.12);
    }

    .cx-film-poster {
        position: relative;
        aspect-ratio: 16 / 9;
        overflow: hidden;
        background: linear-gradient(135deg, #1A1953, #3a37a0);
    }
    .cx-film-poster img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }
    .cx-film-card:hover .cx-film-poster img {
        transform: scale(1.05);
    }
    .cx-poster-link {
        position: absolute;
        inset: 0;
        z-index: 2;
    }
    .cx-poster-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(to top, rgba(0, 0, 0, 0.78) 0%, rgba(0, 0, 0, 0.15) 45%, rgba(0, 0, 0, 0) 70%);
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        padding: 12px;
        pointer-events: none;
        z-index: 1;
    }
    .cx-poster-bottom {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        width: 100%;
    }
    .cx-classification {
        position: absolute;
        top: 10px;
        right: 10px;
        background: rgba(0, 0, 0, 0.6);
        color: #fff;
        font-size: 0.66rem;
        font-weight: 800;
        padding: 4px 8px;
        border-radius: 6px;
        z-index: 3;
        backdrop-filter: blur(4px);
    }
    .cx-rank-badge {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 0.9rem;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.22);
        border: 3px solid #ffffff;
        flex-shrink: 0;
    }
    .cx-rank-badge.rank-1 { background: linear-gradient(135deg, #ffe082, #ffc107); color: #4a3800; }
    .cx-rank-badge.rank-2 { background: linear-gradient(135deg, #f5f5f5, #cfd8dc); color: #37474f; }
    .cx-rank-badge.rank-3 { background: linear-gradient(135deg, #ffcc80, #fb8c00); color: #4e342e; }
    .cx-rank-badge.rank-x { background: linear-gradient(135deg, #ffffff, #e8eaf6); color: #1A1953; border-color: #1A1953; }

    .cx-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 0.62rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        padding: 4px 10px;
        border-radius: 999px;
        white-space: nowrap;
    }
    .cx-status-now { background: rgba(25, 167, 95, 0.95); color: #fff; }
    .cx-status-soon { background: rgba(212, 176, 106, 0.95); color: #1A1953; }

    .cx-rating {
        background: rgba(0, 0, 0, 0.65);
        color: #ffd54f;
        font-weight: 700;
        font-size: 0.78rem;
        padding: 4px 10px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        flex-shrink: 0;
    }

    .cx-film-body {
        padding: 14px 16px 16px;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
        gap: 6px;
    }
    .cx-film-title {
        font-weight: 800;
        color: #1f2533;
        font-size: 0.98rem;
        line-height: 1.25;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 2.45em;
        margin: 0;
    }
    .cx-film-meta {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
        font-size: 0.78rem;
        color: var(--cx-muted);
        min-height: 1.2em;
    }
    .cx-film-meta iconify-icon { margin-right: 3px; vertical-align: -2px; }
    .cx-film-genres {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
        min-height: 22px;
    }
    .cx-genre-tag {
        font-size: 0.66rem;
        font-weight: 700;
        padding: 2px 8px;
        background: rgba(26, 25, 83, 0.07);
        color: var(--cx-primary);
        border-radius: 6px;
    }
    .cx-genre-muted { opacity: 0.6; }

    .cx-film-actions {
        margin-top: auto;
        padding-top: 12px;
        border-top: 1px dashed #e6e8f0;
    }
    .cx-btn-book {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        height: 40px;
        border-radius: 12px;
        font-size: 0.82rem;
        font-weight: 700;
        text-decoration: none;
        background: var(--cx-primary);
        color: #fff;
        border: 1px solid var(--cx-primary);
        transition: all 0.2s ease;
        position: relative;
        z-index: 3;
    }
    .cx-btn-book:hover {
        background: #14123e;
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(26, 25, 83, 0.25);
    }
    .cx-btn-outline {
        background: #fff;
        color: var(--cx-primary);
    }
    .cx-btn-outline:hover {
        background: var(--cx-primary);
        color: #fff;
    }

    .cx-filter-bar {
        background: #fff;
        border: 1px solid var(--cx-border);
        border-radius: 16px;
        padding: 14px 18px;
        box-shadow: 0 8px 22px rgba(26, 25, 83, 0.04);
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 1.75rem;
    }
    .cx-filter-label {
        font-size: 0.85rem;
        font-weight: 700;
        color: #1f2533;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .cx-filter-select {
        appearance: none;
        background: #f8f9fc;
        border: 1.5px solid rgba(26, 25, 83, 0.12);
        border-radius: 12px;
        padding: 10px 36px 10px 14px;
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--cx-primary);
        min-width: 170px;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath fill='%231A1953' d='M6 8L0 0h12z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 14px center;
    }
    .cx-filter-select:focus {
        outline: none;
        border-color: var(--cx-primary);
        box-shadow: 0 0 0 4px rgba(26, 25, 83, 0.1);
    }

    .cx-empty {
        grid-column: 1 / -1;
        text-align: center;
        padding: 3rem 1rem;
        background: #fff;
        border-radius: 18px;
        border: 1px dashed #d5d9e4;
        color: var(--cx-muted);
    }
    .cx-empty iconify-icon {
        font-size: 2.5rem;
        display: block;
        margin-bottom: 0.75rem;
        opacity: 0.5;
    }

    /* Booking page polish */
    .cx-booking-hero {
        background: linear-gradient(120deg, #1A1953 0%, #2d2b7a 60%, #3a37a0 100%);
        border-radius: 20px;
        padding: 1.25rem 1.5rem;
        color: #fff;
        margin-bottom: 1.5rem;
        box-shadow: 0 12px 30px rgba(26, 25, 83, 0.18);
    }
    .cx-booking-card {
        border: 1px solid var(--cx-border) !important;
        border-radius: 20px !important;
        box-shadow: 0 10px 28px rgba(26, 25, 83, 0.05) !important;
        overflow: hidden;
    }
    .cx-booking-card .card-header {
        background: linear-gradient(120deg, #1A1953, #2d2b7a) !important;
        border: none !important;
        padding: 1rem 1.25rem !important;
    }
    .cx-summary-card {
        border: 1px solid var(--cx-border) !important;
        border-radius: 20px !important;
        box-shadow: 0 10px 28px rgba(26, 25, 83, 0.06) !important;
    }
    .cx-summary-card .card-header {
        background: #fff !important;
        border-bottom: 1px solid var(--cx-border) !important;
        font-weight: 800;
    }

    /* ===== LIVE BADGE (on poster) ===== */
    .cx-live-badge {
        position: absolute;
        top: 10px;
        left: 10px;
        z-index: 3;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: rgba(220, 53, 69, 0.92);
        color: #fff;
        font-size: 0.6rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        padding: 4px 9px;
        border-radius: 6px;
        backdrop-filter: blur(4px);
    }
    .cx-live-dot {
        display: inline-block;
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #fff;
        animation: pulseLiveDot 1.2s ease-in-out infinite;
    }
    @keyframes pulseLiveDot {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.5; transform: scale(1.4); }
    }

    /* ===== SHOWTIME PILLS ===== */
    .cx-showtime-row {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 6px;
        padding: 8px 0 2px;
        border-top: 1px dashed #e6e8f0;
    }
    .cx-showtime-label {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 0.7rem;
        font-weight: 700;
        color: var(--cx-primary);
        white-space: nowrap;
        flex-shrink: 0;
    }
    .cx-showtime-label iconify-icon {
        font-size: 0.82rem;
    }
    .cx-showtime-pills {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
    }
    .cx-showtime-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 0.68rem;
        font-weight: 800;
        padding: 3px 9px;
        border-radius: 6px;
        border: 1.5px solid rgba(26, 25, 83, 0.18);
        background: rgba(26, 25, 83, 0.05);
        color: var(--cx-primary);
        text-decoration: none;
        transition: all 0.18s ease;
        cursor: pointer;
        position: relative;
        z-index: 3;
    }
    .cx-showtime-pill:hover {
        background: var(--cx-primary);
        color: #fff;
        border-color: var(--cx-primary);
    }
    .cx-showtime-pill--live {
        background: rgba(220, 53, 69, 0.1);
        border-color: rgba(220, 53, 69, 0.4);
        color: #dc3545;
    }
    .cx-showtime-pill--live:hover {
        background: #dc3545;
        color: #fff;
        border-color: #dc3545;
    }
    .cx-pill-dot {
        display: inline-block;
        width: 5px;
        height: 5px;
        border-radius: 50%;
        background: #dc3545;
        animation: pulseLiveDot 1.2s ease-in-out infinite;
    }
    .cx-showtime-pill--more {
        background: #f0f1ff;
        color: var(--cx-primary);
        border-color: rgba(26, 25, 83, 0.2);
        font-size: 0.65rem;
    }
</style>
