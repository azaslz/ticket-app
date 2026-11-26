{{--
    メイン内の見出し付きまとまり（1画面に複数の区切りがあるとき）
    <x-section title="申し込み内容" description="内容を確認してください">
        <x-slot:action> <x-button size="sm" variant="outline">変更</x-button> </x-slot:action>  … 任意
        中身
    </x-section>
--}}
@props(['title', 'description' => null])

<section {{ $attributes->merge(['class' => 'space-y-3']) }}>
    <div class="flex items-end justify-between gap-3">
        <div>
            <h2 class="text-lg font-bold text-ink">{{ $title }}</h2>
            @if ($description)
                <p class="mt-0.5 text-sm text-muted">{{ $description }}</p>
            @endif
        </div>
        @isset($action)
            <div class="shrink-0">{{ $action }}</div>
        @endisset
    </div>
    {{ $slot }}
</section>
