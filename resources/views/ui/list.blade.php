{{--
    ■ パターン：縦の一覧（絞り込みなし）
    使う画面：イベント一覧、マイメール、マイファンクラブ など
    幅：medium ／ 1件＝ x-list-item ／ ページ送り＝ x-pagination

    コピーしたら @php の見本データを消し、コントローラーから $events を渡す
--}}
@php
    // ▼ 見本データ（コピーしたら消す）
    $q = '';
    $events = new \Illuminate\Pagination\LengthAwarePaginator([
        (object) ['id' => 1, 'name' => 'さくらフェス2026',       'day' => now()->addDays(14)->setTime(13, 0), 'venue' => '名古屋ドーム',       'status' => '受付中'],
        (object) ['id' => 2, 'name' => 'アコースティックナイト', 'day' => now()->addDays(3)->setTime(18, 30), 'venue' => 'Zepp Nagoya',        'status' => '受付中'],
        (object) ['id' => 3, 'name' => 'ウィンターライブ',       'day' => now()->addDays(40)->setTime(17, 0), 'venue' => '日本ガイシホール',   'status' => '受付終了'],
    ], 25, 3, 1, ['path' => url()->current()]);
    // ▲ 見本データここまで
@endphp

<x-layout title="イベント一覧" width="medium">

    <form method="GET" action="{{ url()->current() }}" class="flex gap-2">
        <div class="flex-1"><x-input name="q" :value="$q" icon="search" placeholder="イベント名・アーティスト名で検索" /></div>
        <x-button type="submit" variant="outline">検索</x-button>
    </form>

    @if ($events->isEmpty())
        <x-empty>
            該当するイベントはありません
            <x-slot:hint>別のキーワードで検索してください</x-slot:hint>
        </x-empty>
    @else
        <x-list>
            @foreach ($events as $event)
                <x-list-item href="#">
                    <p class="font-bold">{{ $event->name }}</p>
                    <p class="mt-0.5 text-muted">{{ Format::dateTime($event->day) }}　{{ $event->venue }}</p>
                    <x-slot:aside>
                        <x-badge :color="$event->status === '受付中' ? 'success' : 'muted'">{{ $event->status }}</x-badge>
                    </x-slot:aside>
                </x-list-item>
            @endforeach
        </x-list>

        <x-pagination :paginator="$events" />
    @endif
</x-layout>
