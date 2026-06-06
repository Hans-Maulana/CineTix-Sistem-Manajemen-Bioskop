@php
    $initial = mb_strtoupper(mb_substr(trim($a['name']), 0, 1));
    $seatStr = implode(' ', $a['seats']);
@endphp
<div class="sd-row {{ $a['status'] === 'redeemed' ? 'redeemed' : '' }}"
     data-status="{{ $a['status'] }}"
     data-name="{{ $a['name'] }}"
     data-seats="{{ $seatStr }}">
    <div class="sd-avatar">{{ $initial }}</div>
    <div class="sd-info-cell">
        <div class="sd-name">{{ $a['name'] }}</div>
        <div class="sd-sub">
            @if ($a['is_guest'])
                <span class="guest-tag">Guest</span>
            @endif
            @if ($a['email'])
                <span><i class="fas fa-envelope me-1"></i>{{ $a['email'] }}</span>
            @endif
            <span><i class="fas fa-ticket me-1"></i>{{ $a['seat_count'] }} tiket</span>
        </div>
    </div>
    <div class="sd-seats">
        @foreach ($a['seats'] as $seat)
            <span class="seat">{{ $seat }}</span>
        @endforeach
    </div>
    <div class="sd-status">
        @if ($a['status'] === 'redeemed')
            <span class="sd-badge redeemed"><i class="fas fa-check"></i> Sudah Hadir</span>
            @if ($a['updated_at'])
                <div class="sd-time">{{ $a['updated_at']->translatedFormat('d M, H:i') }}</div>
            @endif
        @else
            <span class="sd-badge pending"><i class="fas fa-hourglass-half"></i> Belum Scan</span>
        @endif
    </div>
</div>
