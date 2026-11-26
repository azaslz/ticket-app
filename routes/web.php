<?php

use Illuminate\Support\Facades\Route;

// トップ（ヘッダーの「TICKET HUB」もここに来る）
// ※ トップページができるまでは、マイチケットへ移動させる。できたらこの1行を差し替える
Route::get('/', fn () => redirect()->route('myticket.index'));

// ログイン（画面 ／ ログインボタン ／ ログアウト）
Route::get('/login', [\App\Http\Controllers\LoginController::class, 'show'])
    ->name('login');
Route::post('/login', [\App\Http\Controllers\LoginController::class, 'login'])
    ->name('login.attempt');
Route::post('/logout', [\App\Http\Controllers\LoginController::class, 'logout'])
    ->name('logout');

// マイチケット一覧（フィルタ・ソートは GET パラメータで受け取る）
Route::get('/myticket', [\App\Http\Controllers\MyTicketController::class, 'index'])
    ->name('myticket.index');

// マイQR（表示：/myqr?ticket_id=… ／ 更新ボタン：POST）
Route::get('/myqr', [\App\Http\Controllers\MyQrController::class, 'show'])
    ->name('myqr.show');
Route::post('/myqr/refresh', [\App\Http\Controllers\MyQrController::class, 'refresh'])
    ->name('myqr.refresh');

// マイメール（一覧 ／ 本文：/mymail/{Email_id}）
Route::get('/mymail', [\App\Http\Controllers\MyMailController::class, 'index'])
    ->name('mymail.index');
Route::get('/mymail/{emailId}', [\App\Http\Controllers\MyMailController::class, 'show'])
    ->whereNumber('emailId')
    ->name('mymail.show');

// UI 見本ページ・開発用の操作（開発中だけ表示。本番 APP_ENV=production では 404 になる）
if (app()->environment('local')) {
    // ダミーのメールをすべて未読に戻す
    Route::get('/mymail/reset', [\App\Http\Controllers\MyMailController::class, 'resetDummy'])
        ->name('mymail.reset');

    Route::view('/ui', 'ui.index');
    Route::get('/ui/{pattern}', fn (string $pattern) => view('ui.' . $pattern))
        ->whereIn('pattern', ['list', 'detail', 'form', 'complete', 'menu', 'table']);
}
