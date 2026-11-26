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
) ;

-- PASSWORD / パスワードテーブル
CREATE TABLE IF NOT EXISTS `PASSWORD` (
    `Acount_id` INT UNSIGNED NOT NULL,
    `Password_hash_pass` VARCHAR(255) NOT NULL,
    `Password_raw_pass` VARCHAR(255),
    PRIMARY KEY (`Acount_id`)
) ;

-- POSITION / 役職テーブル
CREATE TABLE IF NOT EXISTS `POSITION` (
    `Position_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `Position_name` VARCHAR(50) NOT NULL,
    PRIMARY KEY (`Position_id`)
) ;

-- JOINFC / 加入済みファンクラブテーブル
CREATE TABLE IF NOT EXISTS `JOINFC` (
    `Acount_id` INT UNSIGNED NOT NULL,
    `Fanclub_id` INT UNSIGNED NOT NULL,
    `Joinfc_join_day` DATE NOT NULL,
    `Joinfc_limit` DATE NOT NULL,
    `Joinfc_status` VARCHAR(30) NOT NULL,
    PRIMARY KEY (`Acount_id`, `Fanclub_id`)
) ;

-- FANCLUB / ファンクラブテーブル
CREATE TABLE IF NOT EXISTS `FANCLUB` (
    `Fanclub_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `Fanclub_name` VARCHAR(200) NOT NULL,
    PRIMARY KEY (`Fanclub_id`)
) ;

-- BLACKLIST / ブラックリストテーブル
CREATE TABLE IF NOT EXISTS `BLACKLIST` (
    `Blacklist_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `Acount_id` INT UNSIGNED NOT NULL,
    `Blacklist_status` VARCHAR(30) NOT NULL,
    `Event_id` INT UNSIGNED,
    `Ticket_id` INT UNSIGNED,
    `Company_id` INT UNSIGNED,
    PRIMARY KEY (`Blacklist_id`)
) ;

-- EVENT / イベントテーブル
CREATE TABLE IF NOT EXISTS `EVENT` (
    `Event_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `Event_name` VARCHAR(200) NOT NULL,
    `Event_text` TEXT,
    `Event_limit` DATETIME NOT NULL,
    `Event_venue` VARCHAR(255),
    PRIMARY KEY (`Event_id`)
) ;

-- TICKET / チケットテーブル
CREATE TABLE IF NOT EXISTS `TICKET` (
    `Ticket_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `Event_id` INT UNSIGNED NOT NULL,
    `Company_id` INT UNSIGNED NOT NULL,
    `Ticket_name` VARCHAR(200) NOT NULL,
    `Ticket_day` DATE NOT NULL,
    `Ticket_price` DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (`Ticket_id`)
) ;

-- PARTARTIST / 参加アーティストテーブル
CREATE TABLE IF NOT EXISTS `PARTARTIST` (
    `Ticket_id` INT UNSIGNED NOT NULL,
    `Artist_id` INT UNSIGNED NOT NULL,
    PRIMARY KEY (`Ticket_id`, `Artist_id`)
) ;

-- ARTIST / アーティストテーブル
CREATE TABLE IF NOT EXISTS `ARTIST` (
    `Artist_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `Artist_name` VARCHAR(200) NOT NULL,
    PRIMARY KEY (`Artist_id`)
) ;

-- TICKETCONDITION / チケット条件テーブル
CREATE TABLE IF NOT EXISTS `TICKETCONDITION` (
    `Ticket_id` INT UNSIGNED NOT NULL,
    `Condition_id` INT UNSIGNED NOT NULL,
    `Ticketcondition_value` VARCHAR(255),
    PRIMARY KEY (`Ticket_id`, `Condition_id`)
) ;

-- FORMCONDITION / フォーム条件テーブル
CREATE TABLE IF NOT EXISTS `FORMCONDITION` (
    `Ticket_id` INT UNSIGNED NOT NULL,
    `Condition_id` INT UNSIGNED NOT NULL,
    `Formcondition_value` VARCHAR(255),
    PRIMARY KEY (`Ticket_id`, `Condition_id`)
) ;

-- CONDITION / 条件内容テーブル
CREATE TABLE IF NOT EXISTS `CONDITION` (
    `Condition_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `Condition_group_id` INT UNSIGNED,
    `Condition_text` VARCHAR(255),
    `Condition_condition` VARCHAR(50),
    PRIMARY KEY (`Condition_id`)
) ;

-- APPLY / 申し込みテーブル
CREATE TABLE IF NOT EXISTS `APPLY` (
    `Apply_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `Acount_id` INT UNSIGNED NOT NULL,
    `Ticket_id` INT UNSIGNED NOT NULL,
    `Apply_num` INT UNSIGNED,
    `Apply_day` DATETIME,
    `Apply_result` TINYINT(1),
    PRIMARY KEY (`Apply_id`)
) ;

-- APPLY / 申し込みテーブル
CREATE TABLE IF NOT EXISTS `APPLY` (
    `Apply_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `Acount_id` INT UNSIGNED NOT NULL,
    `Ticket_id` INT UNSIGNED NOT NULL,
    `Apply_num` INT UNSIGNED,
    `Apply_day` DATETIME,
    `Apply_result` TINYINT(1),
    PRIMARY KEY (`Apply_id`)
) ;

-- VISITOR / 入場者テーブル
CREATE TABLE IF NOT EXISTS `VISITOR` (
    `Visitor_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `Acount_id` INT UNSIGNED NOT NULL,
    `Ticket_id` INT UNSIGNED NOT NULL,
    `Apply_id` INT UNSIGNED NOT NULL,
    `Visitor_status` VARCHAR(20) NOT NULL,
    PRIMARY KEY (`Visitor_id`)
) ;

-- SEAT / 座席テーブル
CREATE TABLE IF NOT EXISTS `SEAT` (
    `Apply_id` INT UNSIGNED NOT NULL,
    `Seat_num` VARCHAR(30) NOT NULL,
    PRIMARY KEY (`Apply_id`, `Seat_num`)
) ;

-- COMPANY / 主催企業テーブル
CREATE TABLE IF NOT EXISTS `COMPANY` (
    `Company_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `Company_name` VARCHAR(200) NOT NULL,
    PRIMARY KEY (`Company_id`)
) ;

-- EMAIL / メールテーブル
CREATE TABLE IF NOT EXISTS `EMAIL` (
    `Acount_id` INT UNSIGNED NOT NULL,
    `Email_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `Apply_id` INT UNSIGNED,
    `Email_status` VARCHAR(30),
    PRIMARY KEY (`Acount_id`, `Email_id`)
) ;

-- QR / QRテーブル
CREATE TABLE IF NOT EXISTS `QR` (
    `Acount_id` INT UNSIGNED NOT NULL,
    `Ticket_id` INT UNSIGNED NOT NULL,
    `Visitor_id` INT UNSIGNED NOT NULL,
    `QR_token` VARCHAR(255) NOT NULL,
    `QR_limit` DATETIME NOT NULL,
    PRIMARY KEY (`Acount_id`, `Ticket_id`)
) ;