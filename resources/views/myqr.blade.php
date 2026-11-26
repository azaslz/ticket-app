{{--
    マイQR（設計者：古市）
    パターン：詳細（resources/views/ui/detail.blade.php をコピーして作成）
--}}
<x-layout title="マイQR" :back="['url' => route('myticket.index'), 'label' => 'マイチケットへ戻る']">

    {{-- ── 左：イベント情報（①イベント名 ②公演日 ③座席 ＋チケット名） ── --}}
    <x-slot:sidebar>
        <x-panel title="イベント情報">
            <p class="mb-2 font-bold text-ink">{{ $ticket->Event_name }}</p>
            <x-info :items="[
                'チケット' => $ticket->Ticket_name,
                '公演日'   => Format::date($ticket->Ticket_day),
                '座席'     => $ticket->Seat_nums,
            ]" />
        </x-panel>
    </x-slot:sidebar>

    {{-- ── 右：④QRコード ── --}}
    <x-card class="py-8 text-center">
        <div id="qr" class="flex justify-center transition-opacity">
            @if ($qrSvg)
                {!! $qrSvg !!}
            @else
                {{-- QR 作成用のライブラリが未導入のとき（composer require bacon/bacon-qr-code で解消） --}}
                <x-alert type="info">QRコードを表示するには、ターミナルで「composer require bacon/bacon-qr-code」を実行してください。</x-alert>
            @endif
        </div>

        <p class="mt-4 text-sm text-muted">入場するときに、この画面をスタッフに見せてください</p>
        <p class="mt-1 text-sm text-muted">
            有効期限：<span class="tabular-nums text-ink">{{ Format::dateTime($limit) }}</span>
            （残り <span id="qr-remaining" class="font-bold tabular-nums text-ink" data-limit="{{ $limit->getTimestamp() }}">--:--</span>）
        </p>
        <p id="qr-expired" class="mt-1 hidden text-sm font-medium text-danger">
            有効期限が切れました。「QRを更新」を押してください。
        </p>

        <x-slot:footer>
            {{-- 2) 更新ボタン：トークンを作り直す --}}
            <form method="POST" action="{{ route('myqr.refresh') }}" class="w-full">
                @csrf
                <input type="hidden" name="ticket_id" value="{{ $ticket->Ticket_id }}">
                <x-button type="submit" block>QRを更新</x-button>
            </form>
        </x-slot:footer>
    </x-card>

    <script>
        // 有効期限までの残り時間（分:秒）を1秒ごとに表示し、切れたら QR を薄くして案内を出す
        (function () {
            const remaining = document.getElementById('qr-remaining');
            const limit = Number(remaining.dataset.limit) * 1000;
            let timer = null;

            function tick() {
                const left = Math.max(0, Math.floor((limit - Date.now()) / 1000));
                remaining.textContent =
                    String(Math.floor(left / 60)).padStart(2, '0') + ':' + String(left % 60).padStart(2, '0');

                if (left === 0) {
                    document.getElementById('qr').classList.add('opacity-25');
                    document.getElementById('qr-expired').classList.remove('hidden');
                    if (timer) clearInterval(timer);
                    return false;
                }
                return true;
            }

            if (tick()) {
                timer = setInterval(tick, 1000);
            }
        })();
    </script>
</x-layout>
