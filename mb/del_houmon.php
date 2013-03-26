<?php
//施設訪問履歴削除

require_once('../lib/common.php');

If (chk_mobile() == 0){
	//PC版へ(ここはPC立ち入り禁止！）
	header("Location: ../index.php");
	exit;
}

//ログインチェック
chk_login();

//パラメタGet
$comment_no = clean($_GET["comment_no"],$keitai_char);

//初期化
$view_html = "";

If (ctype_digit($comment_no) == TRUE){
	//コメント読み込み

	//SQL
	$sql = "";
	$sql = "select * from houmon where comment_no = ?";
	
	//パラメタ
	$param = array($comment_no);

	//実行
	$ret = exec_sql($sql,$param,$db_char,$keitai_char,$conn);	

	$cnt = 0;
	while ( $row = $ret->fetchRow() ) {
		$cnt ++;
		//訪問日取得
		$houmon_ymd = clean(conv($row->houmon_y,$keitai_char),$keitai_char) . "/";
		$houmon_ymd .= clean(conv($row->houmon_m,$keitai_char),$keitai_char) . "/";
		$houmon_ymd .= clean(conv($row->houmon_d,$keitai_char),$keitai_char);
		//コメント
		$comment = clean(conv($row->comment,$keitai_char),$keitai_char);
		$comment = cr_to_br($comment);
		//総合ポイント
		$sougou_p = clean(conv($row->sougou_point,$keitai_char),$keitai_char);
		//内輪ポイント
		$utiwa_p = clean(conv($row->utiwa_point,$keitai_char),$keitai_char);
		//登録者
		$touroku_id = clean(conv($row->touroku_id,$keitai_char),$keitai_char);
		//コメントNO
		$comment_no = clean(conv($row->comment_no,$keitai_char),$keitai_char);
	}
	if($cnt > 0){
		//HTML組み立て

		$view_html = "";
		$view_html .= "<P>訪問日：" . $houmon_ymd . "</p>\n";
		$view_html .= "<P>コメント：" . $comment . "</p>\n";
		$view_html .= "<P>総合ポイント："  . $sougou_p . "</p>\n";
		$view_html .= "<P>内輪ポイント：" . $utiwa_p . "</p>\n";
		$view_html .= "<P>登録者：(" . $touroku_id . ")</p>\n";
		$view_html .= "<BR>\n";
		$view_html .= "<p>訪問記録を削除しますか？\n";
		$view_html .= "<form action=./del_houmon.php method=get>\n";
		$view_html .= "	  <input type=submit name=del value=\"削除\">\n";
		$view_html .= "	  <input type = hidden name=comment_no value=" . $comment_no . ">\n";
		$view_html .= "	  <input type=\"hidden\" name=" . session_name() . " value=" . clean(conv(session_id(),$keitai_char),$keitai_char) . ">\n";
		$view_html .= "	  </form>\n";
	}else{
		$view_html = "<P>データなし</P>";
	}	
	If (chk_value($_GET["del"]) == TRUE ){
		$view_html = "";
		//削除処理
		//登録者IDの同一正チェック
		$touroku_id = $_SESSION["SHISETSU"]["USER_NM"];
	
		//SQL
		$sql = "";
		$sql .= " delete from houmon where comment_no = ? and touroku_id = ?";
	
		//パラメタ
		$param = array($comment_no,$touroku_id);
		
		//実行
		$ret = exec_sql($sql,$param,$db_char,$keitai_char,$conn);

	
		//HTML構築
		$view_html = "";
		$view_html .= "<P>削除完了</P>\n";
		$view_html .= "<BR>\n";
	
	}
}else{
	$view_html = "<P>データなし</P>";
}	


?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=shift_jis">
<title></title>
</head>
<body>
<p>施設訪問記録</p>
<hr>
<?=$view_html?>
<a href=./index.php?<?=$sid?>>TOPへ戻る</a>

</body>
</html>
