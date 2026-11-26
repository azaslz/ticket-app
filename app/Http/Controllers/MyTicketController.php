<?php

namespace App\Http\Controllers;

use App\Support\DummyData;
use App\Support\TicketStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/**
 * マイチケット一覧表示（設計者：古市）
 *
 * データの取得元は config('ticket.use_dummy') で切り替える。
 *   true : App\Support\DummyData の仮データ
 *   false: DB（database/sql/schema.sql の APPLY / TICKET / EVENT / SEAT / VISITOR）
 * 取得後の「絞り込み」「並び替え」「ボタン表示判定」はどちらでも同じ処理を通る。
 *
 * 結果（Apply_result の数値）と使用済（VISITOR）の判定は App\Support\TicketStatus にまとめている。
 */
class MyTicketController extends Controller
{
    /** フィルタで選べる値（画面表示順） */
    public const FILTERS = ['当選', '抽選中', '落選', '使用済'];

    /** 初期フィルタ */
    public const DEFAULT_FILTERS = ['当選', '抽選中'];

    /** ソートの選択肢（キー => 表示名） */
    public const SORTS = [
        'day_asc'     => '公演日（昇順）',
        'day_desc'    => '公演日（降順）',
        'event_asc'   => 'イベント名（昇順）',
        'event_desc'  => 'イベント名（降順）',
        'result_asc'  => '結果（当選→抽選中→落選）',
        'result_desc' => '結果（落選→抽選中→当選）',
    ];

    /** 初期ソート */
    public const DEFAULT_SORT = 'day_asc';

    /** 結果で並べるときの順序 */
    private const RESULT_ORDER = ['当選' => 1, '抽選中' => 2, '落選' => 3];

    public function index(Request $request)
    {
        // ── ログインユーザーの特定 ───────────────────────
        $acountId = self::currentAcountId($request);
        if (!$acountId) {
            $to = Route::has('login') ? route('login') : url('/');
            return redirect($to)->with('error', 'ログインしてください。');
        }

        // ── フィルタ値（hidden の filtered=1 で初回表示と区別） ──
        if ($request->has('filtered')) {
            $filters = array_values(array_intersect(
                (array) $request->input('filters', []),
                self::FILTERS
            ));
        } else {
            $filters = self::DEFAULT_FILTERS;
        }

        // ── ソート値（ホワイトリスト方式） ─────────────────
        $sort = $request->input('sort', self::DEFAULT_SORT);
        if (!array_key_exists($sort, self::SORTS)) {
            $sort = self::DEFAULT_SORT;
        }

        // ── 検索キーワード（イベント名の部分一致。画面イメージの「検索」欄） ──
        $q = trim((string) $request->input('q', ''));

        // ── 取得 → 状態付与 → 絞り込み → 並び替え ───────────
        $tickets = self::fetchApplies($acountId)
            ->map(function ($t) {
                $t->status_label     = self::isUsed($t) ? '使用済' : $t->result_label;
                $t->can_show_buttons = self::isAvailable($t);   // 当選かつ未使用
                return $t;
            })
            ->filter(fn ($t) => in_array($t->status_label, $filters, true))
            ->filter(fn ($t) => $q === '' || mb_stripos((string) $t->Event_name, $q) !== false)
            ->values();

        $tickets = $this->sortTickets($tickets, $sort);

        return view('myticket', [
            'tickets'       => $tickets,
            'filters'       => $filters,
            'sort'          => $sort,
            'q'             => $q,
            'filterOptions' => self::FILTERS,
            'sortOptions'   => self::SORTS,
        ]);
    }

    // ================================================================
    //  他画面（マイQR・マイメール）からも使う共通処理
    // ================================================================

    /**
     * ログイン中のユーザーID
     * ログインしていなければ null（各画面はログイン画面へ移動させる）
     */
    public static function currentAcountId(Request $request): ?int
    {
        // ログイン画面（LoginController）が成功時にセッションへ保存した ID
        $id = $request->session()->get('acount_id');

        // ダミーモードで DUMMY_AUTO_LOGIN=true のときだけ、ログインしていなくても仮ユーザーで表示する
        if (!$id && config('ticket.use_dummy') && config('ticket.dummy_auto_login')) {
            $id = config('ticket.dummy_acount_id');
        }
        return $id ? (int) $id : null;
    }

    /**
     * 指定ユーザーの申し込み一覧（イベント名・公演日付き）
     * 各要素は Apply_id, Ticket_id, Apply_result（数値）, result_label（当選/落選/抽選中）,
     *          is_used（入場済みか）, Apply_num, Apply_day, Ticket_name, Ticket_day, Event_name, Event_venue, Seat_nums を持つ
     */
    public static function fetchApplies(int $acountId): Collection
    {
        if (config('ticket.use_dummy')) {
            $tickets = collect(DummyData::tickets())->keyBy('Ticket_id');
            $events  = collect(DummyData::events())->keyBy('Event_id');
            $seats   = collect(DummyData::seats())->groupBy('Apply_id');
            $usedIds = collect(DummyData::visitors())
                ->where('Visitor_status', TicketStatus::VISITOR_USED)
                ->pluck('Apply_id')->all();

            return collect(DummyData::applies())
                ->where('Acount_id', $acountId)
                ->map(function ($a) use ($tickets, $events, $seats, $usedIds) {
                    $t = $tickets[$a['Ticket_id']];
                    return (object) [
                        'Apply_id'     => $a['Apply_id'],
                        'Ticket_id'    => $a['Ticket_id'],
                        'Apply_result' => $a['Apply_result'],
                        'Apply_num'    => $a['Apply_num'] ?? null,
                        'Apply_day'    => $a['Apply_day'] ?? null,
                        'result_label' => TicketStatus::resultLabel($a['Apply_result']),
                        'is_used'      => in_array($a['Apply_id'], $usedIds, true),
                        'Ticket_name'  => $t['Ticket_name'],
                        'Ticket_day'   => $t['Ticket_day'],
                        'Event_name'   => $events[$t['Event_id']]['Event_name'],
                        'Event_venue'  => $events[$t['Event_id']]['Event_venue'],
                        'Seat_nums'    => isset($seats[$a['Apply_id']])
                            ? $seats[$a['Apply_id']]->pluck('Seat_num')->implode(', ')
                            : null,
                    ];
                })
                ->values();
        }

        return DB::table('APPLY')
            ->join('TICKET', 'APPLY.Ticket_id', '=', 'TICKET.Ticket_id')
            ->join('EVENT', 'TICKET.Event_id', '=', 'EVENT.Event_id')
            ->where('APPLY.Acount_id', $acountId)
            ->select([
                'APPLY.Apply_id',
                'APPLY.Ticket_id',
                'APPLY.Apply_result',
                'APPLY.Apply_num',
                'APPLY.Apply_day',
                'TICKET.Ticket_name',
                'TICKET.Ticket_day',
                'EVENT.Event_name',
                'EVENT.Event_venue',
            ])
            // 座席は SEAT(Apply_id, Seat_num)。複数枚なら「A-12, A-13」のようにまとめる
            ->selectRaw("(SELECT GROUP_CONCAT(SEAT.Seat_num ORDER BY SEAT.Seat_num SEPARATOR ', ')
                          FROM SEAT WHERE SEAT.Apply_id = APPLY.Apply_id) AS Seat_nums")
            // 使用済＝この申し込みの入場者（VISITOR）が入場済みになっている
            ->selectRaw('EXISTS(SELECT 1 FROM VISITOR
                          WHERE VISITOR.Apply_id = APPLY.Apply_id AND VISITOR.Visitor_status = ?) AS is_used',
                        [TicketStatus::VISITOR_USED])
            ->get()
            ->map(function ($row) {
                $row->result_label = TicketStatus::resultLabel($row->Apply_result);
                $row->is_used      = (bool) $row->is_used;
                return $row;
            });
    }

    /** 使用済判定（当選 かつ 入場済み） */
    public static function isUsed($apply): bool
    {
        return TicketStatus::isWin($apply->Apply_result) && !empty($apply->is_used);
    }

    /** 当選かつ未使用の判定 */
    public static function isAvailable($apply): bool
    {
        return TicketStatus::isWin($apply->Apply_result) && !self::isUsed($apply);
    }

    // ================================================================

    /**
     * 並び替え。同順位のときは公演日（昇順）→ 申込ID で安定させる
     */
    private function sortTickets(Collection $tickets, string $sort): Collection
    {
        [$key, $dir] = explode('_', $sort);
        $sign = $dir === 'desc' ? -1 : 1;

        $primary = match ($key) {
            'event'  => fn ($a, $b) => strcmp((string) $a->Event_name, (string) $b->Event_name),
            'result' => fn ($a, $b) => (self::RESULT_ORDER[$a->result_label] ?? 9)
                                     <=> (self::RESULT_ORDER[$b->result_label] ?? 9),
            default  => fn ($a, $b) => strcmp((string) $a->Ticket_day, (string) $b->Ticket_day),
        };

        return $tickets->sort(function ($a, $b) use ($primary, $sign) {
            return ($primary($a, $b) * $sign)
                ?: strcmp((string) $a->Ticket_day, (string) $b->Ticket_day)
                ?: ($a->Apply_id <=> $b->Apply_id);
        })->values();
    }
}
