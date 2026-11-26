{{--
    マイメール一覧（設計者：古市）
    パターン：縦の一覧（resources/views/ui/list.blade.php をコピーして作成）
--}}
<x-layout title="マイメール" width="medium">

    <p class="text-sm text-muted">
        未読 <span class="font-bold tabular-nums text-ink">{{ $unreadCount }}</span> 件
    </p>

    @if ($mails->isEmpty())
        <x-empty>
            メールはまだありません
            <x-slot:hint>チケットに申し込むと、受付や抽選結果のお知らせがここに届きます</x-slot:hint>
        </x-empty>
    @else
        {{-- 受信日時の新しい順。行をクリックすると本文を表示して既読にする --}}
        <x-list>
            @foreach ($mails as $mail)
                <x-list-item :href="route('mymail.show', $mail->Email_id)">
                    <p class="{{ $mail->is_unread ? 'font-bold text-ink' : 'font-medium text-muted' }}">{{ $mail->title }}</p>
                    <p class="mt-0.5 truncate text-muted">
                        <span class="tabular-nums">{{ Format::dateTime($mail->received_at) }}</span>　{{ $mail->summary }}
                    </p>

                    @if ($mail->is_unread)
                        <x-slot:aside><x-badge color="primary">未読</x-badge></x-slot:aside>
                    @endif
                </x-list-item>
            @endforeach
        </x-list>
    @endif
</x-layout>
