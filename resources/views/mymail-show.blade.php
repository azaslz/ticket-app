{{--
    マイメール本文（設計者：古市）
    一覧の行をクリックしたときに表示。開いた時点で既読になる
--}}
<x-layout title="マイメール" width="medium" :back="['url' => route('mymail.index'), 'label' => 'マイメールへ戻る']">

    <x-card>
        <x-slot:title>{{ $mail->title }}</x-slot:title>

        <p class="mt-1 text-muted">
            受信日時：<span class="tabular-nums">{{ Format::dateTime($mail->received_at) }}</span>　送信元：TICKET HUB
        </p>

        {{-- 本文（改行をそのまま表示。HTML はエスケープされるので安全） --}}
        <div class="mt-4 whitespace-pre-line leading-relaxed">{{ $mail->body }}</div>
    </x-card>

    {{-- 本文のリンク：Ticket_id を渡して各画面へ --}}
    <x-actions>
        @foreach ($links as $link)
            <x-button :variant="$link['variant']" :href="$link['url']">{{ $link['label'] }}</x-button>
        @endforeach
    </x-actions>
</x-layout>
