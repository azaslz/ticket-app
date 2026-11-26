-- ============================================================
--  テスト用DB（各自の PC の MySQL で使う）
--
--  本番の構造（database/sql/schema.sql）を土台に、
--  各自のコードに必要な列・テーブルを「テスト用の追加」として足したもの。
--  ※ 本番の DB に入れ替える前に、下の「テスト用の追加」と同じものを
--    DB 担当に本番へ追加してもらう必要がある（一覧は docs/TEST_DB.md）
--
--  使い方（A5:SQL Mk-2）
--    1. ローカル（127.0.0.1）の MySQL に、データベース ticket_app を作る
--         CREATE DATABASE ticket_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
--    2. ticket_app を開いて、このファイルを実行する
--    3. 続けて test_data.sql を実行する
--
--  注意：最初に DROP TABLE するので、実行すると今のデータは消えて作り直しになる
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `ACOUNT`, `PASSWORD`, `POSITION`, `JOINFC`, `FANCLUB`, `BLACKLIST`, `EVENT`, `TICKET`, `PARTARTIST`, `ARTIST`, `TICKETCONDITION`, `FORMCONDITION`, `CONDITION`, `APPLY`, `VISITOR`, `SEAT`, `COMPANY`, `EMAIL`, `QR`;

-- ------------------------------------------------------------
--  本番と同じ構造（schema.sql より）
--  変えたのは次の2点だけ
--    ・APPLY が2回書かれていたので1つにした
--    ・EMAIL に KEY (Email_id) を追加（下の説明を参照）
-- ------------------------------------------------------------

-- ACOUNT / アカウントテーブル
CREATE TABLE IF NOT EXISTS `ACOUNT` (
    `Acount_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `Acount_user_name` VARCHAR(100) NOT NULL,
    `Position_id` INT UNSIGNED NOT NULL,
    `Company_id` INT UNSIGNED,
    `Acount_name` VARCHAR(100) NOT NULL,
    `Acount_birthday` DATE NOT NULL,
    `Acount_address` VARCHAR(255) NOT NULL,
    `Acount_phone_number` VARCHAR(20) NOT NULL,
    `Acount_gender` VARCHAR(10) NOT NULL,
    PRIMARY KEY (`Acount_id`),
    UNIQUE KEY `uq_acount_user_name` (`Acount_user_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- PASSWORD / パスワードテーブル
CREATE TABLE IF NOT EXISTS `PASSWORD` (
    `Acount_id` INT UNSIGNED NOT NULL,
    `Password_hash_pass` VARCHAR(255) NOT NULL,
    `Password_raw_pass` VARCHAR(255),
    PRIMARY KEY (`Acount_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- POSITION / 役職テーブル
CREATE TABLE IF NOT EXISTS `POSITION` (
    `Position_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `Position_name` VARCHAR(50) NOT NULL,
    PRIMARY KEY (`Position_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- JOINFC / 加入済みファンクラブテーブル
CREATE TABLE IF NOT EXISTS `JOINFC` (
    `Acount_id` INT UNSIGNED NOT NULL,
    `Fanclub_id` INT UNSIGNED NOT NULL,
    `Joinfc_join_day` DATE NOT NULL,
    `Joinfc_limit` DATE NOT NULL,
    `Joinfc_status` VARCHAR(30) NOT NULL,
    PRIMARY KEY (`Acount_id`, `Fanclub_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- FANCLUB / ファンクラブテーブル
CREATE TABLE IF NOT EXISTS `FANCLUB` (
    `Fanclub_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `Fanclub_name` VARCHAR(200) NOT NULL,
    PRIMARY KEY (`Fanclub_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- BLACKLIST / ブラックリストテーブル
CREATE TABLE IF NOT EXISTS `BLACKLIST` (
    `Blacklist_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `Acount_id` INT UNSIGNED NOT NULL,
    `Blacklist_status` VARCHAR(30) NOT NULL,
    `Event_id` INT UNSIGNED,
    `Ticket_id` INT UNSIGNED,
    `Company_id` INT UNSIGNED,
    PRIMARY KEY (`Blacklist_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- EVENT / イベントテーブル
CREATE TABLE IF NOT EXISTS `EVENT` (
    `Event_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `Event_name` VARCHAR(200) NOT NULL,
    `Event_text` TEXT,
    `Event_limit` DATETIME NOT NULL,
    `Event_venue` VARCHAR(255),
    PRIMARY KEY (`Event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TICKET / チケットテーブル
CREATE TABLE IF NOT EXISTS `TICKET` (
    `Ticket_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `Event_id` INT UNSIGNED NOT NULL,
    `Company_id` INT UNSIGNED NOT NULL,
    `Ticket_name` VARCHAR(200) NOT NULL,
    `Ticket_day` DATE NOT NULL,
    `Ticket_price` DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (`Ticket_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- PARTARTIST / 参加アーティストテーブル
CREATE TABLE IF NOT EXISTS `PARTARTIST` (
    `Ticket_id` INT UNSIGNED NOT NULL,
    `Artist_id` INT UNSIGNED NOT NULL,
    PRIMARY KEY (`Ticket_id`, `Artist_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ARTIST / アーティストテーブル
CREATE TABLE IF NOT EXISTS `ARTIST` (
    `Artist_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `Artist_name` VARCHAR(200) NOT NULL,
    PRIMARY KEY (`Artist_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TICKETCONDITION / チケット条件テーブル
CREATE TABLE IF NOT EXISTS `TICKETCONDITION` (
    `Ticket_id` INT UNSIGNED NOT NULL,
    `Condition_id` INT UNSIGNED NOT NULL,
    `Ticketcondition_value` VARCHAR(255),
    PRIMARY KEY (`Ticket_id`, `Condition_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- FORMCONDITION / フォーム条件テーブル
CREATE TABLE IF NOT EXISTS `FORMCONDITION` (
    `Ticket_id` INT UNSIGNED NOT NULL,
    `Condition_id` INT UNSIGNED NOT NULL,
    `Formcondition_value` VARCHAR(255),
    PRIMARY KEY (`Ticket_id`, `Condition_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- CONDITION / 条件内容テーブル
CREATE TABLE IF NOT EXISTS `CONDITION` (
    `Condition_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `Condition_group_id` INT UNSIGNED,
    `Condition_text` VARCHAR(255),
    `Condition_condition` VARCHAR(50),
    PRIMARY KEY (`Condition_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- APPLY / 申し込みテーブル
CREATE TABLE IF NOT EXISTS `APPLY` (
    `Apply_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `Acount_id` INT UNSIGNED NOT NULL,
    `Ticket_id` INT UNSIGNED NOT NULL,
    `Apply_num` INT UNSIGNED,
    `Apply_day` DATETIME,
    `Apply_result` TINYINT(1),
    PRIMARY KEY (`Apply_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- VISITOR / 入場者テーブル
CREATE TABLE IF NOT EXISTS `VISITOR` (
    `Visitor_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `Acount_id` INT UNSIGNED NOT NULL,
    `Ticket_id` INT UNSIGNED NOT NULL,
    `Apply_id` INT UNSIGNED NOT NULL,
    `Visitor_status` VARCHAR(20) NOT NULL,
    PRIMARY KEY (`Visitor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SEAT / 座席テーブル
CREATE TABLE IF NOT EXISTS `SEAT` (
    `Apply_id` INT UNSIGNED NOT NULL,
    `Seat_num` VARCHAR(30) NOT NULL,
    PRIMARY KEY (`Apply_id`, `Seat_num`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- COMPANY / 主催企業テーブル
CREATE TABLE IF NOT EXISTS `COMPANY` (
    `Company_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `Company_name` VARCHAR(200) NOT NULL,
    PRIMARY KEY (`Company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- EMAIL / メールテーブル
CREATE TABLE IF NOT EXISTS `EMAIL` (
    `Acount_id` INT UNSIGNED NOT NULL,
    `Email_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `Apply_id` INT UNSIGNED,
    `Email_status` VARCHAR(30),
    PRIMARY KEY (`Acount_id`, `Email_id`),
    -- 【テスト用の修正】自動で番号を振る列（AUTO_INCREMENT）は、索引の先頭にないと
    --  MySQL（InnoDB）でエラーになるため、Email_id 単独の索引を追加した
    KEY `idx_email_id` (`Email_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- QR / QRテーブル
CREATE TABLE IF NOT EXISTS `QR` (
    `Acount_id` INT UNSIGNED NOT NULL,
    `Ticket_id` INT UNSIGNED NOT NULL,
    `Visitor_id` INT UNSIGNED NOT NULL,
    `QR_token` VARCHAR(255) NOT NULL,
    `QR_limit` DATETIME NOT NULL,
    PRIMARY KEY (`Acount_id`, `Ticket_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
--  テスト用の追加（本番にはまだ無いもの）
--  追加するときは「誰の・どの画面のためか」をコメントに書き、
--  docs/TEST_DB.md の一覧にも1行足すこと
-- ============================================================

-- マイメール（古市）：件名・本文・受信日時
--   無くても動くが、あると受信日時の順に並び、本文をメールごとに書ける
ALTER TABLE `EMAIL`
    ADD COLUMN `Email_title` VARCHAR(200) NULL,
    ADD COLUMN `Email_text`  TEXT NULL,
    ADD COLUMN `Email_day`   DATETIME NULL;

-- ファンクラブ（栗田）：コードに合わせた列・テーブルをここに追加する予定
--   例）ALTER TABLE `FANCLUB` ADD COLUMN `Fanclub_category` VARCHAR(100) NULL;

SET FOREIGN_KEY_CHECKS = 1;
