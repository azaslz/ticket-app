<?php

namespace App\Http\Controllers;

use App\Support\QrCode;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/**
 * マイQR表示・更新（設計者：古市）
 *
 * 1) 画面起動時
 *    受け取った Ticket_id とログイン中の Acount_id から、自分のチケットかつ「当選」かつ「未使用」かを確認。
 *    満たさなければ「このチケットは表示できません」と表示してマイチケットへ戻る。
 *    有効なトークンを取得し、無い・期限切れなら作り直して保存する。
 * 2) 更新ボタン
 *    トークンを作り直して上書きし、表示し直す。
 *
 * トークンの保存先
 *    DB     … QR テーブル（主キー：Acount_id + Ticket_id。QR_token / QR_limit / Visitor_id）
 *    ダミー … セッション（config('ticket.use_dummy') が true のとき）
 * QRコードの画像は保存せず、表示のたびにトークンから作る（App\Support\QrCode）
 */
class MyQrController extends Controller
{
    private const NOT_AVAILABLE = 'このチケットは表示できません。マイチケットから選び直してください。';

    /** 1) 画面起動時 */
    public function show(Request $request)
    {
        $acountId = MyTicketController::currentAcountId($request);
        if (!$acountId) {
            return $this->toLogin();
        }

        $ticket = $this->findAvailableTicket($request, $acountId);
        if (!$ticket) {
            return redirect()->route('myticket.index')->with('error', self::NOT_AVAILABLE);
        }

        $qr = $this->getToken($acountId, $ticket);
        if (!$qr || $qr['limit']->isPast()) {
            $qr = $this->regenerate($acountId, $ticket);
        }

        return view('myqr', [
            'ticket' => $ticket,
            'token'  => $qr['token'],
            'limit'  => $qr['limit'],
            'qrSvg'  => QrCode::svg($qr['token']),
        ]);
    }

    /** 2) 更新ボタン */
    public function refresh(Request $request)
    {
        $acountId = MyTicketController::currentAcountId($request);
        if (!$acountId) {
            return $this->toLogin();
        }

        $ticket = $this->findAvailableTicket($request, $acountId);
        if (!$ticket) {
            return redirect()->route('myticket.index')->with('error', self::NOT_AVAILABLE);
        }

        $this->regenerate($acountId, $ticket);

        return redirect()
            ->route('myqr.show', ['ticket_id' => $ticket->Ticket_id])
            ->with('success', 'QRコードを更新しました。');
    }

    // ================================================================

    /** 自分の「当選」かつ「未使用」のチケットを探す（見つからなければ null） */
    private function findAvailableTicket(Request $request, int $acountId): ?object
    {
        $ticketId = filter_var($request->input('ticket_id'), FILTER_VALIDATE_INT);
        if ($ticketId === false) {
            return null;
        }

        $ticket = MyTicketController::fetchApplies($acountId)->firstWhere('Ticket_id', $ticketId);

        return ($ticket && MyTicketController::isAvailable($ticket)) ? $ticket : null;
    }

    /** 保存されているトークン（['token' => ..., 'limit' => Carbon]、無ければ null） */
    private function getToken(int $acountId, object $ticket): ?array
    {
        if (config('ticket.use_dummy')) {
            $saved = session($this->sessionKey($acountId, $ticket));
            return $saved ? ['token' => $saved['token'], 'limit' => Carbon::parse($saved['limit'])] : null;
        }

        $row = DB::table('QR')
            ->where('Acount_id', $acountId)
            ->where('Ticket_id', $ticket->Ticket_id)
            ->first();

        return $row ? ['token' => $row->QR_token, 'limit' => Carbon::parse($row->QR_limit)] : null;
    }

    /** トークンを作り直して保存する（古いトークンは上書き） */
    private function regenerate(int $acountId, object $ticket): array
    {
        $token = QrCode::newToken();
        $limit = now()->addMinutes(config('ticket.qr_minutes', 5));

        if (config('ticket.use_dummy')) {
            session([$this->sessionKey($acountId, $ticket) => [
                'token' => $token,
                'limit' => $limit->toDateTimeString(),
            ]]);
        } else {
            // QR.Visitor_id は必須のため、この申し込みの入場者（VISITOR）を探して入れる
            $visitorId = DB::table('VISITOR')
                ->where('Apply_id', $ticket->Apply_id)
                ->where('Acount_id', $acountId)
                ->value('Visitor_id');

            DB::table('QR')->updateOrInsert(
                ['Acount_id' => $acountId, 'Ticket_id' => $ticket->Ticket_id],
                [
                    'Visitor_id' => $visitorId ?? 0,   // ※ 入場者が未登録のときの扱いはチームで要確認
                    'QR_token'   => $token,
                    'QR_limit'   => $limit->toDateTimeString(),
                ]
            );
        }

        return ['token' => $token, 'limit' => $limit];
    }

    private function sessionKey(int $acountId, object $ticket): string
    {
        return "dummy_qr.{$acountId}.{$ticket->Ticket_id}";
    }

    private function toLogin()
    {
        $to = Route::has('login') ? route('login') : url('/');
        return redirect($to)->with('error', 'ログインしてください。');
    }
}
