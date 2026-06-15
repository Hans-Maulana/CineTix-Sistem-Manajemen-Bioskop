@extends('layouts.admin')

@section('title', 'Scan & Verifikasi Tiket')

@push('styles')
<style>
    /* ===== Tickets Scan UI ===== */
    .tk-hero {
        background: linear-gradient(120deg, #1A1953 0%, #2d2b7a 60%, #3a37a0 100%);
        border-radius: 24px;
        color: #fff;
        padding: 28px 32px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 18px 40px rgba(26, 25, 83, 0.25);
    }
    .tk-hero::after {
        content: ""; position: absolute;
        right: -60px; top: -60px;
        width: 220px; height: 220px;
        background: rgba(212, 176, 106, 0.18);
        border-radius: 50%;
    }
    .tk-hero::before {
        content: ""; position: absolute;
        right: 90px; bottom: -80px;
        width: 180px; height: 180px;
        background: rgba(255,255,255,0.05);
        border-radius: 50%;
    }
    .tk-hero h1 { font-size: 1.75rem; font-weight: 800; margin-bottom: 4px; }

    .tk-stat {
        background: #fff;
        border-radius: 18px;
        padding: 18px 20px;
        border: 1px solid rgba(26, 25, 83, 0.06);
        box-shadow: 0 8px 22px rgba(26, 25, 83, 0.04);
        display: flex; align-items: center; gap: 14px;
        height: 100%;
    }
    .tk-stat-icon {
        width: 46px; height: 46px;
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.2rem; flex-shrink: 0;
    }
    .tk-stat-label { font-size: 0.7rem; letter-spacing: 0.06em; font-weight: 700; text-transform: uppercase; color: #8a93a6; }
    .tk-stat-value { font-size: 1.45rem; font-weight: 800; color: #1f2533; line-height: 1.1; margin-top: 2px; }

    /* ===== Top Workspace: Scanner + Activity Sidebar ===== */
    .work-grid {
        display: grid;
        grid-template-columns: 1fr 360px;
        gap: 20px;
        align-items: stretch;
    }
    @media (max-width: 1100px) {
        .work-grid { grid-template-columns: 1fr; }
    }

    /* ===== Scanner Card ===== */
    .scanner-card {
        background: #fff;
        border-radius: 24px;
        padding: 32px;
        border: 1px solid rgba(26, 25, 83, 0.06);
        box-shadow: 0 14px 40px rgba(26, 25, 83, 0.06);
        position: relative;
        overflow: hidden;
    }
    .scanner-card::before {
        content: "";
        position: absolute;
        top: -100px; right: -100px;
        width: 280px; height: 280px;
        background: radial-gradient(circle, rgba(26, 25, 83, 0.04), transparent 70%);
        border-radius: 50%;
    }

    .scanner-grid {
        display: grid;
        grid-template-columns: 320px 1fr;
        gap: 28px;
        position: relative;
        align-items: center;
    }
    @media (max-width: 900px) {
        .scanner-grid { grid-template-columns: 1fr; }
    }

    /* QR illustration / scanner area */
    .qr-stage {
        aspect-ratio: 1 / 1;
        border-radius: 20px;
        background: linear-gradient(135deg, #1A1953 0%, #3a37a0 100%);
        position: relative;
        overflow: hidden;
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 16px 36px rgba(26, 25, 83, 0.25);
    }
    .qr-stage::after {
        content: "";
        position: absolute; inset: 0;
        background: radial-gradient(circle at top right, rgba(212, 176, 106, 0.18), transparent 60%);
    }
    .qr-stage .corner {
        position: absolute;
        width: 36px; height: 36px;
        border: 3px solid rgba(212, 176, 106, 0.85);
    }
    .qr-stage .corner.tl { top: 16px; left: 16px; border-right: 0; border-bottom: 0; border-radius: 8px 0 0 0; }
    .qr-stage .corner.tr { top: 16px; right: 16px; border-left: 0; border-bottom: 0; border-radius: 0 8px 0 0; }
    .qr-stage .corner.bl { bottom: 16px; left: 16px; border-right: 0; border-top: 0; border-radius: 0 0 0 8px; }
    .qr-stage .corner.br { bottom: 16px; right: 16px; border-left: 0; border-top: 0; border-radius: 0 0 8px 0; }
    .qr-stage .qr-icon {
        font-size: 5rem;
        color: rgba(255, 255, 255, 0.85);
        z-index: 1;
        animation: pulse 2.4s ease-in-out infinite;
    }
    @keyframes pulse {
        0%, 100% { transform: scale(1); opacity: 0.85; }
        50% { transform: scale(1.05); opacity: 1; }
    }

    #reader {
        width: 100% !important;
        max-width: 100% !important;
        border: 0 !important;
    }
    #reader video {
        border-radius: 16px !important;
        width: 100% !important;
    }

    .scanner-status {
        display: inline-flex; align-items: center; gap: 8px;
        font-size: 0.75rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase;
        color: #1A1953;
        background: rgba(212, 176, 106, 0.18);
        padding: 6px 12px;
        border-radius: 999px;
        margin-bottom: 12px;
    }
    .scanner-status .dot {
        width: 8px; height: 8px; border-radius: 50%;
        background: #d4b06a;
        box-shadow: 0 0 0 0 rgba(212, 176, 106, 0.6);
        animation: dotPulse 1.6s ease-in-out infinite;
    }
    @keyframes dotPulse {
        0% { box-shadow: 0 0 0 0 rgba(212, 176, 106, 0.6); }
        70% { box-shadow: 0 0 0 12px rgba(212, 176, 106, 0); }
        100% { box-shadow: 0 0 0 0 rgba(212, 176, 106, 0); }
    }
    .scanner-title { font-size: 1.55rem; font-weight: 800; color: #1f2533; line-height: 1.2; }
    .scanner-sub { color: #6b7280; font-size: 0.9rem; margin-top: 6px; line-height: 1.55; }

    .scanner-actions { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 18px; }
    .btn-scan {
        background: linear-gradient(120deg, #1A1953 0%, #3a37a0 100%);
        color: #fff; font-weight: 700; border: 0;
        padding: 12px 22px; border-radius: 12px;
        display: inline-flex; align-items: center; gap: 8px;
        transition: transform 0.2s, box-shadow 0.2s;
        box-shadow: 0 8px 20px rgba(26, 25, 83, 0.25);
    }
    .btn-scan:hover { color: #fff; transform: translateY(-2px); box-shadow: 0 12px 26px rgba(26, 25, 83, 0.35); }
    .btn-scan-stop {
        background: #fff; color: #d63b3b; border: 1px solid rgba(214, 59, 59, 0.25);
        font-weight: 700; padding: 12px 22px; border-radius: 12px;
        display: inline-flex; align-items: center; gap: 8px;
    }
    .btn-scan-stop:hover { background: #fff5f5; color: #d63b3b; }

    .manual-divider {
        display: flex; align-items: center; gap: 12px;
        color: #9aa3b6; font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em;
        margin: 22px 0 14px;
    }
    .manual-divider::before, .manual-divider::after {
        content: ""; flex: 1; height: 1px;
        background: linear-gradient(90deg, transparent, rgba(26, 25, 83, 0.12), transparent);
    }

    .manual-input-group {
        display: flex; gap: 10px;
    }
    .manual-input-group input {
        flex: 1;
        border: 1px solid rgba(26, 25, 83, 0.12);
        border-radius: 12px;
        padding: 12px 16px;
        font-family: 'Courier New', monospace;
        font-size: 0.95rem;
        letter-spacing: 0.05em;
        transition: all 0.2s;
    }
    .manual-input-group input:focus {
        outline: 0; border-color: #d4b06a;
        box-shadow: 0 0 0 4px rgba(212, 176, 106, 0.15);
    }
    .manual-input-group button {
        background: #1A1953; color: #fff; border: 0;
        padding: 0 22px; border-radius: 12px; font-weight: 700;
        display: inline-flex; align-items: center; gap: 8px;
    }
    .manual-input-group button:hover { background: #2d2b7a; color: #fff; }

    /* ===== Section heading ===== */
    .section-head {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 14px;
    }
    .section-head h5 {
        font-size: 1.1rem; font-weight: 800; color: #1f2533; margin: 0;
        display: flex; align-items: center; gap: 10px;
    }
    .section-head h5 .icon-pill {
        width: 36px; height: 36px; border-radius: 12px;
        background: linear-gradient(135deg, #1A1953, #3a37a0);
        color: #fff; display: flex; align-items: center; justify-content: center;
        font-size: 0.95rem;
    }
    .section-head .sub { color: #6b7280; font-size: 0.85rem; margin-top: 2px; }

    /* ===== Schedule monitor ===== */
    .sched-tabs {
        display: flex; gap: 8px; flex-wrap: wrap;
        background: #fff; padding: 6px;
        border-radius: 14px;
        border: 1px solid rgba(26, 25, 83, 0.06);
    }
    .sched-tab {
        background: transparent; border: 0;
        padding: 8px 16px; border-radius: 10px;
        font-size: 0.85rem; font-weight: 600; color: #6b7280;
        display: inline-flex; align-items: center; gap: 8px;
        transition: all 0.2s;
    }
    .sched-tab:hover { background: rgba(26, 25, 83, 0.05); color: #1A1953; }
    .sched-tab.active {
        background: linear-gradient(120deg, #1A1953, #3a37a0);
        color: #fff;
        box-shadow: 0 4px 14px rgba(26, 25, 83, 0.25);
    }
    .sched-tab .count {
        background: rgba(255, 255, 255, 0.2);
        padding: 1px 8px; border-radius: 999px;
        font-size: 0.7rem; font-weight: 700;
    }
    .sched-tab:not(.active) .count {
        background: rgba(26, 25, 83, 0.08);
        color: #1A1953;
    }

    .sched-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(290px, 1fr));
        gap: 16px;
    }
    .sched-card {
        background: #fff;
        border-radius: 18px;
        border: 1px solid rgba(26, 25, 83, 0.06);
        box-shadow: 0 6px 18px rgba(26, 25, 83, 0.04);
        overflow: hidden;
        transition: all 0.25s;
        display: flex; flex-direction: column;
    }
    .sched-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 14px 30px rgba(26, 25, 83, 0.1);
        border-color: rgba(212, 176, 106, 0.4);
    }
    .sched-poster {
        position: relative;
        aspect-ratio: 16 / 9;
        background: #f1f3f9;
        overflow: hidden;
    }
    .sched-poster img {
        width: 100%; height: 100%; object-fit: cover;
    }
    .sched-poster .pill {
        position: absolute; top: 10px; left: 10px;
        padding: 4px 10px; border-radius: 999px;
        font-size: 0.7rem; font-weight: 700;
        background: rgba(255, 255, 255, 0.95);
        color: #1A1953;
        backdrop-filter: blur(8px);
    }
    .sched-poster .pill.live { background: #d63b3b; color: #fff; }
    .sched-poster .time-overlay {
        position: absolute; bottom: 0; left: 0; right: 0;
        background: linear-gradient(transparent, rgba(0,0,0,0.78));
        color: #fff; padding: 26px 14px 12px;
        font-weight: 800; font-size: 1rem;
        display: flex; align-items: center; justify-content: space-between;
    }
    .sched-poster .time-overlay .date {
        font-size: 0.72rem; font-weight: 600; opacity: 0.8;
    }
    .sched-body {
        padding: 14px 16px 16px;
        display: flex; flex-direction: column;
        flex: 1;
    }
    .sched-title {
        font-size: 0.98rem; font-weight: 700; color: #1f2533;
        line-height: 1.3;
        display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .sched-meta {
        font-size: 0.78rem; color: #6b7280;
        display: flex; align-items: center; gap: 6px;
        margin-top: 4px;
    }
    .sched-stats {
        display: grid; grid-template-columns: 1fr 1fr;
        gap: 8px; margin-top: 12px;
    }
    .sched-stat {
        background: #f7f8fc;
        border-radius: 10px;
        padding: 8px 10px;
    }
    .sched-stat .lbl { font-size: 0.65rem; color: #8a93a6; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; }
    .sched-stat .val { font-size: 1.05rem; font-weight: 800; color: #1f2533; line-height: 1.1; }
    .sched-stat .val small { font-size: 0.7rem; color: #6b7280; font-weight: 500; }

    .sched-occupancy {
        margin-top: 12px;
    }
    .sched-occupancy .label {
        display: flex; align-items: center; justify-content: space-between;
        font-size: 0.72rem; font-weight: 600; color: #6b7280;
        margin-bottom: 4px;
    }
    .sched-occupancy .bar {
        height: 6px; background: #f1f3f9;
        border-radius: 999px; overflow: hidden;
    }
    .sched-occupancy .fill {
        height: 100%;
        background: linear-gradient(90deg, #1A1953, #d4b06a);
        border-radius: 999px;
        transition: width 0.5s ease;
    }

    .empty-state {
        text-align: center;
        padding: 50px 20px;
        background: #fff;
        border-radius: 18px;
        border: 2px dashed rgba(26, 25, 83, 0.12);
    }
    .empty-state .icon {
        width: 70px; height: 70px;
        margin: 0 auto 14px;
        background: rgba(26, 25, 83, 0.06);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.6rem; color: #1A1953;
    }

    /* ===== Activity feed (sidebar) ===== */
    .activity-panel {
        background: #fff;
        border-radius: 24px;
        border: 1px solid rgba(26, 25, 83, 0.06);
        box-shadow: 0 14px 40px rgba(26, 25, 83, 0.06);
        display: flex; flex-direction: column;
        overflow: hidden;
    }
    .activity-header {
        padding: 20px 22px 14px;
        border-bottom: 1px solid rgba(26, 25, 83, 0.06);
    }
    .activity-header h6 {
        margin: 0; font-size: 1rem; font-weight: 800; color: #1f2533;
        display: flex; align-items: center; gap: 10px;
    }
    .activity-header .ico {
        width: 32px; height: 32px; border-radius: 10px;
        background: linear-gradient(135deg, #1A1953, #3a37a0);
        color: #fff; display: flex; align-items: center; justify-content: center;
        font-size: 0.85rem;
    }
    .activity-header .sub { font-size: 0.78rem; color: #6b7280; margin-top: 2px; }
    .activity-list { flex: 1; overflow-y: auto; max-height: 540px; }
    .activity-item {
        display: flex; align-items: center; gap: 12px;
        padding: 12px 18px;
        border-bottom: 1px solid rgba(26, 25, 83, 0.05);
        transition: background 0.2s;
        cursor: pointer;
        text-decoration: none; color: inherit;
    }
    .activity-item:last-child { border-bottom: 0; }
    .activity-item:hover { background: #fafbfd; color: inherit; }
    .activity-avatar {
        width: 40px; height: 40px;
        border-radius: 12px;
        background: linear-gradient(135deg, #1A1953, #3a37a0);
        color: #fff; font-weight: 700;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.9rem; flex-shrink: 0;
    }
    .activity-main { flex: 1; min-width: 0; }
    .activity-name {
        font-weight: 700; color: #1f2533; font-size: 0.88rem;
        display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .activity-meta {
        font-size: 0.72rem; color: #6b7280; margin-top: 2px;
        display: flex; gap: 8px; align-items: center; flex-wrap: wrap;
    }
    .activity-meta .film {
        display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical;
        overflow: hidden; max-width: 100%;
    }
    .activity-seats { display: flex; gap: 4px; flex-wrap: wrap; margin-top: 4px; }
    .activity-seats .seat {
        background: rgba(212, 176, 106, 0.15);
        color: #b18a3f;
        padding: 1px 7px; border-radius: 6px;
        font-weight: 700; font-size: 0.7rem;
        font-family: 'Courier New', monospace;
    }
    .activity-time {
        font-size: 0.72rem; color: #8a93a6;
        text-align: right; flex-shrink: 0;
        font-weight: 600;
    }
    .activity-empty {
        text-align: center; padding: 50px 20px;
        color: #8a93a6;
    }
    .activity-empty .ico {
        width: 56px; height: 56px;
        margin: 0 auto 10px;
        background: rgba(26, 25, 83, 0.06); color: #1A1953;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.2rem;
    }

    /* Schedule card view-detail button */
    .sched-action {
        margin-top: 12px;
    }
    .sched-action .btn-detail {
        width: 100%;
        background: rgba(26, 25, 83, 0.06);
        color: #1A1953; font-weight: 700;
        border: 0;
        padding: 9px 12px;
        border-radius: 10px;
        font-size: 0.82rem;
        display: inline-flex; align-items: center; justify-content: center; gap: 8px;
        transition: all 0.2s;
    }
    .sched-action .btn-detail:hover {
        background: linear-gradient(120deg, #1A1953, #3a37a0); color: #fff;
        box-shadow: 0 6px 16px rgba(26, 25, 83, 0.2);
    }

    /* ===== Attendee Modal ===== */
    .att-overlay {
        position: fixed; inset: 0;
        background: rgba(15, 18, 30, 0.55);
        backdrop-filter: blur(8px);
        z-index: 1075;
        display: none;
        align-items: center; justify-content: center;
        padding: 24px;
    }
    .att-overlay.show { display: flex; animation: fadeIn 0.2s ease; }
    .att-modal {
        width: 100%; max-width: 720px;
        max-height: 90vh;
        background: #fff;
        border-radius: 24px;
        overflow: hidden;
        display: flex; flex-direction: column;
        box-shadow: 0 24px 60px rgba(0, 0, 0, 0.4);
        animation: slideUp 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .att-head {
        padding: 22px 24px;
        background: linear-gradient(120deg, #1A1953, #3a37a0);
        color: #fff;
        position: relative;
    }
    .att-head h5 { margin: 0; font-weight: 800; font-size: 1.2rem; }
    .att-head .meta {
        margin-top: 6px; font-size: 0.85rem; opacity: 0.9;
        display: flex; gap: 14px; flex-wrap: wrap;
    }
    .att-head .meta span { display: inline-flex; align-items: center; gap: 6px; }
    .att-close {
        position: absolute; top: 16px; right: 16px;
        width: 36px; height: 36px;
        background: rgba(255,255,255,0.18);
        border: 0; border-radius: 50%;
        color: #fff; font-size: 1rem;
        display: flex; align-items: center; justify-content: center;
        transition: background 0.2s;
    }
    .att-close:hover { background: rgba(255,255,255,0.3); }

    .att-toolbar {
        padding: 14px 24px;
        border-bottom: 1px solid rgba(26, 25, 83, 0.06);
        display: flex; gap: 10px; flex-wrap: wrap; align-items: center;
        background: #fafbfd;
    }
    .att-search {
        flex: 1; min-width: 200px;
        position: relative;
    }
    .att-search input {
        width: 100%;
        border: 1px solid rgba(26, 25, 83, 0.12);
        border-radius: 10px;
        padding: 8px 12px 8px 36px;
        font-size: 0.88rem;
        background: #fff;
    }
    .att-search input:focus {
        outline: 0; border-color: #d4b06a;
        box-shadow: 0 0 0 3px rgba(212, 176, 106, 0.15);
    }
    .att-search i {
        position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
        color: #9aa3b6; font-size: 0.85rem;
    }
    .att-filters { display: flex; gap: 6px; }
    .att-filter {
        background: #fff; border: 1px solid rgba(26, 25, 83, 0.1);
        padding: 7px 14px; border-radius: 10px;
        font-size: 0.82rem; font-weight: 600; color: #6b7280;
        cursor: pointer; transition: all 0.2s;
    }
    .att-filter:hover { color: #1A1953; border-color: rgba(26, 25, 83, 0.25); }
    .att-filter.active {
        background: linear-gradient(120deg, #1A1953, #3a37a0);
        color: #fff; border-color: transparent;
    }

    .att-list { overflow-y: auto; flex: 1; padding: 4px 0; }
    .att-row {
        display: flex; align-items: center; gap: 14px;
        padding: 14px 24px;
        border-bottom: 1px solid rgba(26, 25, 83, 0.05);
        transition: background 0.2s;
    }
    .att-row:last-child { border-bottom: 0; }
    .att-row:hover { background: #fafbfd; }
    .att-avatar {
        width: 44px; height: 44px;
        border-radius: 12px;
        background: linear-gradient(135deg, #1A1953, #3a37a0);
        color: #fff; font-weight: 700;
        display: flex; align-items: center; justify-content: center;
        font-size: 1rem; flex-shrink: 0;
    }
    .att-row.redeemed .att-avatar {
        background: linear-gradient(135deg, #16a34a, #22c55e);
    }
    .att-info { flex: 1; min-width: 0; }
    .att-name {
        font-weight: 700; color: #1f2533; font-size: 0.95rem;
        display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .att-seats { display: flex; gap: 4px; flex-wrap: wrap; margin-top: 5px; }
    .att-seats .seat {
        background: rgba(212, 176, 106, 0.18);
        color: #b18a3f;
        padding: 2px 8px; border-radius: 6px;
        font-weight: 700; font-size: 0.72rem;
        font-family: 'Courier New', monospace;
    }
    .att-status {
        flex-shrink: 0; display: flex; flex-direction: column; align-items: flex-end; gap: 4px;
    }
    .att-badge {
        font-size: 0.7rem; font-weight: 700; padding: 4px 10px;
        border-radius: 999px;
        text-transform: uppercase; letter-spacing: 0.04em;
    }
    .att-badge.redeemed { background: rgba(34, 197, 94, 0.12); color: #16a34a; }
    .att-badge.pending { background: rgba(245, 158, 11, 0.12); color: #d97706; }
    .att-time { font-size: 0.7rem; color: #8a93a6; font-weight: 600; }
    .att-empty {
        text-align: center; padding: 50px 24px; color: #8a93a6;
    }
    .att-empty .ico {
        width: 60px; height: 60px;
        margin: 0 auto 12px;
        background: #f1f3f9; color: #1A1953;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.4rem;
    }

    /* ===== Scan Result Modal ===== */
    .scan-overlay {
        position: fixed; inset: 0;
        background: rgba(15, 18, 30, 0.6);
        backdrop-filter: blur(8px);
        z-index: 1080;
        display: none;
        align-items: center; justify-content: center;
        padding: 20px;
        animation: fadeIn 0.25s ease;
    }
    .scan-overlay.show { display: flex; }
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    .scan-modal {
        width: 100%;
        max-width: 460px;
        background: #fff;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 24px 60px rgba(0, 0, 0, 0.4);
        animation: slideUp 0.35s cubic-bezier(0.16, 1, 0.3, 1);
    }
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(20px) scale(0.95); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }
    .scan-modal .head {
        padding: 28px 24px 22px;
        text-align: center;
        color: #fff;
        position: relative;
    }
    .scan-modal.success .head { background: linear-gradient(135deg, #16a34a, #22c55e); }
    .scan-modal.warning .head { background: linear-gradient(135deg, #f59e0b, #fbbf24); }
    .scan-modal.error .head { background: linear-gradient(135deg, #dc2626, #ef4444); }
    .scan-modal .head-icon {
        width: 64px; height: 64px;
        background: rgba(255,255,255,0.25);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.8rem;
        margin: 0 auto 12px;
        animation: bounce 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    @keyframes bounce {
        0% { transform: scale(0.3); opacity: 0; }
        100% { transform: scale(1); opacity: 1; }
    }
    .scan-modal .head h4 { font-weight: 800; font-size: 1.4rem; margin: 0; }
    .scan-modal .head p { margin: 4px 0 0; font-size: 0.88rem; opacity: 0.9; }

    .scan-modal .body { padding: 22px 24px; }
    .scan-row {
        display: flex; justify-content: space-between; align-items: flex-start;
        gap: 16px; padding: 10px 0;
        border-bottom: 1px solid #f1f3f9;
    }
    .scan-row:last-child { border-bottom: 0; }
    .scan-row .lbl {
        font-size: 0.78rem; color: #8a93a6; font-weight: 600;
        text-transform: uppercase; letter-spacing: 0.04em;
        flex-shrink: 0;
    }
    .scan-row .val {
        font-size: 0.92rem; font-weight: 700; color: #1f2533;
        text-align: right; word-break: break-word;
    }
    .scan-seats {
        display: flex; flex-wrap: wrap; gap: 6px; justify-content: flex-end;
    }
    .scan-seats .seat {
        background: rgba(212, 176, 106, 0.18);
        color: #b18a3f;
        padding: 4px 10px; border-radius: 8px;
        font-weight: 700; font-size: 0.8rem;
        font-family: 'Courier New', monospace;
    }
    .scan-modal .foot {
        padding: 16px 24px 22px;
        display: flex; gap: 10px;
    }
    .scan-modal .foot button {
        flex: 1;
        padding: 12px 18px;
        border-radius: 12px;
        font-weight: 700; font-size: 0.9rem;
        border: 0;
        transition: all 0.2s;
    }
    .btn-modal-primary {
        background: linear-gradient(120deg, #1A1953, #3a37a0);
        color: #fff;
    }
    .btn-modal-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(26, 25, 83, 0.25); }
    .btn-modal-secondary {
        background: #f1f3f9;
        color: #1f2533;
    }
    .btn-modal-secondary:hover { background: #e3e6f0; }
</style>
@endpush

@section('content')
@php
    $stats = $stats ?? [];
    $totalActive = $stats['total_active'] ?? 0;
    $redeemedToday = $stats['redeemed_today'] ?? 0;
    $unredeemed = $stats['unredeemed'] ?? 0;
    $todayTotal = $stats['today_total'] ?? 0;

    $allSchedules = $schedules ?? collect();
    $tab = $range ?? request('schedule_range', 'today');
    $now = \Carbon\Carbon::now();
@endphp

<div class="container-fluid py-4">
    <!-- Hero -->
    <div class="tk-hero mb-4">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 position-relative" style="z-index:1;">
            <div>
                <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mb-2"
                     style="background:rgba(255,255,255,0.12); font-size:0.75rem; font-weight:700; letter-spacing:0.06em; text-transform:uppercase;">
                    <i class="fas fa-qrcode"></i> Verifikasi Tiket
                </div>
                <h1>Scan Tiket</h1>
                <p class="mb-0" style="opacity:0.85; font-size:0.95rem;">Pindai QR atau masukkan kode tiket untuk validasi cepat.</p>
            </div>
            <div class="text-end">
                <div style="opacity:0.7; font-size:0.8rem; font-weight:600;">{{ $now->translatedFormat('l, d F Y') }}</div>
                <div style="font-size:1.6rem; font-weight:800; font-family:'Courier New', monospace; letter-spacing:0.05em;" id="liveClock">{{ $now->format('H:i:s') }}</div>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="tk-stat">
                <div class="tk-stat-icon" style="background:rgba(26, 25, 83, 0.1); color:#1A1953;"><i class="fas fa-ticket-alt"></i></div>
                <div>
                    <div class="tk-stat-label">Tiket Aktif</div>
                    <div class="tk-stat-value">{{ number_format($totalActive) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="tk-stat">
                <div class="tk-stat-icon" style="background:rgba(34, 197, 94, 0.12); color:#16a34a;"><i class="fas fa-check-circle"></i></div>
                <div>
                    <div class="tk-stat-label">Scan Hari Ini</div>
                    <div class="tk-stat-value">{{ number_format($redeemedToday) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="tk-stat">
                <div class="tk-stat-icon" style="background:rgba(245, 158, 11, 0.12); color:#d97706;"><i class="fas fa-hourglass-half"></i></div>
                <div>
                    <div class="tk-stat-label">Belum Digunakan</div>
                    <div class="tk-stat-value">{{ number_format($unredeemed) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="tk-stat">
                <div class="tk-stat-icon" style="background:rgba(212, 176, 106, 0.18); color:#b18a3f;"><i class="fas fa-calendar-day"></i></div>
                <div>
                    <div class="tk-stat-label">Tiket Hari Ini</div>
                    <div class="tk-stat-value">{{ number_format($todayTotal) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scanner + Activity Sidebar -->
    <div class="work-grid mb-4">
        <!-- Scanner Card -->
        <div class="scanner-card">
            <div class="scanner-grid">
                <div>
                    <div class="qr-stage" id="qrStage">
                        <div class="corner tl"></div>
                        <div class="corner tr"></div>
                        <div class="corner bl"></div>
                        <div class="corner br"></div>
                        <i class="fas fa-qrcode qr-icon" id="qrIcon"></i>
                        <div id="reader" style="display:none; width:100%; height:100%;"></div>
                    </div>
                </div>
                <div>
                    <span class="scanner-status">
                        <span class="dot"></span>
                        Siap Scan
                    </span>
                    <div class="scanner-title">Validasi Tiket Cepat</div>
                    <div class="scanner-sub">Aktifkan kamera untuk memindai QR code di tiket pengunjung, atau ketik kode tiket secara manual jika kamera tidak tersedia.</div>
                    <div class="scanner-actions">
                        <button id="btnStartScan" class="btn-scan">
                            <i class="fas fa-camera"></i> Mulai Scan Kamera
                        </button>
                        <button id="btnStopScan" class="btn-scan-stop d-none">
                            <i class="fas fa-stop"></i> Hentikan
                        </button>
                    </div>

                    <div class="manual-divider">atau input manual</div>

                    <form id="manualScanForm" class="manual-input-group">
                        @csrf
                        <input type="text" id="ticketCodeInput" name="ticket_code" placeholder="Masukkan kode tiket (mis. TK-XXXX-YYYY)" autocomplete="off" required>
                        <button type="submit"><i class="fas fa-bolt"></i> Verifikasi</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Activity Sidebar -->
        @php
            $bookingsCollection = isset($bookings) ? (method_exists($bookings, 'getCollection') ? $bookings->getCollection() : collect($bookings)) : collect();
            $recentBookings = $bookingsCollection->filter(fn($b) => $b->status_redeem === 'redeemed')->sortByDesc('updated_at')->take(5);
        @endphp
        <div class="activity-panel">
            <div class="activity-header">
                <h6><span class="ico"><i class="fas fa-clock-rotate-left"></i></span> Aktivitas Scan</h6>
                <div class="sub">5 tiket terakhir yang divalidasi</div>
            </div>
            <div class="activity-list">
                @forelse ($recentBookings as $bk)
                    @php
                        $name = optional($bk->user)->name ?? $bk->guest_email ?? 'Tamu';
                        $cleanName = preg_replace('/^\d+\s*-\s*/', '', $name);
                        $initial = mb_strtoupper(mb_substr(trim($cleanName), 0, 1));
                        $firstTb = $bk->ticketBookings->first();
                        $sched = optional($firstTb)->schedule;
                        $filmTitle = optional(optional($sched)->film)->title ?? '-';
                        $seats = $bk->ticketBookings->map(fn ($t) => optional($t->seat)->seat_code)->filter()->values();
                        $schedId = optional($sched)->id;
                    @endphp
                    <a href="{{ $schedId ? route('admin.tickets.schedule', $schedId) : '#' }}" class="activity-item">
                        <div class="activity-avatar">{{ $initial }}</div>
                        <div class="activity-main">
                            <div class="activity-name">{{ $cleanName }}</div>
                            <div class="activity-meta">
                                <span class="film"><i class="fas fa-film me-1"></i>{{ \Illuminate\Support\Str::limit($filmTitle, 28) }}</span>
                            </div>
                            <div class="activity-seats">
                                @foreach ($seats->take(3) as $sc)
                                    <span class="seat">{{ $sc }}</span>
                                @endforeach
                                @if ($seats->count() > 3)
                                    <span class="text-muted" style="font-size:0.7rem;">+{{ $seats->count() - 3 }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="activity-time">
                            <div>{{ optional($bk->updated_at)->format('H:i') }}</div>
                            <div class="text-muted" style="font-weight:500; font-size:0.65rem;">{{ optional($bk->updated_at)->diffForHumans() }}</div>
                        </div>
                    </a>
                @empty
                    <div class="activity-empty">
                        <div class="ico"><i class="fas fa-inbox"></i></div>
                        <div style="font-weight:600; color:#1f2533;">Belum ada scan</div>
                        <div class="small mt-1">Aktivitas scan akan muncul di sini</div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Schedule Monitor -->
    <div class="section-head">
        <div>
            <h5><span class="icon-pill"><i class="fas fa-clapperboard"></i></span> Monitor Jadwal</h5>
            <div class="sub">Pantau okupansi & redeem per jadwal</div>
        </div>
    </div>

    <div class="sched-tabs mb-3">
        <a href="{{ route('admin.tickets.index', ['schedule_range' => 'today']) }}#sched"
           class="sched-tab {{ $tab === 'today' ? 'active' : '' }}">
            <i class="fas fa-calendar-day"></i> Hari Ini
        </a>
        <a href="{{ route('admin.tickets.index', ['schedule_range' => 'now']) }}#sched"
           class="sched-tab {{ $tab === 'now' ? 'active' : '' }}">
            <i class="fas fa-circle-play"></i> Sedang Tayang
        </a>
        <a href="{{ route('admin.tickets.index', ['schedule_range' => 'upcoming']) }}#sched"
           class="sched-tab {{ $tab === 'upcoming' ? 'active' : '' }}">
            <i class="fas fa-forward"></i> Akan Datang
        </a>
        <a href="{{ route('admin.tickets.index', ['schedule_range' => 'all']) }}#sched"
           class="sched-tab {{ $tab === 'all' ? 'active' : '' }}">
            <i class="fas fa-list"></i> Semua <span class="count">{{ $allSchedules->count() }}</span>
        </a>
    </div>

    <div id="sched"></div>
    @if ($allSchedules->isEmpty())
        <div class="empty-state mb-4">
            <div class="icon"><i class="fas fa-calendar-xmark"></i></div>
            <div style="font-weight:700; color:#1f2533;">Tidak ada jadwal pada filter ini</div>
            <div class="text-muted small mt-1">Coba pilih tab lain untuk melihat jadwal lainnya.</div>
        </div>
    @else
        <div class="sched-grid mb-4">
            @foreach ($allSchedules as $sc)
                @php
                    $sold = $sc->tickets_sold ?? 0;
                    $redeemed = $sc->tickets_redeemed ?? 0;
                    $cap = optional($sc->studio)->capacity ?? 0;
                    $occupancy = $cap > 0 ? min(100, round(($sold / $cap) * 100)) : 0;
                    $isLive = $sc->status === 'now playing';
                @endphp
                <div class="sched-card">
                    <div class="sched-poster">
                        @if (optional($sc->film)->cover_url)
                            <img src="{{ $sc->film->cover_url }}" alt="{{ $sc->film->title }}">
                        @else
                            <div class="d-flex align-items-center justify-content-center h-100 text-muted"><i class="fas fa-film fa-2x"></i></div>
                        @endif
                        @if ($isLive)
                            <span class="pill live"><i class="fas fa-circle"></i> LIVE</span>
                        @else
                            <span class="pill">{{ ucfirst($sc->status) }}</span>
                        @endif
                        <div class="time-overlay">
                            <span>{{ optional($sc->start_time)->format('H:i') }} - {{ optional($sc->end_time)->format('H:i') }}</span>
                            <span class="date">{{ optional($sc->schedule_date)->translatedFormat('d M') }}</span>
                        </div>
                    </div>
                    <div class="sched-body">
                        <div class="sched-title">{{ optional($sc->film)->title ?? 'Tanpa Film' }}</div>
                        <div class="sched-meta">
                            <i class="fas fa-door-open"></i> {{ optional($sc->studio)->name ?? '-' }}
                            <span class="mx-1">•</span>
                            <i class="fas fa-tag"></i> Rp{{ number_format($sc->ticket_price, 0, ',', '.') }}
                        </div>
                        <div class="sched-stats">
                            <div class="sched-stat">
                                <div class="lbl">Terjual</div>
                                <div class="val">{{ $sold }} <small>/{{ $cap }}</small></div>
                            </div>
                            <div class="sched-stat">
                                <div class="lbl">Hadir</div>
                                <div class="val">{{ $redeemed }} <small>/{{ $sold }}</small></div>
                            </div>
                        </div>
                        <div class="sched-occupancy">
                            <div class="label"><span>Okupansi</span><span>{{ $occupancy }}%</span></div>
                            <div class="bar"><div class="fill" style="width: {{ $occupancy }}%"></div></div>
                        </div>
                        <div class="sched-action">
                            <a href="{{ route('admin.tickets.schedule', $sc->id) }}" class="btn-detail">
                                <i class="fas fa-users"></i> Lihat Pengunjung ({{ $sold }})
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>

<!-- Scan Result Modal -->
<div class="scan-overlay" id="scanResultOverlay">
    <div class="scan-modal" id="scanModal">
        <div class="head">
            <div class="head-icon"><i class="fas fa-check" id="scanIcon"></i></div>
            <h4 id="scanTitle">Tiket Berhasil Divalidasi</h4>
            <p id="scanSubtitle">Selamat menonton!</p>
        </div>
        <div class="body" id="scanBody">
            <!-- filled by JS -->
        </div>
        <div class="foot">
            <button class="btn-modal-secondary" id="btnScanClose">Tutup</button>
            <button class="btn-modal-primary" id="btnScanContinue"><i class="fas fa-camera"></i> Scan Lagi</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
(function () {
    const liveClock = document.getElementById('liveClock');
    if (liveClock) {
        setInterval(() => {
            const d = new Date();
            const hh = String(d.getHours()).padStart(2, '0');
            const mm = String(d.getMinutes()).padStart(2, '0');
            const ss = String(d.getSeconds()).padStart(2, '0');
            liveClock.textContent = `${hh}:${mm}:${ss}`;
        }, 1000);
    }

    const reader = document.getElementById('reader');
    const qrIcon = document.getElementById('qrIcon');
    const btnStart = document.getElementById('btnStartScan');
    const btnStop = document.getElementById('btnStopScan');
    const manualForm = document.getElementById('manualScanForm');
    const ticketInput = document.getElementById('ticketCodeInput');
    const overlay = document.getElementById('scanResultOverlay');
    const modal = document.getElementById('scanModal');
    const scanIcon = document.getElementById('scanIcon');
    const scanTitle = document.getElementById('scanTitle');
    const scanSubtitle = document.getElementById('scanSubtitle');
    const scanBody = document.getElementById('scanBody');
    const btnClose = document.getElementById('btnScanClose');
    const btnContinue = document.getElementById('btnScanContinue');

    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const scanUrl = "{{ route('admin.tickets.scan') }}";
    let html5QrCode = null;
    let isScanning = false;

    function startScan() {
        if (isScanning) return;
        if (qrIcon) qrIcon.style.display = 'none';
        reader.style.display = 'block';
        btnStart.classList.add('d-none');
        btnStop.classList.remove('d-none');

        if (!html5QrCode) {
            html5QrCode = new Html5Qrcode('reader');
        }
        html5QrCode.start(
            { facingMode: 'environment' },
            { fps: 10, qrbox: { width: 240, height: 240 } },
            (decoded) => {
                if (!isScanning) return;
                isScanning = false;
                stopScan(false);
                submitTicket(decoded);
            },
            () => {}
        ).then(() => { isScanning = true; })
        .catch((err) => {
            console.error(err);
            alert('Tidak dapat mengakses kamera: ' + err);
            stopScan();
        });
    }

    function stopScan(showIcon = true) {
        if (html5QrCode && html5QrCode.isScanning) {
            html5QrCode.stop().catch(() => {});
        }
        isScanning = false;
        reader.style.display = 'none';
        if (showIcon && qrIcon) qrIcon.style.display = 'block';
        btnStart.classList.remove('d-none');
        btnStop.classList.add('d-none');
    }

    btnStart?.addEventListener('click', startScan);
    btnStop?.addEventListener('click', () => stopScan(true));

    manualForm?.addEventListener('submit', (e) => {
        e.preventDefault();
        const code = ticketInput.value.trim();
        if (!code) return;
        submitTicket(code);
    });

    function submitTicket(code) {
        const fd = new FormData();
        fd.append('qr_code', code);
        fd.append('_token', csrf);

        fetch(scanUrl, {
            method: 'POST',
            body: fd,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
        })
        .then(async (r) => {
            const ct = r.headers.get('content-type') || '';
            if (ct.includes('application/json')) {
                const data = await r.json();
                if (!r.ok) {
                    showResult({ status: 'error', message: data.message || 'Terjadi kesalahan server.' });
                    return;
                }
                showResult(data);
            } else {
                window.location.reload();
            }
        })
        .catch((err) => {
            console.error(err);
            showResult({ status: 'error', message: 'Periksa koneksi internet Anda.' });
        });
    }

    function showResult(data) {
        const status = data.status || 'success';
        modal.classList.remove('success', 'warning', 'error');
        modal.classList.add(status);

        const iconMap = { success: 'fa-check', warning: 'fa-triangle-exclamation', error: 'fa-xmark' };
        scanIcon.className = 'fas ' + (iconMap[status] || 'fa-info');

        const titleMap = {
            success: 'Tiket Berhasil Divalidasi',
            warning: 'Tiket Sudah Pernah Discan',
            error: 'Tiket Tidak Valid',
        };
        scanTitle.textContent = data.title || titleMap[status] || 'Hasil Scan';
        scanSubtitle.textContent = data.message || '';

        let html = '';
        if (data.customer) html += row('Nama', escapeHtml(data.customer));
        if (data.film_title) html += row('Film', escapeHtml(data.film_title));
        if (data.studio) {
            const studioText = data.studio + (data.studio_type ? ` <span class="text-muted" style="font-weight:500;">· ${escapeHtml(data.studio_type)}</span>` : '');
            html += rowRaw('Studio', studioText);
        }
        if (data.date) html += row('Tanggal', escapeHtml(data.date));
        if (data.time) html += row('Jam', escapeHtml(data.time));
        if (Array.isArray(data.seats) && data.seats.length) {
            const chips = data.seats.map(s => `<span class="seat">${escapeHtml(s)}</span>`).join('');
            html += `<div class="scan-row"><div class="lbl">Kursi</div><div class="val"><div class="scan-seats">${chips}</div></div></div>`;
        }
        if (data.qr_code) html += rowRaw('Kode', `<span style="font-family:'Courier New',monospace; font-size:0.78rem; color:#6b7280;">${escapeHtml(data.qr_code)}</span>`);

        scanBody.innerHTML = html || `<div class="text-muted text-center py-2">${escapeHtml(data.message || 'Tidak ada data tiket.')}</div>`;
        overlay.classList.add('show');
        ticketInput.value = '';
    }

    function rowRaw(label, value) {
        return `<div class="scan-row"><div class="lbl">${escapeHtml(label)}</div><div class="val">${value}</div></div>`;
    }

    function row(label, value) {
        return `<div class="scan-row"><div class="lbl">${escapeHtml(label)}</div><div class="val">${value}</div></div>`;
    }
    function escapeHtml(s) {
        return String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    }

    btnClose?.addEventListener('click', () => {
        overlay.classList.remove('show');
        ticketInput.focus();
    });
    btnContinue?.addEventListener('click', () => {
        overlay.classList.remove('show');
        startScan();
    });
    overlay?.addEventListener('click', (e) => {
        if (e.target === overlay) overlay.classList.remove('show');
    });

    @if (session('success'))
        showResult({ status: 'success', title: 'Berhasil', message: @json(session('success')) });
    @elseif (session('warning'))
        showResult({ status: 'warning', title: 'Perhatian', message: @json(session('warning')) });
    @elseif (session('error'))
        showResult({ status: 'error', title: 'Gagal', message: @json(session('error')) });
    @endif
})();
</script>
@endpush
