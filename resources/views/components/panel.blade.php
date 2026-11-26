{{--
    サイドバー用の白いパネル（見出し付き）
    <x-panel title="表示条件"> ... </x-panel>
--}}
@props(['title' => null])

<section {{ $attributes->merge(['class' => 'rounded-xl bg-surface p-4 ring-1 ring-line']) }}>
    @if ($title)
        <h2 class="mb-2 text-sm font-bold text-muted">{{ $title }}</h2>
    @endif
    {{ $slot }}
</section>
