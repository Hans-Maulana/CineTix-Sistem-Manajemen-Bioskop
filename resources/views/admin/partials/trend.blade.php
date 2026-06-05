@php
    $dir = $trend['direction'] ?? 'flat';
    $pct = $trend['percent'] ?? 0;
@endphp
@if($dir === 'up')
    <span class="trend-badge trend-up"><i class="bi bi-arrow-up-right"></i> {{ $pct }}%</span>
@elseif($dir === 'down')
    <span class="trend-badge trend-down"><i class="bi bi-arrow-down-right"></i> {{ $pct }}%</span>
@else
    <span class="trend-badge trend-flat"><i class="bi bi-dash"></i> 0%</span>
@endif
