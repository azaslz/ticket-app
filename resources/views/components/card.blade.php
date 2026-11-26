{{--
    一覧などに使う白いカード
    <x-card>
        <x-slot:title>イベント1</x-slot:title>   … 任意
        本文
        <x-slot:footer> ボタンなど </x-slot:footer> … 任意（下端にそろう）
    </x-card>

    ticket を付けると、footer の上に「切り取り線」が入ったチケット型になる
    <x-card ticket> ... </x-card>
--}}
@props(['ticket' => false])

<article {{ $attributes->merge(['class' => 'flex flex-col rounded-xl bg-surface p-4 ring-1 ring-line']) }}>
    @isset($title)
        <h3 class="text-base font-bold leading-snug text-ink">{{ $title }}</h3>
    @endisset

    <div class="flex-1 text-sm text-ink">
        {{ $slot }}
    </div>

    @isset($footer)
        @if ($ticket)
            <div class="ticket-perforation mt-4" aria-hidden="true"></div>
        @endif
        <div class="{{ $ticket ? 'pt-4' : 'mt-4' }} flex gap-2">
            {{ $footer }}
        </div>
    @endisset
</article>
