{{--
    一覧の1行（白い横長の箱）。href を付けると行全体がリンクになり右端に「>」が付く
    <x-list-item href="{{ route('event.show', $id) }}">
        <p class="font-bold">イベント名</p>
        <p class="text-sm text-muted">2026/11/24(火) 13:00・名古屋ドーム</p>
        <x-slot:aside> <x-badge color="primary">未読</x-badge> </x-slot:aside>   … 任意（右側）
    </x-list-item>
--}}
@props(['href' => null])

@php
    $row = 'flex items-center gap-4 rounded-xl bg-surface px-4 py-3 ring-1 ring-line';
@endphp

<li>
    @if ($href)
        <a href="{{ $href }}" {{ $attributes->merge(['class' => $row . ' transition-colors hover:bg-primary-light/40 focus-visible:outline-2 focus-visible:outline-primary']) }}>
    @else
        <div {{ $attributes->merge(['class' => $row]) }}>
    @endif

        <div class="min-w-0 flex-1 text-sm text-ink">{{ $slot }}</div>

        @isset($aside)
            <div class="shrink-0 text-sm text-muted">{{ $aside }}</div>
        @endisset

    @if ($href)
            <svg class="h-5 w-5 shrink-0 text-muted" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"/>
            </svg>
        </a>
    @else
        </div>
    @endif
</li>
