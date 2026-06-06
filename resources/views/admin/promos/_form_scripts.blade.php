<script>
(function () {
    const codeEl = document.getElementById('code');
    const descEl = document.getElementById('description');
    const valEl  = document.getElementById('discount_value');
    const fromEl = document.getElementById('valid_from');
    const untilEl = document.getElementById('valid_until');
    const maxEl  = document.getElementById('max_usage');
    const perEl  = document.getElementById('max_usage_per_customer');

    const radioFixed = document.getElementById('typeFixed');
    const radioPercent = document.getElementById('typePercentage');
    const radioFixedLabel = radioFixed?.closest('.pf-radio');
    const radioPercentLabel = radioPercent?.closest('.pf-radio');

    const previewNum   = document.getElementById('previewNum');
    const previewValue = document.getElementById('previewValue');
    const previewType  = document.getElementById('previewType');
    const previewCode  = document.getElementById('previewCode');
    const previewDesc  = document.getElementById('previewDesc');
    const previewUntil = document.getElementById('previewUntil');
    const previewPer   = document.getElementById('previewPer');
    const previewMax   = document.getElementById('previewMax');
    const previewUsed  = document.getElementById('previewUsed');
    const previewFill  = document.getElementById('previewFill');
    const valuePrefix  = document.getElementById('valuePrefix');
    const valueHelp    = document.getElementById('valueHelp');

    function getType() {
        return document.querySelector('input[name="discount_type"]:checked')?.value || 'fixed';
    }

    function fmtRupiah(n) {
        const v = parseFloat(n);
        if (isNaN(v)) return '0';
        return v.toLocaleString('id-ID', { maximumFractionDigits: 0 });
    }

    function renderValue() {
        const type = getType();
        const raw = parseFloat(valEl.value);
        if (type === 'percentage') {
            valuePrefix.textContent = '%';
            if (valueHelp) valueHelp.textContent = 'Maksimal 100.';
            previewValue.innerHTML = `<span id="previewNum">${isNaN(raw) ? '0' : raw}</span><small>%</small>`;
            previewType.textContent = 'Diskon Persentase';
        } else {
            valuePrefix.textContent = 'Rp';
            if (valueHelp) valueHelp.textContent = 'Dalam Rupiah, mis. 15000.';
            previewValue.innerHTML = `<small>Rp</small> <span id="previewNum">${isNaN(raw) ? '0' : fmtRupiah(raw)}</span>`;
            previewType.textContent = 'Potongan Tetap';
        }
    }

    function renderCode() {
        const v = (codeEl.value || '').toUpperCase();
        codeEl.value = v;
        previewCode.textContent = v || 'KODE-PROMO';
    }

    function renderDesc() {
        const v = descEl.value;
        previewDesc.textContent = v || 'Deskripsi promo akan tampil di sini';
        previewDesc.classList.toggle('empty', !v);
    }

    function renderDate() {
        if (!untilEl.value) { previewUntil.textContent = '—'; return; }
        const d = new Date(untilEl.value);
        if (isNaN(d.getTime())) { previewUntil.textContent = '—'; return; }
        const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        previewUntil.textContent = `${d.getDate()} ${months[d.getMonth()]} {{ "" }}${d.getFullYear()}`;
    }

    function renderQuota() {
        previewPer.textContent = (perEl.value || '1') + 'x';
        previewMax.textContent = maxEl.value ? maxEl.value : '∞';
        const usedText = previewUsed?.textContent || '0';
        const used = parseInt(usedText) || 0;
        const max = parseInt(maxEl.value) || 0;
        const pct = max > 0 ? Math.min(100, (used / max) * 100) : (used > 0 ? 100 : 0);
        if (previewFill) previewFill.style.width = pct + '%';
    }

    function syncRadios() {
        if (radioFixed.checked) {
            radioFixedLabel?.classList.add('active');
            radioPercentLabel?.classList.remove('active');
        } else {
            radioFixedLabel?.classList.remove('active');
            radioPercentLabel?.classList.add('active');
        }
        renderValue();
    }

    codeEl?.addEventListener('input', renderCode);
    descEl?.addEventListener('input', renderDesc);
    valEl?.addEventListener('input', renderValue);
    fromEl?.addEventListener('change', () => {
        if (untilEl.value && fromEl.value && untilEl.value <= fromEl.value) {
            const d = new Date(fromEl.value);
            d.setDate(d.getDate() + 1);
            untilEl.value = d.toISOString().slice(0, 10);
        }
        if (!untilEl.min) untilEl.min = fromEl.value;
        renderDate();
    });
    untilEl?.addEventListener('change', renderDate);
    maxEl?.addEventListener('input', renderQuota);
    perEl?.addEventListener('input', renderQuota);
    radioFixed?.addEventListener('change', syncRadios);
    radioPercent?.addEventListener('change', syncRadios);

    syncRadios();
})();
</script>
