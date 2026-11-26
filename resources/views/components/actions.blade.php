{{--
    ボタンの並び（フォームの下、完了画面など）
    いちばん大事なボタンを「最後」に書く → PCでは右端、スマホでは一番上になる
    <x-actions>
        <x-button variant="outline" href="...">戻る</x-button>
        <x-button type="submit">申し込む</x-button>
    </x-actions>
--}}
<div {{ $attributes->merge(['class' => 'flex flex-col-reverse gap-2 sm:flex-row sm:justify-end']) }}>
    {{ $slot }}
</div>
