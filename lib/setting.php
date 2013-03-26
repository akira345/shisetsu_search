<?php
//施設検索システム　設定ファイル


//DB接続定義
$dsn = "mysql://test_user:test@localhost/test_db";//マップ用。文字コードはUTF-8前提

//mb_convert_encoding形式で指定する
//DBサーバがわ文字コード
$db_char = "UTF-8";

//PCページ文字コード
$web_char = "EUC-JP";

//携帯ページ文字コード
$keitai_char = "SJIS";

/////////変更不可///////////////////////////////////
////携帯セッションID用共通変数
$sid = clean(conv(SID,$keitai_char),$keitai_char);
////////////////////////////////////////////////////

//GoogleMapsAPIKeyセット
$gmap_key = "とってきたキー";

//GeoForm APIKeyセット
//http://lab.cirius.co.jp/よりAPIのキーを取得すること
$keitai_gps_key = 'とってきたキー';

//GPS情報取得後のもどり先URL
$keitai_ret_url = 'http://exsample.com/shisetsu/mb/ktouroku.php?' . $sid ;


?>