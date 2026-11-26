<?php

namespace App\Http\Controllers;

use App\Support\DummyData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

/**
 * ログイン認証（設計者：古市）
 *
 * 1) 画面起動時 … ユーザーID・パスワードを空にして表示
 * 2) ログインボタン
 *    ・どちらかが空欄 → 「必須項目を入力してください」
 *    ・ACOUNT（Acount_user_name）と PASSWORD（Password_hash_pass）で照合（入力パスワードをハッシュ化して比べる）
 *    ・不一致 → 「ユーザーIDまたはパスワードが正しくありません」、失敗回数 +1
 *    ・失敗回数が規定回数（5回）に達したら、そのユーザーIDを一定時間（15分）ロック
 *    ・成功 → 失敗回数を0に戻し、POSITION.Position_name で移動先を分ける
 *        一般 → マイページ ／ 企業 → 企業ページ ／ 管理者 → 管理者ページ
 * 3) 「パスワードを忘れた方」 … パスワード変更画面へ（画面側のリンク）
 *
 * 失敗回数とロックは、ACOUNT テーブルに列がないため Laravel の RateLimiter（キャッシュ）で数えている。
 * 回数と時間は config/ticket.php の login_max_attempts / login_lock_minutes で変えられる。
 */
class LoginController extends Controller
{
    private const REQUIRED = '必須項目を入力してください';
    private const MISMATCH = 'ユーザーIDまたはパスワードが正しくありません';

    /**
     * 役職名 → 移動先のルート名
     * まだ画面がないものは、できるまで仮の移動先（FALLBACK）を使う
     */
    private const DESTINATIONS = [
        '一般'   => 'mypage',
        '企業'   => 'company.index',
        '管理者' => 'admin.index',
    ];

    /** 1) 画面起動時 */
    public function show()
    {
        return view('login');
    }

    /** 2) ログインボタン */
    public function login(Request $request)
    {
        $userName = trim((string) $request->input('user_name', ''));
        $password = (string) $request->input('password', '');

        // 必須チェック
        if ($userName === '' || $password === '') {
            return $this->fail($request, self::REQUIRED);
        }

        // ロック中か
        $key = $this->lockKey($userName);
        if (RateLimiter::tooManyAttempts($key, config('ticket.login_max_attempts', 5))) {
            $minutes = (int) ceil(RateLimiter::availableIn($key) / 60);
            return $this->fail($request, "ログインに続けて失敗したため、このユーザーIDはロックされています。{$minutes}分ほど待ってから、もう一度お試しください。");
        }

        // 照合
        $account = $this->findAccount($userName);
        if (!$account || !$this->passwordMatches($password, $account)) {
            RateLimiter::hit($key, config('ticket.login_lock_minutes', 15) * 60);   // 失敗回数 +1
            return $this->fail($request, self::MISMATCH);
        }

        // 成功：失敗回数を0に戻し、ログイン状態を保存
        RateLimiter::clear($key);
        $request->session()->regenerate();   // なりすまし防止のためセッションIDを作り直す
        $request->session()->put([
            'acount_id'     => (int) $account->Acount_id,
            'acount_name'   => $account->Acount_name,
            'position_name' => $account->Position_name,
        ]);

        return $this->redirectByPosition($account->Position_name);
    }

    /** ログアウト（ログイン画面の「ログアウト」ボタン） */
    public function logout(Request $request)
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'ログアウトしました。');
    }

    // ================================================================

    /** ユーザーID（Acount_user_name）からアカウント・パスワード・役職名を取る */
    private function findAccount(string $userName): ?object
    {
        if (config('ticket.use_dummy')) {
            $account = collect(DummyData::accounts())->firstWhere('Acount_user_name', $userName);
            if (!$account) {
                return null;
            }
            $raw      = collect(DummyData::passwords())->firstWhere('Acount_id', $account['Acount_id'])['Password_raw_pass'] ?? null;
            $position = collect(DummyData::positions())->firstWhere('Position_id', $account['Position_id'])['Position_name'] ?? null;

            return (object) [
                'Acount_id'          => $account['Acount_id'],
                'Acount_name'        => $account['Acount_name'],
                'Password_hash_pass' => $raw !== null ? Hash::make($raw) : null,   // DB と同じくハッシュにしてから照合する
                'Position_name'      => $position,
            ];
        }

        return DB::table('ACOUNT')
            ->join('PASSWORD', 'ACOUNT.Acount_id', '=', 'PASSWORD.Acount_id')
            ->leftJoin('POSITION', 'ACOUNT.Position_id', '=', 'POSITION.Position_id')
            ->where('ACOUNT.Acount_user_name', $userName)
            ->select([
                'ACOUNT.Acount_id',
                'ACOUNT.Acount_name',
                'PASSWORD.Password_hash_pass',
                'POSITION.Position_name',
            ])
            ->first();
    }

    /**
     * 入力パスワードをハッシュ化して、保存されているハッシュと比べる
     * Password_hash_pass は PHP の password_hash()（bcrypt）で作られている前提
     */
    private function passwordMatches(string $password, object $account): bool
    {
        if (empty($account->Password_hash_pass)) {
            return false;
        }
        try {
            return Hash::check($password, $account->Password_hash_pass);
        } catch (\RuntimeException $e) {
            // bcrypt 以外の形式で保存されていると、ここに来る（DB 担当とハッシュの作り方を合わせる）
            Log::warning('ログイン：パスワードのハッシュ形式が bcrypt ではありません', ['acount_id' => $account->Acount_id]);
            return false;
        }
    }

    /** 役職名で移動先を分ける（画面がまだないときは仮の移動先） */
    private function redirectByPosition(?string $positionName)
    {
        $route = self::DESTINATIONS[$positionName] ?? null;

        if ($route && Route::has($route)) {
            return redirect()->route($route);
        }

        // 仮の移動先（画面ができたら自動でそちらへ移る）
        //   一般 → マイチケット ／ それ以外 → ログイン画面に「準備中」と表示
        if ($positionName === '一般') {
            return redirect()->route('myticket.index');
        }
        return redirect()->route('login')->with('success', "ログインしました（{$positionName}向けのページは準備中です）。");
    }

    /** 失敗したとき：パスワードは消し、ユーザーIDは残して戻す */
    private function fail(Request $request, string $message)
    {
        return redirect()->route('login')
            ->withErrors(['login' => $message])
            ->withInput($request->only('user_name'));
    }

    /** ロックの単位（ユーザーIDごと。大文字・小文字の違いは同じものとして数える） */
    private function lockKey(string $userName): string
    {
        return 'login:' . mb_strtolower($userName);
    }
}
