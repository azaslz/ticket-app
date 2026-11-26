{{--
    メッセージ枠
    <x-alert type="error">このチケットは表示できません</x-alert>
    type: error / success / info
--}}
@props(['type' => 'info'])

@php
    $types = [
        'error'   => 'bg-danger-light text-danger ring-danger/20',
        'success' => 'bg-success-light text-success-dark ring-success/20',
        'info'    => 'bg-primary-light text-primary-dark ring-primary/20',
    ];
@endphp

<div role="{{ $type === 'error' ? 'alert' : 'status' }}"
     {{ $attributes->merge(['class' => 'rounded-lg px-4 py-3 text-sm ring-1 ring-inset ' . ($types[$type] ?? $types['info'])]) }}>
    {{ $slot }}
</div>
