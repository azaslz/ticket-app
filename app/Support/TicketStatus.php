<?php

namespace App\Support;

/**
 * チケットの状態（当選・落選・抽選中・使用済）の判定を1か所にまとめたもの
 * マイチケット・マイQR・マイメールなど、どの画面でもこのクラスを使う
 *
 * DB（database/sql/schema.sql）の対応：
 *   APPLY.Apply_result（TINYINT(1)）
 *       1    … 当選
 *       0    … 落選
 *       NULL … 抽選中（まだ結果が入っていない）
 *   VISITOR.Visitor_status
 *       VISITOR_USED の値 … 入場済み（＝使用済）
 *
 * ※ 上の値はチームで要確認。違っていたら、この下の定数だけを直せばよい
 */
class TicketStatus
{
    public const RESULT_WIN  = 1;
    public const RESULT_LOSE = 0;

    /** VISITOR.Visitor_status で「入場済み」を表す値 */
    public const VISITOR_USED = '入場済';

    /** APPLY.Apply_result の値 → 画面に出す結果名 */
    public static function resultLabel($applyResult): string
    {
        if ($applyResult === null || $applyResult === '') {
            return '抽選中';
        }
        return match ((int) $applyResult) {
            self::RESULT_WIN  => '当選',
            self::RESULT_LOSE => '落選',
            default           => '抽選中',
        };
    }

    /** 当選かどうか */
    public static function isWin($applyResult): bool
    {
        return self::resultLabel($applyResult) === '当選';
    }
}
