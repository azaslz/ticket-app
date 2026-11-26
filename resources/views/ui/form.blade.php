{{--
    ■ パターン：入力フォーム
    使う画面：申し込みフォーム、アカウント登録、パスワード変更、ログイン など
    幅：narrow ／ フォーム＝ x-form ／ 1つ選ぶ＝ x-radio-group ／ ボタン＝ actions スロット

    ボタンは「キャンセル（outline）→ 主操作（primary）」の順に書く
--}}
<x-layout title="さくらフェス2026 申し込み" width="narrow" :back="['url' => '#', 'label' => 'イベント詳細へ戻る']">

    <x-form action="#">
        <x-radio-group name="ticket" label="公演日" required
            :options="['1' => '11/24(火) 13:00', '2' => '11/25(水) 13:00', '3' => '11/26(木) 18:00']" />

        <x-radio-group name="seat" label="席種" required
            :options="['s' => 'S席 ¥9,800', 'a' => 'A席 ¥8,000']" />

        <x-select name="num" label="枚数" required :options="['1' => '1枚', '2' => '2枚', '3' => '3枚', '4' => '4枚']" />

        <x-textarea name="note" label="備考" rows="3" help="車いす席の希望などがあれば入力してください" />

        <x-slot:actions>
            <x-button variant="outline" href="#">キャンセル</x-button>
            <x-button type="submit">申し込む</x-button>
        </x-slot:actions>
    </x-form>
</x-layout>
