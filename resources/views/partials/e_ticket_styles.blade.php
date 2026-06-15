<style>
    .cx-eticket-wrap {
        position: relative;
        max-width: 840px;
        margin: 0 auto;
    }

    .cx-eticket {
        background: #fff;
        border: 1px solid rgba(26, 25, 83, 0.12);
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 10px 32px rgba(26, 25, 83, 0.12);
        position: relative;
    }

    .cx-eticket-top {
        background: linear-gradient(135deg, #1A1953 0%, #2d2b7a 100%);
        color: #fff;
        padding: 14px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }

    .cx-eticket-brand {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 0.82rem;
        font-weight: 800;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }

    .cx-eticket-brand iconify-icon {
        font-size: 1.15rem;
        opacity: 0.9;
    }

    .cx-eticket-meta-top {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .cx-eticket-status {
        background: rgba(25, 167, 95, 0.2);
        color: #a7f3c7;
        border: 1px solid rgba(25, 167, 95, 0.35);
        font-size: 0.68rem;
        font-weight: 800;
        letter-spacing: 0.05em;
        padding: 4px 10px;
        border-radius: 999px;
    }

    .cx-eticket-id {
        font-size: 0.72rem;
        font-weight: 700;
        color: rgba(255, 255, 255, 0.75);
        font-family: 'JetBrains Mono', 'Courier New', monospace;
    }

    .cx-eticket-booking-code {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 2px;
        padding: 6px 10px;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.16);
        border-radius: 10px;
    }

    .cx-eticket-booking-label {
        font-size: 0.62rem;
        font-weight: 700;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        color: rgba(255, 255, 255, 0.65);
    }

    .cx-eticket-booking-value {
        font-size: 0.78rem;
        font-weight: 800;
        font-family: 'JetBrains Mono', 'Courier New', monospace;
        color: #fff;
        letter-spacing: 0.04em;
    }

    .cx-eticket-field--full {
        grid-column: 1 / -1;
    }

    .cx-eticket-booking-inline {
        font-family: 'JetBrains Mono', 'Courier New', monospace;
        font-size: 0.9rem;
        font-weight: 800;
        color: #1A1953;
        letter-spacing: 0.04em;
    }

    .cx-eticket-body {
        display: grid;
        grid-template-columns: 220px 1fr auto;
        gap: 0;
        position: relative;
    }

    .cx-eticket-body::before {
        content: "";
        position: absolute;
        left: 220px;
        top: 12px;
        bottom: 12px;
        width: 1px;
        background: repeating-linear-gradient(
            to bottom,
            rgba(26, 25, 83, 0.15) 0,
            rgba(26, 25, 83, 0.15) 6px,
            transparent 6px,
            transparent 12px
        );
    }

    .cx-eticket-poster {
        padding: 24px 24px 24px 46px;
        display: flex;
        align-items: center;
    }

    .cx-eticket-poster img {
        width: 150px;
        height: 225px;
        object-fit: cover;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(26, 25, 83, 0.15);
        background: linear-gradient(135deg, #1A1953, #3a37a0);
    }

    .cx-eticket-poster-placeholder {
        width: 146px;
        height: 221px;
        border-radius: 10px;
        background: linear-gradient(135deg, #1A1953, #3a37a0);
        display: flex;
        align-items: center;
        justify-content: center;
        color: rgba(255, 255, 255, 0.45);
        font-size: 1.8rem;
    }

    .cx-eticket-info {
        padding: 18px 20px;
        min-width: 0;
    }

    .cx-eticket-title {
        font-size: 1.05rem;
        font-weight: 800;
        color: #1f2533;
        margin: 0 0 12px;
        line-height: 1.25;
    }

    .cx-eticket-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px 16px;
    }

    .cx-eticket-field label {
        display: block;
        font-size: 0.68rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: #8a93a6;
        margin-bottom: 2px;
    }

    .cx-eticket-field span {
        font-size: 0.88rem;
        font-weight: 700;
        color: #1f2533;
    }

    .cx-eticket-field span.cx-eticket-seats {
        font-size: 1rem;
        color: #1A1953;
    }

    .cx-eticket-qr {
        padding: 16px 18px 16px 12px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        border-left: none;
        min-width: 130px;
    }

    .cx-eticket-qr-frame {
        padding: 8px;
        background: #fff;
        border: 1px solid rgba(26, 25, 83, 0.1);
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(26, 25, 83, 0.08);
        margin-bottom: 8px;
    }

    .cx-eticket-qr-frame img {
        width: 96px;
        height: 96px;
        display: block;
    }

    .cx-eticket-qr-hint {
        font-size: 0.68rem;
        font-weight: 600;
        color: #8a93a6;
        line-height: 1.4;
        max-width: 110px;
        margin: 0;
    }

    .cx-eticket-footer {
        padding: 12px 20px;
        background: #f4f6fa;
        border-top: 1px dashed rgba(26, 25, 83, 0.12);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }

    .cx-eticket-footer p {
        margin: 0;
        font-size: 0.78rem;
        color: #8a93a6;
        font-weight: 600;
    }

    .cx-eticket-download {
        border: none;
        border-radius: 10px;
        padding: 8px 14px;
        font-size: 0.78rem;
        font-weight: 700;
        background: #1A1953;
        color: #fff;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.18s ease;
    }

    .cx-eticket-download:hover {
        background: #14123e;
        color: #fff;
    }

    .cx-eticket-notch {
        position: absolute;
        width: 22px;
        height: 22px;
        background: #e4e8ef;
        border-radius: 50%;
        top: 50%;
        transform: translateY(-50%);
        z-index: 2;
        border: 1px solid rgba(26, 25, 83, 0.08);
    }

    .cx-eticket-notch.left { left: -11px; }
    .cx-eticket-notch.right { right: -11px; }

    @media (max-width: 640px) {
        .cx-eticket-body {
            grid-template-columns: 1fr;
        }

        .cx-eticket-body::before {
            display: none;
        }

        .cx-eticket-poster {
            justify-content: center;
            padding-bottom: 0;
        }

        .cx-eticket-info {
            padding-top: 0;
            text-align: center;
        }

        .cx-eticket-grid {
            text-align: left;
        }

        .cx-eticket-qr {
            padding: 0 20px 18px;
            border-top: 1px dashed rgba(26, 25, 83, 0.12);
            margin-top: 4px;
            padding-top: 16px;
        }

        .cx-eticket-notch {
            display: none;
        }
    }
</style>
