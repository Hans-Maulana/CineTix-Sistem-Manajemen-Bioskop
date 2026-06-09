@php
    $isEdit = isset($promo) && $promo->exists;
    $action = $isEdit ? route('admin.promos.update', $promo) : route('admin.promos.store');

    $fCode  = old('code', $promo->code ?? '');
    $fDesc  = old('description', $promo->description ?? '');
    $fType  = old('discount_type', $promo->discount_type ?? 'fixed');
    $fValue = old('discount_value', $promo->discount_value ?? '');
    $fFrom  = old('valid_from', isset($promo) && $promo->valid_from ? $promo->valid_from->format('Y-m-d') : '');
    $fUntil = old('valid_until', isset($promo) && $promo->valid_until ? $promo->valid_until->format('Y-m-d') : '');
    $fMax   = old('max_usage', $promo->max_usage ?? '');
    $fPer   = old('max_usage_per_customer', $promo->max_usage_per_customer ?? 1);
@endphp

<form method="POST" action="{{ $action }}" id="promoForm">
    @csrf
    @if ($isEdit) @method('PUT') @endif

    <div class="pf-grid">
        <!-- LEFT: Form -->
        <div class="pf-form">
            @if ($errors->any())
                <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-3">
                    <i class="fas fa-circle-exclamation me-2"></i>Ada {{ $errors->count() }} input yang perlu diperbaiki.
                </div>
            @endif

            <!-- Section: Informasi Dasar -->
            <div class="pf-section">
                <div class="pf-section-title"><span class="num">1</span> Informasi Dasar</div>

                <div class="mb-3">
                    <label class="pf-label">Kode Promo <span class="text-danger">*</span></label>
                    <input type="text" name="code" id="code"
                           class="pf-input pf-code @error('code') is-invalid @enderror"
                           value="{{ $fCode }}" required maxlength="50"
                           placeholder="Mis. WEEKEND50" autocomplete="off"
                           style="text-transform: uppercase;">
                    <div class="pf-help">Akan otomatis di-uppercase. Maks 50 karakter.</div>
                    @error('code') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="mb-0">
                    <label class="pf-label">Deskripsi</label>
                    <input type="text" name="description" id="description"
                           class="pf-input @error('description') is-invalid @enderror"
                           value="{{ $fDesc }}" maxlength="255"
                           placeholder="Mis. Diskon spesial akhir pekan">
                    @error('description') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
            </div>

            <!-- Section: Diskon -->
            <div class="pf-section">
                <div class="pf-section-title"><span class="num">2</span> Pengaturan Diskon</div>

                <div class="mb-3">
                    <label class="pf-label">Tipe Potongan <span class="text-danger">*</span></label>
                    <div class="pf-radio-group">
                        <label class="pf-radio {{ $fType === 'fixed' ? 'active' : '' }}">
                            <input type="radio" name="discount_type" value="fixed" {{ $fType === 'fixed' ? 'checked' : '' }} id="typeFixed">
                            <div class="ico"><i class="fas fa-money-bill-wave"></i></div>
                            <div>
                                <div class="ttl">Nominal Tetap</div>
                                <div class="sub">Potongan rupiah pasti</div>
                            </div>
                        </label>
                        <label class="pf-radio {{ $fType === 'percentage' ? 'active' : '' }}">
                            <input type="radio" name="discount_type" value="percentage" {{ $fType === 'percentage' ? 'checked' : '' }} id="typePercentage">
                            <div class="ico"><i class="fas fa-percent"></i></div>
                            <div>
                                <div class="ttl">Persentase</div>
                                <div class="sub">Potongan dari subtotal</div>
                            </div>
                        </label>
                    </div>
                    @error('discount_type') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="mb-0">
                    <label class="pf-label">Nilai Diskon <span class="text-danger">*</span></label>
                    <div class="pf-input-affix">
                        <span class="prefix" id="valuePrefix">{{ $fType === 'percentage' ? '%' : 'Rp' }}</span>
                        <input type="number" name="discount_value" id="discount_value"
                               class="pf-input @error('discount_value') is-invalid @enderror"
                               value="{{ $fValue }}" min="0" step="0.01" required
                               placeholder="{{ $fType === 'percentage' ? '10' : '15000' }}">
                    </div>
                    <div class="pf-help" id="valueHelp">
                        @if ($fType === 'percentage') Maksimal 100. @else Dalam Rupiah, mis. 15000. @endif
                    </div>
                    @error('discount_value') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
            </div>

            <!-- Section: Periode -->
            <div class="pf-section">
                <div class="pf-section-title"><span class="num">3</span> Periode Berlaku</div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="pf-label">Mulai <span class="text-danger">*</span></label>
                        <input type="date" name="valid_from" id="valid_from"
                               class="pf-input @error('valid_from') is-invalid @enderror"
                               value="{{ $fFrom }}" required>
                        @error('valid_from') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="pf-label">Sampai <span class="text-danger">*</span></label>
                        <input type="date" name="valid_until" id="valid_until"
                               class="pf-input @error('valid_until') is-invalid @enderror"
                               value="{{ $fUntil }}" required>
                        @error('valid_until') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="pf-help mt-2">Tanggal akhir harus setelah tanggal mulai.</div>
            </div>

            <!-- Section: Kuota -->
            <div class="pf-section">
                <div class="pf-section-title"><span class="num">4</span> Pengaturan Kuota</div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="pf-label">Total Kuota Sistem</label>
                        <input type="number" name="max_usage" id="max_usage"
                               class="pf-input @error('max_usage') is-invalid @enderror"
                               value="{{ $fMax }}" min="1"
                               placeholder="Kosongkan = unlimited">
                        <div class="pf-help">Total maksimal pemakaian oleh seluruh customer.</div>
                        @error('max_usage') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="pf-label">Limit per Customer <span class="text-danger">*</span></label>
                        <input type="number" name="max_usage_per_customer" id="max_usage_per_customer"
                               class="pf-input @error('max_usage_per_customer') is-invalid @enderror"
                               value="{{ $fPer }}" min="1" required>
                        <div class="pf-help">Berapa kali 1 customer bisa memakai promo ini.</div>
                        @error('max_usage_per_customer') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                </div>

                @if ($isEdit)
                    <div class="alert alert-info border-0 mt-3 mb-0 d-flex align-items-center" style="background:rgba(26, 25, 83, 0.06); color:#1A1953; border-radius:12px;">
                        <i class="fas fa-info-circle me-2"></i>
                        <div>Promo ini sudah dipakai <strong>{{ $promo->usage_count }} kali</strong>.</div>
                    </div>
                @endif
            </div>

            <div class="d-flex gap-2 justify-content-end mt-4">
                <a href="{{ route('admin.promos.index') }}" class="btn btn-light border" style="border-radius:12px; padding:11px 20px; font-weight:600;">Batal</a>
                <button type="submit" class="pr-btn-add">
                    <i class="fas fa-{{ $isEdit ? 'save' : 'plus' }}"></i>
                    {{ $isEdit ? 'Perbarui Promo' : 'Simpan Promo' }}
                </button>
            </div>
        </div>

        <!-- RIGHT: Live Preview -->
        <div class="pf-preview">
            <div class="pf-preview-label">
                <i class="fas fa-eye me-1"></i> Preview Promo
            </div>

            <div class="pr-card" id="livePreview">
                <div class="pr-coupon">
                    <span class="pr-status-pill" style="background:rgba(34, 197, 94, 0.25);">
                        <span style="width:6px; height:6px; background:#4ade80; border-radius:50%;"></span> Preview
                    </span>
                    <div class="pr-coupon-discount" id="previewValue">
                        @if ($fType === 'percentage')
                            <span id="previewNum">{{ $fValue ?: '0' }}</span><small>%</small>
                        @else
                            <small>Rp</small> <span id="previewNum">{{ $fValue ? number_format((float) $fValue, 0, ',', '.') : '0' }}</span>
                        @endif
                    </div>
                    <div class="pr-coupon-type" id="previewType">
                        {{ $fType === 'percentage' ? 'Diskon Persentase' : 'Potongan Tetap' }}
                    </div>
                    <div class="pr-coupon-code" id="previewCode">{{ $fCode ?: 'KODE-PROMO' }}</div>
                </div>
                <div class="pr-body">
                    <div class="pr-desc {{ !$fDesc ? 'empty' : '' }}" id="previewDesc">
                        {{ $fDesc ?: 'Deskripsi promo akan tampil di sini' }}
                    </div>
                    <div class="mt-auto">
                        <div class="pr-meta-row">
                            <div>
                                <div class="lbl"><i class="fas fa-calendar-day me-1"></i>Berlaku sampai</div>
                                <div class="val" id="previewUntil">{{ $fUntil ? \Carbon\Carbon::parse($fUntil)->translatedFormat('d M Y') : '—' }}</div>
                            </div>
                            <div class="text-end">
                                <div class="lbl"><i class="fas fa-user me-1"></i>Per Customer</div>
                                <div class="val" id="previewPer">{{ $fPer }}x</div>
                            </div>
                        </div>
                        <div class="pr-progress">
                            <div class="label">
                                <span><i class="fas fa-fire me-1"></i>Pemakaian</span>
                                <span><span id="previewUsed">{{ $isEdit ? $promo->usage_count : 0 }}</span> / <span id="previewMax">{{ $fMax ?: '∞' }}</span></span>
                            </div>
                            <div class="bar">
                                <div class="fill" id="previewFill" style="width: {{ $isEdit && $promo->max_usage ? min(100, ($promo->usage_count / $promo->max_usage) * 100) : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pf-tips mt-3">
                <div class="pf-tip-title"><i class="fas fa-lightbulb me-1"></i> Tips</div>
                <ul class="pf-tip-list">
                    <li>Kode promo bersifat case-insensitive — selalu disimpan dalam huruf besar.</li>
                    <li>Kosongkan <strong>Total Kuota Sistem</strong> untuk pemakaian unlimited.</li>
                    <li>Promo otomatis tidak berlaku setelah <strong>tanggal akhir</strong>.</li>
                </ul>
            </div>
        </div>
    </div>
</form>
