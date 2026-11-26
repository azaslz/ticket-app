<?php

/**
 * チケット販売アプリ独自の設定
 */
return [

    // true : app/Support/DummyData.php の仮データを使う（DB未完成の間）
    // false: 実際の DB（.env の DB_*）を使う
    'use_dummy' => env('USE_DUMMY_DATA', true),

    // true にすると、ログインしていなくても DUMMY_ACOUNT_ID のユーザーとして画面を表示する
    // （ダミーモードのときだけ。ふだんは false にして、ログイン画面からログインする）
    'dummy_auto_login' => (bool) env('DUMMY_AUTO_LOGIN', false),
    'dummy_acount_id'  => (int) env('DUMMY_ACOUNT_ID', 1),

    // マイQR：QRコード（トークン）の有効期限（分）
    'qr_minutes' => (int) env('QR_MINUTES', 5),

    // ログイン：この回数続けて失敗すると、同じユーザーIDを一定時間ロックする
    'login_max_attempts' => (int) env('LOGIN_MAX_ATTEMPTS', 5),
    'login_lock_minutes' => (int) env('LOGIN_LOCK_MINUTES', 15),

];
