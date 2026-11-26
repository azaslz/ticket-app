{{--
    ■ パターン：完了・結果
    使う画面：申し込み完了、退会完了、パスワード変更完了 など
    幅：narrow ／ 結果＝ x-alert ／ 内容＝ x-card + x-info ／ 次の操作＝ x-actions
--}}
<x-layout title="申し込み完了" width="narrow">

    <x-alert type="success">申し込みを受け付けました。抽選結果はマイメールでお知らせします。</x-alert>

    <x-card>
        <x-slot:title>申し込み内容</x-slot:title>
        <x-info class="mt-3" :items="[
            'イベント' => 'さくらフェス2026',
            '公演日'   => Format::dateTime(now()->addDays(14)->setTime(13, 0)),
            '席種'     => 'S席',
            '枚数'     => Format::count(2, '枚'),
            '合計'     => Format::price(19600),
        ]" />
    </x-card>

    <x-actions>
        <x-button variant="outline" href="#">イベント一覧へ</x-button>
        <x-button href="#">マイチケットを見る</x-button>
    </x-actions>
</x-layout>
