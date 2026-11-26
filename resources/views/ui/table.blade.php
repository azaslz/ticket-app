{{--
    ■ パターン：表
    使う画面：企業イベント一覧 など（項目が多く、横に比べたいデータ）
    幅：wide ／ 表＝ x-table ／ 数字の列は右寄せ + tabular-nums
--}}
@php
    // ▼ 見本データ（コピーしたら消す）
    $rows = [
        ['さくらフェス2026',       now()->addDays(14), 1200, 3420, '受付中'],
        ['アコースティックナイト', now()->addDays(3),  300,  280,  '受付中'],
        ['ウィンターライブ',       now()->addDays(40), 800,  1950, '受付終了'],
    ];
    // ▲ 見本データここまで
@endphp

<x-layout title="企業イベント一覧">

    <x-section title="開催予定のイベント" description="申し込み状況を確認できます">
        <x-slot:action><x-button size="sm" href="#">イベントを作成</x-button></x-slot:action>

        <x-table>
            <thead>
                <tr><th>イベント名</th><th>公演日</th><th class="text-right">定員</th><th class="text-right">申込数</th><th>状態</th><th></th></tr>
            </thead>
            <tbody>
                @foreach ($rows as [$name, $day, $cap, $applies, $status])
                    <tr>
                        <td class="font-medium">{{ $name }}</td>
                        <td class="whitespace-nowrap tabular-nums">{{ Format::date($day) }}</td>
                        <td class="text-right tabular-nums">{{ number_format($cap) }}</td>
                        <td class="text-right tabular-nums">{{ number_format($applies) }}</td>
                        <td><x-badge :color="$status === '受付中' ? 'success' : 'muted'">{{ $status }}</x-badge></td>
                        <td class="text-right"><x-button size="sm" variant="outline" href="#">編集</x-button></td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
    </x-section>
</x-layout>
