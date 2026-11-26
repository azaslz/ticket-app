<?php

namespace App\Support;

/**
 * マイメールの文面を作る
 * テンプレート（config/mail_templates.php）の { } に、DB から取った値を差し込む
 *
 *   $mail = MailTemplate::render($apply, $name);
 *   $mail['title'] / $mail['body'] / $mail['summary'] / $mail['hidden_summary'] / $mail['hide_until_read']
 */
class MailTemplate
{
    /** 申し込みの結果 → テンプレートの種類 */
    public static function typeFor(?object $apply): string
    {
        if (!$apply) {
            return 'notice';
        }
        return match ($apply->result_label) {
            '当選'  => 'win',
            '落選'  => 'lose',
            default => 'applied',
        };
    }

    /** 差し込む値（{name} などに入るもの） */
    public static function values(?object $apply, ?string $name): array
    {
        return [
            '{name}'      => $name ?: 'お客',
            '{event}'     => $apply->Event_name ?? '―',
            '{venue}'     => $apply->Event_venue ?? '―',
            '{ticket}'    => $apply->Ticket_name ?? '―',
            '{day}'       => Format::date($apply->Ticket_day ?? null),
            '{seats}'     => ($apply->Seat_nums ?? null) ?: '決まりしだいお知らせします',
            '{num}'       => isset($apply->Apply_num) ? Format::count($apply->Apply_num, '枚') : '―',
            '{apply_day}' => Format::dateTime($apply->Apply_day ?? null),
            '{apply_id}'  => $apply->Apply_id ?? '―',
        ];
    }

    /** テンプレートに値を差し込んだ件名・本文・一覧の1行 */
    public static function render(?object $apply, ?string $name): array
    {
        $type   = self::typeFor($apply);
        $tpl    = config("mail_templates.{$type}");
        $values = self::values($apply, $name);
        $fill   = fn (string $text) => strtr($text, $values);

        return [
            'type'            => $type,
            'title'           => $fill($tpl['title']),
            'body'            => $fill($tpl['body']),
            'summary'         => $fill($tpl['summary']),
            'hidden_summary'  => $fill(config('mail_templates.hidden_summary')),   // 未読のあいだ一覧に出す文
            'hide_until_read' => (bool) $tpl['hide_until_read'],
        ];
    }
}
