<?php

/**
 * ============================================================
 *  マイメールの文面テンプレート
 *
 *  申し込みの結果（APPLY.Apply_result）ごとに、件名・本文・一覧に出す1行を決める。
 *  { } で囲んだ部分に、データベースから取った値が入る。
 *  文面を変えたいときは、このファイルだけを直せばよい。
 *
 *  使える差し込み項目（どのテンプレートでも使える）
 *    {name}      … 氏名（ACOUNT.Acount_name）
 *    {event}     … イベント名（EVENT.Event_name）
 *    {venue}     … 会場（EVENT.Event_venue）
 *    {ticket}    … チケット名（TICKET.Ticket_name）
 *    {day}       … 公演日（TICKET.Ticket_day）例：2026/11/24(火)
 *    {seats}     … 座席（SEAT.Seat_num。複数枚は「A-12, A-13」）
 *    {num}       … 枚数（APPLY.Apply_num）例：2枚
 *    {apply_day} … 申し込み日時（APPLY.Apply_day）
 *    {apply_id}  … 申込番号（APPLY.Apply_id）
 *  値が入っていない項目は「―」（座席だけは「決まりしだいお知らせします」）になる。
 *
 *  注意：当選・落選のメールは、開くまで結果がわからないようにする
 *    ・title は当選・落選で同じ文にする（「当選」「落選」を書かない）
 *    ・hide_until_read を true にすると、未読のあいだは一覧に summary の代わりに hidden_summary を出す
 * ============================================================
 */
return [

    // 未読の抽選結果メールで、一覧に出す文（{ } の差し込みも使える）
    'hidden_summary' => '{event}の抽選結果が届いています。開いて確認してください。',

    // ── 抽選中（Apply_result が NULL）：申し込み受付 ──────────────
    'applied' => [
        'title'           => '【受付完了】{event}',
        'summary'         => '{event}の申し込みを受け付けました。',
        'hide_until_read' => false,
        'body'            => <<<'TXT'
{name} 様

TICKET HUB をご利用いただき、ありがとうございます。
以下の内容で申し込みを受け付けました。

イベント：{event}
チケット：{ticket}
公演日　：{day}
会場　　：{venue}
枚数　　：{num}
申込番号：{apply_id}

抽選結果は、決まりしだいマイメールでお知らせします。
TXT,
    ],

    // ── 当選（Apply_result = 1） ───────────────────────────
    'win' => [
        'title'           => '【抽選結果】{event}',
        'summary'         => '{event}の抽選の結果【当選】しました。',
        'hide_until_read' => true,
        'body'            => <<<'TXT'
{name} 様

{event}の抽選結果が届いています。開いて確認してください。
抽選の結果【当選】しました。

チケット：{ticket}
公演日　：{day}
会場　　：{venue}
座席　　：{seats}
枚数　　：{num}
申込番号：{apply_id}

入場するときは、マイQRの画面を入場口のスタッフに見せてください。
TXT,
    ],

    // ── 落選（Apply_result = 0） ───────────────────────────
    'lose' => [
        'title'           => '【抽選結果】{event}',
        'summary'         => '{event}の抽選の結果【落選】しました。',
        'hide_until_read' => true,
        'body'            => <<<'TXT'
{name} 様

{event}の抽選結果が届いています。開いて確認してください。
抽選の結果【落選】しました。

チケット：{ticket}
公演日　：{day}
会場　　：{venue}
枚数　　：{num}
申込番号：{apply_id}

またのお申し込みをお待ちしております。
TXT,
    ],

    // ── 申し込みにひもづかないお知らせ（Apply_id が NULL） ──────────
    // ※ 本文の列（Email_text）があればそちらが優先される
    'notice' => [
        'title'           => 'TICKET HUB からのお知らせ',
        'summary'         => 'TICKET HUB からのお知らせです。',
        'hide_until_read' => false,
        'body'            => <<<'TXT'
{name} 様

TICKET HUB からのお知らせです。
TXT,
    ],

];
