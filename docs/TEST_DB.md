# テスト用DBの使い方

本番の DB ができるまで、各自の PC の MySQL（XAMPP）に **テスト用DB** を作って開発します。
最後に、`.env` の接続先を本番の DB に変えて入れ替えます。

## 方針
- テスト用DBは、本番の構造（`database/sql/schema.sql`）を土台にする
- 各自のコードに必要な列・テーブルは「テスト用の追加」として `database/sql/test_schema.sql` の下の欄に足してよい
- **足したものは、本番に入れ替える前に DB 担当に本番へ追加してもらう**（下の「4. 本番に入れ替える前に依頼するもの」に必ず1行書く）

## 1. ファイル
| ファイル | 内容 |
|---|---|
| `database/sql/schema.sql` | 本番の構造（DB 担当のもの。変更しない） |
| `database/sql/test_schema.sql` | テスト用DBの構造（本番＋テスト用の追加） |
| `database/sql/test_data.sql` | テスト用のデータ（ダミーデータと同じ内容） |

## 2. テスト用DBの作り方（A5:SQL Mk-2）
1. XAMPP で MySQL を起動する
2. A5:SQL Mk-2 で、自分の PC の MySQL（ホスト `127.0.0.1`、ポート `3306`、ユーザー `root`）に接続する
3. SQL 画面で次を実行し、データベースを作る（初回だけ）
   ```sql
   CREATE DATABASE ticket_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
4. 左のツリーで `ticket_app` をダブルクリックして作業対象にする（`mysql` などのままにしない）
5. `test_schema.sql` を開いて実行する（テーブルを作り直す）
6. `test_data.sql` を開いて実行する（テスト用データを入れる）

`test_schema.sql` は最初にテーブルを消して作り直すので、**データを最初の状態に戻したいとき**も 5 → 6 を実行すればよいです。

## 3. アプリをテスト用DBにつなぐ
`.env` を次のようにして、`php artisan config:clear` を実行します。
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ticket_app
DB_USERNAME=root
DB_PASSWORD=

USE_DUMMY_DATA=false
```
`USE_DUMMY_DATA=true` に戻すと、DB を使わないダミーデータに戻ります。

テスト用アカウント（パスワードはすべて `pass1234`）

| ユーザーID | 役職 | 名前 |
|---|---|---|
| user01 | 一般 | テスト 太郎（申し込み・メール・ファンクラブのデータあり） |
| user02 | 一般 | サンプル 花子（他人のデータが見えないことの確認用） |
| company01 | 企業 | 企業 担当者 |
| admin01 | 管理者 | 管理者 |

## 4. 本番に入れ替える前に依頼するもの
テスト用に足したもの・直したものの一覧です。**本番に入れ替える前に、DB 担当にこの一覧を渡して本番にも反映してもらいます。**
テスト用DBに何かを足した人は、ここに1行追加してください。

| 誰の・どの画面のため | テーブル | 内容 | 必須？ |
|---|---|---|---|
| 全体（不具合の修正） | APPLY | `schema.sql` で同じ CREATE 文が2回書かれている → 1つにする | 整理のみ |
| 全体（不具合の修正） | EMAIL | `Email_id` が AUTO_INCREMENT なのに主キーの2番目にあり、InnoDB ではテーブルが作れない → `KEY (Email_id)` を追加する | 必須 |
| 全体 | すべて | 文字コードを `utf8mb4` にする（日本語の文字化け防止） | 必須 |
| マイメール（古市） | EMAIL | 列を追加：`Email_title` VARCHAR(200)、`Email_text` TEXT、`Email_day` DATETIME | 推奨（無くても動くが、受信日時順にならない） |
| ファンクラブ（栗田） | FANCLUB など | （コードに合わせて追加予定） | |

## 5. 本番への入れ替え
1. 上の一覧を DB 担当に渡し、本番に反映してもらう
2. `.env` の `DB_HOST`・`DB_DATABASE`・`DB_USERNAME`・`DB_PASSWORD` を本番のものに変える
3. `USE_DUMMY_DATA=false` のまま `php artisan config:clear` を実行する
4. 各画面を開いて動作を確認する
