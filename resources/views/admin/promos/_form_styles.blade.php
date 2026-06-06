<style>
    /* Hero & coupon styles (shared with index) */
    .pr-hero {
        background: linear-gradient(120deg, #1A1953 0%, #2d2b7a 60%, #3a37a0 100%);
        border-radius: 24px;
        color: #fff;
        padding: 28px 32px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 18px 40px rgba(26, 25, 83, 0.25);
    }
    .pr-hero::after {
        content: ""; position: absolute;
        right: -60px; top: -60px;
        width: 220px; height: 220px;
        background: rgba(212, 176, 106, 0.18);
        border-radius: 50%;
    }
    .pr-hero h1 { font-size: 1.75rem; font-weight: 800; margin-bottom: 4px; }
    .pr-btn-add {
        background: linear-gradient(120deg, #d4b06a, #e7c585);
        color: #1A1953; font-weight: 700;
        border: 0; padding: 11px 22px;
        border-radius: 12px;
        display: inline-flex; align-items: center; gap: 8px;
        transition: all 0.2s;
        box-shadow: 0 8px 20px rgba(212, 176, 106, 0.35);
    }
    .pr-btn-add:hover { transform: translateY(-2px); box-shadow: 0 12px 26px rgba(212, 176, 106, 0.5); color: #1A1953; }

    /* Coupon card preview */
    .pr-card {
        background: #fff;
        border-radius: 20px;
        border: 1px solid rgba(26, 25, 83, 0.06);
        box-shadow: 0 6px 18px rgba(26, 25, 83, 0.05);
        position: relative;
        overflow: hidden;
        display: flex; flex-direction: column;
    }
    .pr-coupon {
        position: relative;
        background: linear-gradient(135deg, #1A1953 0%, #3a37a0 100%);
        color: #fff;
        padding: 22px 22px 28px;
    }
    .pr-coupon::before, .pr-coupon::after {
        content: "";
        position: absolute; bottom: -12px;
        width: 24px; height: 24px;
        background: #f7f8fc;
        border-radius: 50%;
    }
    .pr-coupon::before { left: -12px; }
    .pr-coupon::after { right: -12px; }
    .pr-status-pill {
        position: absolute; top: 14px; right: 14px;
        padding: 4px 10px; border-radius: 999px;
        background: rgba(255, 255, 255, 0.18);
        backdrop-filter: blur(6px);
        font-size: 0.68rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.06em;
        display: inline-flex; align-items: center; gap: 5px;
    }
    .pr-coupon-discount {
        font-size: 2.4rem; font-weight: 800; line-height: 1;
    }
    .pr-coupon-discount small { font-size: 1.2rem; opacity: 0.85; font-weight: 700; }
    .pr-coupon-type {
        font-size: 0.72rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.08em;
        opacity: 0.9; margin-top: 6px;
    }
    .pr-coupon-code {
        margin-top: 14px; padding: 8px 12px;
        background: rgba(255, 255, 255, 0.18);
        backdrop-filter: blur(6px);
        border: 1px dashed rgba(255, 255, 255, 0.4);
        border-radius: 10px;
        font-family: 'Courier New', monospace;
        font-weight: 800; font-size: 1rem;
        letter-spacing: 0.08em;
        text-align: center;
    }
    .pr-body {
        padding: 18px 20px 16px;
        flex: 1;
        display: flex; flex-direction: column;
    }
    .pr-desc {
        font-size: 0.88rem; color: #6b7280;
        line-height: 1.45;
        margin-bottom: 12px; min-height: 38px;
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .pr-desc.empty { font-style: italic; color: #9aa3b6; }
    .pr-meta-row {
        display: flex; justify-content: space-between; gap: 10px;
        font-size: 0.78rem; color: #6b7280;
        padding-top: 12px;
        border-top: 1px dashed rgba(26, 25, 83, 0.08);
    }
    .pr-meta-row .lbl { color: #8a93a6; font-weight: 600; }
    .pr-meta-row .val { font-weight: 700; color: #1f2533; margin-top: 2px; }
    .pr-progress { margin-top: 12px; }
    .pr-progress .label {
        display: flex; justify-content: space-between;
        font-size: 0.74rem; font-weight: 600; color: #6b7280;
        margin-bottom: 4px;
    }
    .pr-progress .bar {
        height: 6px; background: #f1f3f9;
        border-radius: 999px; overflow: hidden;
    }
    .pr-progress .fill {
        height: 100%;
        background: linear-gradient(90deg, #1A1953, #d4b06a);
        border-radius: 999px;
        transition: width 0.4s;
    }

    /* ===== Promo Form ===== */
    .pf-grid {
        display: grid;
        grid-template-columns: 1fr 360px;
        gap: 24px;
        align-items: start;
    }
    @media (max-width: 1100px) {
        .pf-grid { grid-template-columns: 1fr; }
    }

    .pf-form {
        background: #fff;
        border-radius: 20px;
        border: 1px solid rgba(26, 25, 83, 0.06);
        box-shadow: 0 8px 22px rgba(26, 25, 83, 0.05);
        padding: 26px 28px;
    }

    .pf-section {
        padding: 16px 0;
        border-bottom: 1px dashed rgba(26, 25, 83, 0.08);
    }
    .pf-section:first-of-type { padding-top: 0; }
    .pf-section:last-of-type { border-bottom: 0; padding-bottom: 0; }

    .pf-section-title {
        font-size: 0.95rem; font-weight: 800; color: #1A1953;
        display: flex; align-items: center; gap: 10px;
        margin-bottom: 14px;
    }
    .pf-section-title .num {
        width: 26px; height: 26px;
        background: linear-gradient(135deg, #1A1953, #3a37a0);
        color: #fff;
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.82rem; font-weight: 800;
    }

    .pf-label {
        font-size: 0.82rem; font-weight: 700;
        color: #1f2533; margin-bottom: 6px;
        display: block;
    }
    .pf-input {
        width: 100%;
        border: 1px solid rgba(26, 25, 83, 0.12);
        border-radius: 11px;
        padding: 11px 14px;
        font-size: 0.92rem;
        background: #fff;
        transition: all 0.2s;
    }
    .pf-input:focus {
        outline: 0; border-color: #d4b06a;
        box-shadow: 0 0 0 4px rgba(212, 176, 106, 0.15);
    }
    .pf-code {
        font-family: 'Courier New', monospace;
        font-weight: 700; letter-spacing: 0.04em;
    }
    .pf-help {
        font-size: 0.75rem; color: #8a93a6;
        margin-top: 4px;
    }

    .pf-input-affix {
        position: relative;
    }
    .pf-input-affix .prefix {
        position: absolute; left: 0; top: 0; bottom: 0;
        width: 50px;
        display: flex; align-items: center; justify-content: center;
        background: rgba(26, 25, 83, 0.06);
        color: #1A1953; font-weight: 800;
        border-radius: 11px 0 0 11px;
        border-right: 1px solid rgba(26, 25, 83, 0.08);
    }
    .pf-input-affix input {
        padding-left: 64px;
    }

    .pf-radio-group {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }
    .pf-radio {
        background: #fff;
        border: 2px solid rgba(26, 25, 83, 0.08);
        border-radius: 14px;
        padding: 12px 14px;
        cursor: pointer;
        display: flex; align-items: center; gap: 12px;
        transition: all 0.2s;
    }
    .pf-radio:hover {
        border-color: rgba(26, 25, 83, 0.3);
        background: #fafbfd;
    }
    .pf-radio.active {
        border-color: #d4b06a;
        background: rgba(212, 176, 106, 0.08);
        box-shadow: 0 4px 12px rgba(212, 176, 106, 0.18);
    }
    .pf-radio input { position: absolute; opacity: 0; pointer-events: none; }
    .pf-radio .ico {
        width: 38px; height: 38px;
        background: linear-gradient(135deg, #1A1953, #3a37a0);
        color: #fff;
        border-radius: 11px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1rem;
        flex-shrink: 0;
    }
    .pf-radio .ttl { font-weight: 700; color: #1f2533; font-size: 0.9rem; }
    .pf-radio .sub { font-size: 0.75rem; color: #6b7280; }

    /* Preview panel */
    .pf-preview {
        position: sticky;
        top: 90px;
    }
    .pf-preview-label {
        font-size: 0.78rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.06em;
        color: #8a93a6;
        margin-bottom: 10px;
    }
    .pf-tips {
        background: rgba(212, 176, 106, 0.08);
        border: 1px solid rgba(212, 176, 106, 0.25);
        border-radius: 14px;
        padding: 14px 16px;
    }
    .pf-tip-title {
        font-size: 0.85rem; font-weight: 800;
        color: #b18a3f;
        margin-bottom: 6px;
    }
    .pf-tip-list {
        margin: 0; padding-left: 20px;
        font-size: 0.8rem; color: #6b7280;
        line-height: 1.55;
    }
    .pf-tip-list li { margin-bottom: 4px; }
</style>
