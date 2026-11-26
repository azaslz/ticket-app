<?php

namespace App\Support;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

/**
 * 文字列から QR コードの画像（SVG）を作る
 * 画像はテーブルに保存せず、表示のたびにトークンから作る（設計書どおり）
 *
 * 使うには、最初に1回だけターミナルで次を実行する：
 *   composer require bacon/bacon-qr-code
 * 未導入のときは null を返す（画面側で案内を出す）
 */
class QrCode
{
    public static function svg(string $text, int $size = 220): ?string
    {
        if (!class_exists(Writer::class)) {
            return null;
        }

        // 第2引数はまわりの余白（読み取りに必要な白い枠。モジュール2つ分）
        $renderer = new ImageRenderer(new RendererStyle($size, 2), new SvgImageBackEnd());
        $svg = (new Writer($renderer))->writeString($text);

        // 先頭の XML 宣言を消し、読み上げ用の説明を付ける
        $svg = preg_replace('/^<\?xml[^>]*\?>\s*/', '', $svg);
        return preg_replace('/<svg\b/', '<svg role="img" aria-label="入場用QRコード"', $svg, 1);
    }

    /** QR に入れるトークン（推測できないランダムな40文字） */
    public static function newToken(): string
    {
        return bin2hex(random_bytes(20));
    }
}
