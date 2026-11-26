{{-- マイチケット一覧（設計者：古市） --}}
<x-layout title="マイチケット">

    {{-- ── 左カラム：表示条件・並び替え ── --}}
    <x-slot:sidebar>
        {{-- 入力欄はすべて form="filter-form" で右側の検索フォームと一緒に送信する --}}
        <x-panel title="表示条件">
            @foreach ($filterOptions as $option)
                <x-checkbox name="filters[]" :value="$option" :checked="in_array($option, $filters, true)"
                            form="filter-form" class="js-auto-submit">
                    {{ $option }}
                </x-checkbox>
            @endforeach
        </x-panel>

        <x-panel title="並び替え">
            <x-select name="sort" :options="$sortOptions" :selected="$sort"
                      form="filter-form" class="js-auto-submit" />
        </x-panel>
    </x-slot:sidebar>

    {{-- ── 右カラム：検索 + 一覧 ── --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <form id="filter-form" method="GET" action="{{ route('myticket.index') }}" class="flex gap-2 sm:w-96">
            {{-- 初回表示と「全部外した」を区別するためのフラグ --}}
            <input type="hidden" name="filtered" value="1">
            <div class="flex-1"><x-input name="q" :value="$q" icon="search" placeholder="イベント名で検索" /></div>
            <x-button type="submit" variant="outline">検索</x-button>
        </form>
        <p class="text-sm text-muted"><span class="font-bold tabular-nums text-ink">{{ $tickets->count() }}</span> 件</p>
    </div>

    @if ($tickets->isEmpty())
        <x-empty>
            該当するチケットはありません
            <x-slot:hint>表示条件や検索キーワードを変えてみてください</x-slot:hint>
        </x-empty>
    @else
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($tickets as $ticket)
                @php
                    $day = \Carbon\Carbon::parse($ticket->Ticket_day)->locale('ja');
                    $hasTime = $day->format('H:i') !== '00:00';
                    $statusText = $ticket->status_label === '当選' ? '当選・未使用' : $ticket->status_label;
                    $isPast = in_array($ticket->status_label, ['使用済', '落選'], true);
                @endphp

                <x-card :ticket="$ticket->can_show_buttons" :class="$isPast ? 'opacity-75' : ''">
                    {{-- 公演日を主役にする（チケットの券面と同じ並び） --}}
                    <div class="flex items-start justify-between gap-2">
                        <p class="leading-none text-ink">
                            <span class="text-xs tabular-nums text-muted">{{ $day->format('Y') }}</span><br>
                            <span class="text-2xl font-bold tabular-nums tracking-tight">{{ $day->format('n.j') }}</span>
                            <span class="text-sm font-medium">({{ $day->isoFormat('ddd') }})</span>
                            @if ($hasTime)
                                <span class="ml-1 text-sm tabular-nums text-muted">{{ $day->format('H:i') }} 開演</span>
                            @endif
                        </p>
                        <x-badge :status="$ticket->status_label" class="shrink-0">{{ $statusText }}</x-badge>
                    </div>

                    <h3 class="mt-3 text-base font-bold leading-snug">{{ $ticket->Event_name }}</h3>

                    <p class="mt-1 text-sm text-muted">
                        座席 <span class="ml-1 font-medium text-ink">{{ $ticket->Seat_nums ?? '未定' }}</span>
                    </p>

                    {{-- 当選かつ未使用のときだけボタンを表示 --}}
                    @if ($ticket->can_show_buttons)
                        <x-slot:footer>
                            {{-- マイQR（/myqr?ticket_id=…） --}}
                            <x-button size="sm" class="flex-1"
                                :href="Route::has('myqr.show') ? route('myqr.show', ['ticket_id' => $ticket->Ticket_id]) : '#'">
                                QRを表示
                            </x-button>
                            {{-- 座席表（他担当の画面。ルート名とパラメータは要確認） --}}
                            <x-button size="sm" variant="success" class="flex-1"
                                :href="Route::has('seat.show') ? route('seat.show', ['ticket_id' => $ticket->Ticket_id]) : '#'">
                                座席表
                            </x-button>
                        </x-slot:footer>
                    @endif
                </x-card>
            @endforeach
        </div>
    @endif

    <script>
        // チェックボックス・並び替えを変更したら即座に再検索
        document.querySelectorAll('.js-auto-submit').forEach(function (el) {
            el.addEventListener('change', function () {
                document.getElementById('filter-form').submit();
            });
        });
    </script>
</x-layout>
