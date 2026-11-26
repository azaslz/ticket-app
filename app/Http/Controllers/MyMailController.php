<?php

namespace App\Http\Controllers;

use App\Support\DummyData;
use App\Support\MailTemplate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/**
 * マイメール一覧・本文表示（設計者：古市）
 *
 * 1) 画面起動時 … 自分宛てのメールを受信日時の新しい順に一覧表示。未読には「未読」マーク
 *                  抽選結果のメールは、開くまで当選・落選がわからないようにする
 *                  （件名は当選・落選とも「【抽選結果】」、未読のあいだは本文の書き出しを出さない）
 * 2) 行クリック … 本文を表示し、そのメールを「既読」に更新
 * 3) 本文のリンク … Ticket_id を渡してマイQR・座席表・マイチケットへ移動
 *
 * 今の EMAIL テーブル（Acount_id, Email_id, Apply_id, Email_status）には本文と受信日時の列がない。
 *   ・件名と本文 … 文面テンプレート（config/mail_templates.php）に、申し込みの情報（DB）を差し込んで作る
 *                  列（Email_title / Email_text）があればそちらを優先する
 *   ・受信日時   … 列（Email_day）があればそれで並べ、無ければ Email_id の大きい順（新しい順）
 * 列が追加されれば、このコードのままで自動的にそちらが使われる。
 */
class MyMailController extends Controller
{
    /** EMAIL.Email_status の値（※チームで要確認。違っていたらここだけ直す） */
    public const UNREAD = '未読';
    public const READ   = '既読';

    private const NOT_AVAILABLE = 'このメールは表示できません。一覧から選び直してください。';


    /** 1) 画面起動時：一覧 */
    public function index(Request $request)
    {
        $acountId = MyTicketController::currentAcountId($request);
        if (!$acountId) {
            return $this->toLogin();
        }

        $mails = $this->fetchMails($request, $acountId);

        return view('mymail', [
            'mails'       => $mails,
            'unreadCount' => $mails->where('is_unread', true)->count(),
        ]);
    }

    /** 2) 行クリック：本文を表示して既読にする */
    public function show(Request $request, int $emailId)
    {
        $acountId = MyTicketController::currentAcountId($request);
        if (!$acountId) {
            return $this->toLogin();
        }

        $mail = $this->fetchMails($request, $acountId)->firstWhere('Email_id', $emailId);
        if (!$mail) {
            return redirect()->route('mymail.index')->with('error', self::NOT_AVAILABLE);
        }

        if ($mail->is_unread) {
            $this->markAsRead($request, $acountId, $emailId);
            $mail->is_unread = false;
        }

        return view('mymail-show', [
            'mail'  => $mail,
            'links' => $this->links($mail->apply),
        ]);
    }

    /**
     * 【開発用】ダミーのメールをすべて未読に戻す（/mymail/reset。APP_ENV=local のときだけ使える）
     */
    public function resetDummy(Request $request)
    {
        $request->session()->forget('dummy_mail_read');

        return redirect()->route('mymail.index')->with('success', 'ダミーのメールを未読に戻しました。');
    }

    // ================================================================

    /**
     * 自分宛てのメール（新しい順）
     * 各要素：Email_id, is_unread, received_at(Carbon|null), title, body, summary, apply（関連する申し込み or null）
     */
    private function fetchMails(Request $request, int $acountId): Collection
    {
        if (config('ticket.use_dummy')) {
            $readIds = $request->session()->get('dummy_mail_read', []);
            $rows = collect(DummyData::emails())
                ->where('Acount_id', $acountId)
                ->map(function ($r) use ($readIds) {
                    $r = (object) $r;
                    if (in_array($r->Email_id, $readIds, true)) {
                        $r->Email_status = self::READ;
                    }
                    return $r;
                });
        } else {
            $rows = DB::table('EMAIL')->where('Acount_id', $acountId)->get();
        }

        $applies = MyTicketController::fetchApplies($acountId)->keyBy('Apply_id');
        $name    = $this->accountName($acountId);

        return $rows
            ->map(function ($r) use ($applies, $name) {
                $apply = $r->Apply_id ? ($applies[$r->Apply_id] ?? null) : null;

                // テンプレート（config/mail_templates.php）に DB の値を差し込む
                $mail = MailTemplate::render($apply, $name);

                // 件名・本文の列があればそちらを優先（その場合、件名と1行目で結果がわからないよう文面に注意）
                $hasOwnText = !empty($r->Email_text ?? null);
                $title = ($r->Email_title ?? null) ?: $mail['title'];
                $body  = $hasOwnText ? $r->Email_text : $mail['body'];
                $isUnread = ($r->Email_status ?? null) !== self::READ;   // 未設定（null）も未読として扱う

                // 一覧の2行目。抽選結果のメールは、未読のあいだ結果がわからない文にする
                if ($hasOwnText) {
                    $summary = strtok($body, "\n") ?: '';
                } elseif ($mail['hide_until_read'] && $isUnread) {
                    $summary = $mail['hidden_summary'];
                } else {
                    $summary = $mail['summary'];
                }

                return (object) [
                    'Email_id'    => (int) $r->Email_id,
                    'is_unread'   => $isUnread,
                    'received_at' => !empty($r->Email_day) ? Carbon::parse($r->Email_day) : null,
                    'title'       => $title,
                    'body'        => $body,
                    'summary'     => $summary,
                    'apply'       => $apply,
                ];
            })
            ->sort(function ($a, $b) {
                // 受信日時の新しい順。日時が無いときは Email_id の大きい順
                return ($b->received_at?->getTimestamp() ?? 0) <=> ($a->received_at?->getTimestamp() ?? 0)
                    ?: $b->Email_id <=> $a->Email_id;
            })
            ->values();
    }

    /** 既読に更新 */
    private function markAsRead(Request $request, int $acountId, int $emailId): void
    {
        if (config('ticket.use_dummy')) {
            $readIds = $request->session()->get('dummy_mail_read', []);
            $request->session()->put('dummy_mail_read', array_values(array_unique([...$readIds, $emailId])));
            return;
        }

        DB::table('EMAIL')
            ->where('Acount_id', $acountId)
            ->where('Email_id', $emailId)
            ->update(['Email_status' => self::READ]);
    }

    /** 宛名に使う氏名 */
    private function accountName(int $acountId): ?string
    {
        if (config('ticket.use_dummy')) {
            return collect(DummyData::accounts())->firstWhere('Acount_id', $acountId)['Acount_name'] ?? null;
        }
        return DB::table('ACOUNT')->where('Acount_id', $acountId)->value('Acount_name');
    }

    /**
     * 3) 本文の下に出す移動ボタン（Ticket_id を渡す）
     * 並びは「補助 → 主操作」（x-actions が主操作を右端に置く）
     */
    private function links(?object $apply): array
    {
        $links = [['label' => 'マイチケットを見る', 'url' => route('myticket.index'), 'variant' => 'outline']];

        if ($apply && MyTicketController::isAvailable($apply)) {
            $links[] = [
                'label'   => '座席表',
                'url'     => Route::has('seat.show') ? route('seat.show', ['ticket_id' => $apply->Ticket_id]) : '#',
                'variant' => 'success',
            ];
            $links[] = [
                'label'   => 'QRを表示',
                'url'     => route('myqr.show', ['ticket_id' => $apply->Ticket_id]),
                'variant' => 'primary',
            ];
        }

        return $links;
    }

    private function toLogin()
    {
        $to = Route::has('login') ? route('login') : url('/');
        return redirect($to)->with('error', 'ログインしてください。');
    }
}
