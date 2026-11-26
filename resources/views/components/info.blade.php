{{--
    「項目名：値」の並び（イベント情報、申し込み内容の確認など）
    <x-info :items="['公演日' => Format::dateTime($day), '会場' => $venue, '座席' => $seat]" />
    値が null のときは「―」を表示する
--}}
@props(['items' => []])

<dl {{ $attributes->merge(['class' => 'space-y-1.5 text-sm']) }}>
    @foreach ($items as $label => $value)
        <div class="flex gap-3">
            <dt class="w-20 shrink-0 text-muted">{{ $label }}</dt>
            <dd class="min-w-0 font-medium text-ink">{{ $value ?? '―' }}</dd>
        </div>
    @endforeach
</dl>
