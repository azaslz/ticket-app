{{--
    ボタン（href を渡すとリンク、渡さないと <button>）

    <x-button href="{{ route('...') }}">QR</x-button>
    <x-button variant="success" href="...">座席表</x-button>
    <x-button type="submit" block>更新</x-button>

    variant: primary（青・メイン操作） / success（緑・サブ操作）
             outline（白抜き） / danger（赤・削除など）
    size:    md（標準） / sm（カード内など）
    block:   付けると横幅いっぱい
    disabled: 押せない状態
--}}
@props([
    'variant'  => 'primary',
    'size'     => 'md',
    'href'     => null,
    'type'     => 'button',
    'block'    => false,
    'disabled' => false,
])

@php
    $variants = [
        'primary' => 'bg-primary text-white hover:bg-primary-dark',
        'success' => 'bg-success text-white hover:bg-success-dark',
        'outline' => 'bg-surface text-ink ring-1 ring-inset ring-line hover:bg-page',
        'danger'  => 'bg-danger text-white hover:opacity-90',
    ];
    $sizes = [
        'sm' => 'h-9 px-3 text-sm',
        'md' => 'h-10 px-4 text-sm',
    ];

    $class = implode(' ', [
        'inline-flex items-center justify-center gap-1.5 rounded-md font-medium transition-colors',
        'focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary',
        $variants[$variant] ?? $variants['primary'],
        $sizes[$size] ?? $sizes['md'],
        $block ? 'w-full' : '',
        $disabled ? 'pointer-events-none opacity-50' : '',
    ]);
@endphp

@if ($href && !$disabled)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $class]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" @disabled($disabled) {{ $attributes->merge(['class' => $class]) }}>{{ $slot }}</button>
@endif
