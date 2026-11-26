{{--
    ■ パターン：詳細（左に情報、右に主な内容）
    使う画面：マイQR、イベント詳細、座席表 など
    幅：サイドバーありなので自動で wide ／ 左＝ x-panel + x-info ／ 戻る＝ back

    コピーしたら @php の見本データを消し、コントローラーから渡す
--}}
@php
    // ▼ 見本データ（コピーしたら消す）
    $event = (object) ['name' => 'さくらフェス2026', 'day' => now()->addDays(14)->setTime(13, 0), 'venue' => '名古屋ドーム', 'seat' => 'A-12'];
    // ▲ 見本データここまで
@endphp

<x-layout title="マイQR" :back="['url' => '#', 'label' => 'マイチケットへ戻る']">

    <x-slot:sidebar>
        <x-panel title="イベント情報">
            <p class="mb-2 font-bold text-ink">{{ $event->name }}</p>
            <x-info :items="[
                '公演日' => Format::dateTime($event->day),
                '会場'   => $event->venue,
                '座席'   => $event->seat,
            ]" />
        </x-panel>
    </x-slot:sidebar>

    {{-- 主な内容は白い箱（x-card）に入れる --}}
    <x-card class="py-8 text-center">
        <div class="mx-auto grid h-48 w-48 place-items-center rounded-md bg-page text-sm text-muted">ここにQRコード</div>
        <p class="mt-4 text-sm text-muted">有効期限：<span class="tabular-nums text-ink">{{ Format::dateTime(now()->addMinutes(5)) }}</span></p>

        <x-slot:footer>
            <x-button block>QRを更新</x-button>
        </x-slot:footer>
    </x-card>
</x-layout>
