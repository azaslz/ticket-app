{{--
    状態ラベル
    <x-badge status="当選" />            … チケットの状態名から色を自動で選ぶ
    <x-badge color="primary">未読</x-badge> … 色を指定して任意の文字

    color: primary / success / warning / danger / muted
--}}
@props(['status' => null, 'color' => null])

@php
    // チケット状態 → 色（全画面で同じ対応にする）
    $statusColors = [
        '当選'   => 'success',
        '抽選中' => 'warning',
        '落選'   => 'muted',
        '使用済' => 'primary',
    ];
    $color = $color ?? ($statusColors[$status] ?? 'muted');

    $colors = [
        'primary' => 'bg-primary-light text-primary-dark',
        'success' => 'bg-success-light text-success-dark',
        'warning' => 'bg-warning-light text-warning',
        'danger'  => 'bg-danger-light text-danger',
        'muted'   => 'bg-panel text-muted',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ' . ($colors[$color] ?? $colors['muted'])]) }}>
    {{ $slot->isEmpty() ? $status : $slot }}
</span>
