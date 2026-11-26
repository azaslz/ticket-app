<?php

namespace App\Support;

use Carbon\Carbon;

/**
 * 画面に出す日付・金額などの表記を全画面で統一するためのヘルパー
 * Blade では {{ Format::price(9800) }} のように使える（AppServiceProvider で登録）
 *
 *   Format::date('2026-11-24')              → 2026/11/24(火)
 *   Format::dateTime('2026-11-24 13:00:00') → 2026/11/24(火) 13:00
 *   Format::time('2026-11-24 13:00:00')     → 13:00
 *   Format::price(9800)                     → ¥9,800
 *   Format::count(2, '枚')                  → 2枚
 */
class Format
{
    public static function date($value): string
    {
        if (blank($value)) {
            return '―';
        }
        return Carbon::parse($value)->locale('ja')->isoFormat('YYYY/M/D(ddd)');
    }

    public static function dateTime($value): string
    {
        if (blank($value)) {
            return '―';
        }
        return self::date($value) . ' ' . Carbon::parse($value)->format('H:i');
    }

    public static function time($value): string
    {
        return blank($value) ? '―' : Carbon::parse($value)->format('H:i');
    }

    public static function price($value): string
    {
        return blank($value) ? '―' : '¥' . number_format((int) $value);
    }

    public static function count($value, string $unit = '件'): string
    {
        return number_format((int) $value) . $unit;
    }
}
