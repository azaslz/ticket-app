<?php

namespace App\Support;

/**
 * DB が使えるまでの仮データ（database/sql/schema.sql の列名・型に合わせている）
 *
 * 公演日は「今日」からの相対日付にしているので、いつ動かしても
 * 未来・過去の公演が両方そろう。
 * Apply_result は DB と同じく 1=当選 / 0=落選 / null=抽選中（App\Support\TicketStatus）
 *
 * マイQR・マイメールでも同じデータを使い回す予定。
 */
class DummyData
{
    /**
     * ACOUNT（ログイン・メールの宛名などに使う）
     * Acount_user_name がログインで入力するユーザーID
     */
    public static function accounts(): array
    {
        return [
            ['Acount_id' => 1, 'Acount_user_name' => 'user01',    'Position_id' => 1, 'Acount_name' => 'テスト 太郎'],
            ['Acount_id' => 2, 'Acount_user_name' => 'user02',    'Position_id' => 1, 'Acount_name' => 'サンプル 花子'],
            ['Acount_id' => 3, 'Acount_user_name' => 'company01', 'Position_id' => 2, 'Acount_name' => '企業 担当者'],
            ['Acount_id' => 4, 'Acount_user_name' => 'admin01',   'Position_id' => 3, 'Acount_name' => '管理者'],
        ];
    }

    /** POSITION（役職） */
    public static function positions(): array
    {
        return [
            ['Position_id' => 1, 'Position_name' => '一般'],
            ['Position_id' => 2, 'Position_name' => '企業'],
            ['Position_id' => 3, 'Position_name' => '管理者'],
        ];
    }

    /**
     * PASSWORD（テスト用。全員のパスワードは「pass1234」）
     * DB の Password_raw_pass（生パスワード・テストユーザーのみ）にあたる値から、
     * ログインのときにハッシュ（bcrypt）を作って照合する
     */
    public static function passwords(): array
    {
        return [
            ['Acount_id' => 1, 'Password_raw_pass' => 'pass1234'],
            ['Acount_id' => 2, 'Password_raw_pass' => 'pass1234'],
            ['Acount_id' => 3, 'Password_raw_pass' => 'pass1234'],
            ['Acount_id' => 4, 'Password_raw_pass' => 'pass1234'],
        ];
    }

    /** EVENT */
    public static function events(): array
    {
        return [
            ['Event_id' => 1, 'Event_name' => 'さくらフェス2026',        'Event_venue' => '名古屋ドーム'],
            ['Event_id' => 2, 'Event_name' => 'アコースティックナイト',  'Event_venue' => 'Zepp Nagoya'],
            ['Event_id' => 3, 'Event_name' => 'ウィンターライブ',        'Event_venue' => '日本ガイシホール'],
            ['Event_id' => 4, 'Event_name' => 'オーケストラ定期演奏会',  'Event_venue' => '愛知県芸術劇場'],
            ['Event_id' => 5, 'Event_name' => 'かがやきアイドル祭',      'Event_venue' => 'ポートメッセなごや'],
        ];
    }

    /** TICKET（Ticket_day は今日からの日数で作る） */
    public static function tickets(): array
    {
        // DB の Ticket_day は DATE 型（時刻なし）
        $day = fn (int $offset) => today()->addDays($offset)->format('Y-m-d');

        return [
            ['Ticket_id' => 101, 'Event_id' => 1, 'Ticket_name' => 'S席',     'Ticket_day' => $day(14),  'Ticket_price' => 9800],
            ['Ticket_id' => 102, 'Event_id' => 2, 'Ticket_name' => '1階立見', 'Ticket_day' => $day(3),   'Ticket_price' => 6500],
            ['Ticket_id' => 103, 'Event_id' => 3, 'Ticket_name' => 'アリーナ', 'Ticket_day' => $day(40),  'Ticket_price' => 11000],
            ['Ticket_id' => 104, 'Event_id' => 4, 'Ticket_name' => 'A席',     'Ticket_day' => $day(-10), 'Ticket_price' => 5000],
            ['Ticket_id' => 105, 'Event_id' => 5, 'Ticket_name' => '一般',    'Ticket_day' => $day(25),  'Ticket_price' => 7700],
            ['Ticket_id' => 106, 'Event_id' => 1, 'Ticket_name' => 'A席',     'Ticket_day' => $day(-30), 'Ticket_price' => 8000],
        ];
    }

    /** SEAT（Apply_id ごとに割り当てられた座席。当選分のみ） */
    public static function seats(): array
    {
        return [
            ['Apply_id' => 1, 'Seat_num' => 'A-12'],
            ['Apply_id' => 1, 'Seat_num' => 'A-13'],
            ['Apply_id' => 3, 'Seat_num' => 'アリーナB-8'],
            ['Apply_id' => 4, 'Seat_num' => '2階C-21'],
            ['Apply_id' => 4, 'Seat_num' => '2階C-22'],
            ['Apply_id' => 7, 'Seat_num' => 'A-14'],
        ];
    }

    /** APPLY（Acount_id = 1 が主な確認用ユーザー） */
    public static function applies(): array
    {
        return [
            ['Apply_id' => 1, 'Acount_id' => 1, 'Ticket_id' => 101, 'Apply_num' => 2, 'Apply_result' => 1],     // 当選・未使用
            ['Apply_id' => 2, 'Acount_id' => 1, 'Ticket_id' => 102, 'Apply_num' => 1, 'Apply_result' => null],  // 抽選中
            ['Apply_id' => 3, 'Acount_id' => 1, 'Ticket_id' => 103, 'Apply_num' => 1, 'Apply_result' => 1],     // 当選・未使用
            ['Apply_id' => 4, 'Acount_id' => 1, 'Ticket_id' => 104, 'Apply_num' => 2, 'Apply_result' => 1],     // 当選・入場済み（使用済）
            ['Apply_id' => 5, 'Acount_id' => 1, 'Ticket_id' => 105, 'Apply_num' => 1, 'Apply_result' => 0],     // 落選
            ['Apply_id' => 6, 'Acount_id' => 1, 'Ticket_id' => 106, 'Apply_num' => 1, 'Apply_result' => 0],     // 落選
            ['Apply_id' => 7, 'Acount_id' => 2, 'Ticket_id' => 101, 'Apply_num' => 1, 'Apply_result' => 1],     // 他人（表示されないこと）
        ];
    }

    /** VISITOR（入場者。Visitor_status が TicketStatus::VISITOR_USED なら使用済） */
    public static function visitors(): array
    {
        return [
            ['Visitor_id' => 1, 'Acount_id' => 1, 'Ticket_id' => 104, 'Apply_id' => 4, 'Visitor_status' => TicketStatus::VISITOR_USED],
        ];
    }

    /**
     * EMAIL（マイメール用）
     * Email_title / Email_text / Email_day は今の DB にはない列（追加予定として用意）。
     * null のものは、申し込みの結果から本文を自動で作る（DB の今の状態と同じ動き）。
     */
    public static function emails(): array
    {
        $at = fn (int $daysAgo, string $time) => today()->subDays($daysAgo)->setTimeFromTimeString($time)->format('Y-m-d H:i:s');

        return [
            ['Acount_id' => 1, 'Email_id' => 1, 'Apply_id' => 4, 'Email_status' => '既読', 'Email_day' => $at(20, '12:00'), 'Email_title' => null, 'Email_text' => null],
            ['Acount_id' => 1, 'Email_id' => 2, 'Apply_id' => 2, 'Email_status' => '既読', 'Email_day' => $at(6, '10:15'), 'Email_title' => null, 'Email_text' => null],
            ['Acount_id' => 1, 'Email_id' => 3, 'Apply_id' => 5, 'Email_status' => '未読', 'Email_day' => $at(2, '18:00'), 'Email_title' => null, 'Email_text' => null],
            ['Acount_id' => 1, 'Email_id' => 4, 'Apply_id' => 1, 'Email_status' => '未読', 'Email_day' => $at(1, '12:00'), 'Email_title' => null, 'Email_text' => null],
            [
                'Acount_id' => 1, 'Email_id' => 5, 'Apply_id' => 3, 'Email_status' => '未読', 'Email_day' => $at(0, '09:00'),
                'Email_title' => '【重要】開場時間変更のお知らせ',
                'Email_text'  => "ウィンターライブにお申し込みいただいた皆さまへ\n\n会場の準備の都合により、開場時間を30分早めて15:30とさせていただきます。\n開演時間に変更はありません。\n\nお手数ですが、マイチケットからチケットの内容をご確認ください。",
            ],
            ['Acount_id' => 2, 'Email_id' => 6, 'Apply_id' => 7, 'Email_status' => '未読', 'Email_day' => $at(1, '12:00'), 'Email_title' => null, 'Email_text' => null],  // 他人（表示されないこと）
        ];
    }
}
