{{--
    縦に並ぶ一覧（イベント一覧、マイメール、マイページのメニューなど）
    <x-list>
        <x-list-item href="...">...</x-list-item>
    </x-list>
--}}
<ul {{ $attributes->merge(['class' => 'space-y-2']) }}>
    {{ $slot }}
</ul>
