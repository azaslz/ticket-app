{{--
    データが0件のときの表示
    <x-empty>該当するチケットはありません</x-empty>
    <x-empty>…<x-slot:hint>表示条件を変えてみてください</x-slot:hint></x-empty>
--}}
<div {{ $attributes->merge(['class' => 'rounded-xl border-2 border-dashed border-line px-4 py-12 text-center']) }}>
    <p class="text-sm font-medium text-ink">{{ $slot }}</p>
    @isset($hint)
        <p class="mt-1 text-sm text-muted">{{ $hint }}</p>
    @endisset
</div>
