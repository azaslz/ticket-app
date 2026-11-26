{{--
    ■ パターン：メニュー
    使う画面：マイページ、企業ページのトップ など
    幅：narrow ／ 利用者情報＝ x-card ／ メニュー＝ x-list + x-list-item(href)
--}}
<x-layout title="マイページ" width="narrow">

    <x-card>
        <p class="text-lg font-bold">古市 優希</p>
        <p class="mt-0.5 text-muted">ユーザーID：<span class="tabular-nums">1</span></p>
    </x-card>

    <x-list>
        <x-list-item href="{{ route('myticket.index') }}">
            <p class="font-bold">マイチケット</p>
            <p class="mt-0.5 text-muted">申し込んだチケットとQRコード</p>
        </x-list-item>
        <x-list-item href="#">
            <p class="font-bold">マイメール</p>
            <p class="mt-0.5 text-muted">抽選結果などのお知らせ</p>
            <x-slot:aside><x-badge color="primary">未読 2</x-badge></x-slot:aside>
        </x-list-item>
        <x-list-item href="#">
            <p class="font-bold">マイファンクラブ</p>
            <p class="mt-0.5 text-muted">加入中のファンクラブ</p>
        </x-list-item>
    </x-list>
</x-layout>
