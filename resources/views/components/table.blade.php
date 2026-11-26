{{--
    表（企業向けのイベント一覧など、項目が多く横に比べたいデータ）
    中の th / td にクラスを書かなくても見た目がそろう
    <x-table>
        <thead><tr><th>イベント名</th><th class="text-right">申込数</th></tr></thead>
        <tbody><tr><td>…</td><td class="text-right tabular-nums">120</td></tr></tbody>
    </x-table>
--}}
<div class="overflow-x-auto rounded-xl bg-surface ring-1 ring-line">
    <table {{ $attributes->merge(['class' => 'w-full text-left text-sm text-ink
        [&_th]:whitespace-nowrap [&_th]:bg-page [&_th]:px-4 [&_th]:py-2.5 [&_th]:font-medium [&_th]:text-muted
        [&_td]:px-4 [&_td]:py-3 [&_td]:align-middle
        [&_tbody_tr]:border-t [&_tbody_tr]:border-line [&_tbody_tr:hover]:bg-page']) }}>
        {{ $slot }}
    </table>
</div>
