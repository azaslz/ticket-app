-- ============================================================
--  テスト用データ（test_schema.sql を実行したあとに実行する）
--
--  app/Support/DummyData.php（ダミーモード）と同じ内容。
--  日付は「今日から何日後」で入れるので、いつ流しても
--  未来の公演・過去の公演がそろう。
--
--  テスト用アカウント（パスワードはすべて pass1234）
--    user01    … 一般（テスト 太郎）  ← マイチケット等の確認はこれ
--    user02    … 一般（サンプル 花子）← 他人のデータが見えないことの確認用
--    company01 … 企業
--    admin01   … 管理者
-- ============================================================

SET NAMES utf8mb4;

-- 役職
INSERT INTO `POSITION` (`Position_id`, `Position_name`) VALUES
    (1, '一般'),
    (2, '企業'),
    (3, '管理者');

-- 主催企業
INSERT INTO `COMPANY` (`Company_id`, `Company_name`) VALUES
    (1, 'チケットハブ株式会社');

-- アカウント（Acount_user_name がログインで入力するユーザーID）
INSERT INTO `ACOUNT` (`Acount_id`, `Acount_user_name`, `Position_id`, `Company_id`, `Acount_name`, `Acount_birthday`, `Acount_address`, `Acount_phone_number`, `Acount_gender`) VALUES
    (1, 'user01',    1, NULL, 'テスト 太郎',   '2004-04-01', '静岡県静岡市葵区1-1-1', '090-0000-0001', '未回答'),
    (2, 'user02',    1, NULL, 'サンプル 花子', '2003-08-15', '静岡県浜松市中央区2-2-2', '090-0000-0002', '未回答'),
    (3, 'company01', 2, 1,    '企業 担当者',   '1990-01-01', '東京都千代田区3-3-3', '03-0000-0003', '未回答'),
    (4, 'admin01',   3, NULL, '管理者',        '1985-01-01', '東京都千代田区4-4-4', '03-0000-0004', '未回答');

-- パスワード（Password_hash_pass は PHP の password_hash()（bcrypt）で作った「pass1234」のハッシュ）
INSERT INTO `PASSWORD` (`Acount_id`, `Password_hash_pass`, `Password_raw_pass`) VALUES
    (1, '$2y$12$naVrcCN8P3qv.5Tj0hPDZuMuoK6z85T.SeP7uk2/0i0N2exCxqT5q', 'pass1234'),
    (2, '$2y$12$naVrcCN8P3qv.5Tj0hPDZuMuoK6z85T.SeP7uk2/0i0N2exCxqT5q', 'pass1234'),
    (3, '$2y$12$naVrcCN8P3qv.5Tj0hPDZuMuoK6z85T.SeP7uk2/0i0N2exCxqT5q', 'pass1234'),
    (4, '$2y$12$naVrcCN8P3qv.5Tj0hPDZuMuoK6z85T.SeP7uk2/0i0N2exCxqT5q', 'pass1234');

-- イベント
INSERT INTO `EVENT` (`Event_id`, `Event_name`, `Event_text`, `Event_limit`, `Event_venue`) VALUES
    (1, 'さくらフェス2026',       '春の野外音楽フェス',         NOW() + INTERVAL 7 DAY,  '名古屋ドーム'),
    (2, 'アコースティックナイト', 'アコースティック中心のライブ', NOW() - INTERVAL 3 DAY,  'Zepp Nagoya'),
    (3, 'ウィンターライブ',       '冬の特別公演',               NOW() + INTERVAL 20 DAY, '日本ガイシホール'),
    (4, 'オーケストラ定期演奏会', 'クラシックの定期公演',       NOW() - INTERVAL 30 DAY, '愛知県芸術劇場'),
    (5, 'かがやきアイドル祭',     'アイドルの合同ライブ',       NOW() + INTERVAL 10 DAY, 'ポートメッセなごや');

-- チケット（Ticket_day は DATE 型）
INSERT INTO `TICKET` (`Ticket_id`, `Event_id`, `Company_id`, `Ticket_name`, `Ticket_day`, `Ticket_price`) VALUES
    (101, 1, 1, 'S席',      CURDATE() + INTERVAL 14 DAY, 9800),
    (102, 2, 1, '1階立見',  CURDATE() + INTERVAL 3 DAY,  6500),
    (103, 3, 1, 'アリーナ', CURDATE() + INTERVAL 40 DAY, 11000),
    (104, 4, 1, 'A席',      CURDATE() - INTERVAL 10 DAY, 5000),
    (105, 5, 1, '一般',     CURDATE() + INTERVAL 25 DAY, 7700),
    (106, 1, 1, 'A席',      CURDATE() - INTERVAL 30 DAY, 8000);

-- 申し込み（Apply_result：1=当選 / 0=落選 / NULL=抽選中）
INSERT INTO `APPLY` (`Apply_id`, `Acount_id`, `Ticket_id`, `Apply_num`, `Apply_day`, `Apply_result`) VALUES
    (1, 1, 101, 2, NOW() - INTERVAL 30 DAY, 1),     -- 当選・未使用
    (2, 1, 102, 1, NOW() - INTERVAL 6 DAY,  NULL),  -- 抽選中
    (3, 1, 103, 1, NOW() - INTERVAL 15 DAY, 1),     -- 当選・未使用
    (4, 1, 104, 2, NOW() - INTERVAL 40 DAY, 1),     -- 当選・入場済み（使用済）
    (5, 1, 105, 1, NOW() - INTERVAL 20 DAY, 0),     -- 落選
    (6, 1, 106, 1, NOW() - INTERVAL 60 DAY, 0),     -- 落選
    (7, 2, 101, 1, NOW() - INTERVAL 30 DAY, 1);     -- 他人（user01 に表示されないこと）

-- 座席（当選分のみ）
INSERT INTO `SEAT` (`Apply_id`, `Seat_num`) VALUES
    (1, 'A-12'),
    (1, 'A-13'),
    (3, 'アリーナB-8'),
    (4, '2階C-21'),
    (4, '2階C-22'),
    (7, 'A-14');

-- 入場者（Visitor_status が「入場済」なら使用済）
INSERT INTO `VISITOR` (`Visitor_id`, `Acount_id`, `Ticket_id`, `Apply_id`, `Visitor_status`) VALUES
    (1, 1, 104, 4, '入場済'),
    (2, 1, 101, 1, '未入場'),
    (3, 1, 103, 3, '未入場'),
    (4, 2, 101, 7, '未入場');

-- メール（Email_title / Email_text / Email_day はテスト用に追加した列。NULL なら文面テンプレートで作る）
INSERT INTO `EMAIL` (`Acount_id`, `Email_id`, `Apply_id`, `Email_status`, `Email_day`, `Email_title`, `Email_text`) VALUES
    (1, 1, 4, '既読', TIMESTAMP(CURDATE() - INTERVAL 20 DAY, '12:00:00'), NULL, NULL),
    (1, 2, 2, '既読', TIMESTAMP(CURDATE() - INTERVAL 6 DAY,  '10:15:00'), NULL, NULL),
    (1, 3, 5, '未読', TIMESTAMP(CURDATE() - INTERVAL 2 DAY,  '18:00:00'), NULL, NULL),
    (1, 4, 1, '未読', TIMESTAMP(CURDATE() - INTERVAL 1 DAY,  '12:00:00'), NULL, NULL),
    (1, 5, 3, '未読', TIMESTAMP(CURDATE(), '09:00:00'),
        '【重要】開場時間変更のお知らせ',
        'ウィンターライブにお申し込みいただいた皆さまへ\n\n会場の準備の都合により、開場時間を30分早めて15:30とさせていただきます。\n開演時間に変更はありません。\n\nお手数ですが、マイチケットからチケットの内容をご確認ください。'),
    (2, 6, 7, '未読', TIMESTAMP(CURDATE() - INTERVAL 1 DAY,  '12:00:00'), NULL, NULL);

-- アーティスト・参加アーティスト
INSERT INTO `ARTIST` (`Artist_id`, `Artist_name`) VALUES
    (1, 'サクラバンド'),
    (2, 'アコースティック・トリオ'),
    (3, 'かがやきガールズ');
INSERT INTO `PARTARTIST` (`Ticket_id`, `Artist_id`) VALUES
    (101, 1), (106, 1), (102, 2), (105, 3);

-- ファンクラブ・加入済みファンクラブ（ファンクラブ担当の確認用）
INSERT INTO `FANCLUB` (`Fanclub_id`, `Fanclub_name`) VALUES
    (1, 'サクラバンド公式ファンクラブ'),
    (2, 'アコースティック倶楽部'),
    (3, 'かがやきガールズ応援団');
INSERT INTO `JOINFC` (`Acount_id`, `Fanclub_id`, `Joinfc_join_day`, `Joinfc_limit`, `Joinfc_status`) VALUES
    (1, 1, CURDATE() - INTERVAL 100 DAY, CURDATE() + INTERVAL 265 DAY, '加入中'),
    (1, 3, CURDATE() - INTERVAL 400 DAY, CURDATE() - INTERVAL 35 DAY,  '退会済'),
    (2, 2, CURDATE() - INTERVAL 50 DAY,  CURDATE() + INTERVAL 315 DAY, '加入中');
