<?php
//訪問暦一覧
require_once('../lib/common.php');

//機種判定追加
If (chk_mobile() == 0){
	//PC版へ(ここはPC立ち入り禁止！）
	header("Location: ../index.php");
	exit;
}

//値取得
If (clean($_GET["no"],$keitai_char)){
	$no = clean($_GET["no"],$keitai_char);
}else{
	$no = clean($_POST["no"],$keitai_char);
}

If (ctype_digit($no) == TRUE){
	$sql = "";
	$sql .= " select text from googlemap where no = ?" ;
	
	//パラメタ
		$param = array($no);
	//実行
		$ret = exec_sql($sql,$param,$db_char,$keitai_char,$conn);
	
	$cnt = 0;
	while ( $row = $ret->fetchRow() ) {
		$cnt ++;
		//タイトル取得
		$title = clean(conv($row->text,$keitai_char),$keitai_char);
	}
//コメント削除処理
	If (chk_value($_GET["del"]) == TRUE && chk_value($_SESSION["SHISETSU"]["ID"]) == TRUE){
		//削除
		//やはりいきなり削除は不味い気がするので確認ページをかます
		$comment_no = clean($_GET["comment_no"],$keitai_char);
		
		//Get文字列構築
		$params = array(
			'comment_no' => $comment_no
		);
				
		header("Location: ./del_houmon.php?" . $sid . "&" . http_build_query($params) );
		exit;
	}

	//コメント読み込み
	$sql = "";
	$sql = "select * from houmon where no = ? order by houmon_y DESC, houmon_m DESC, houmon_d DESC";
	
	//パラメタ
		$param = array($no);
	
	//実行
		$ret = exec_sql($sql,$param,$db_char,$keitai_char,$conn);
	
	$cnt = 0;
	//ログインしている場合はコメント削除ボタンを表示させる
	while ( $row = $ret->fetchRow() ) {
		$cnt ++;
		//訪問日取得
		$houmon_ymd = clean(conv($row->houmon_y,$keitai_char),$keitai_char);
		$houmon_ymd .= "/" . clean(conv($row->houmon_m,$keitai_char),$keitai_char);
		$houmon_ymd .= "/" . clean(conv($row->houmon_d,$keitai_char),$keitai_char);
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
		//登録日
		$touroku_date = clean(conv($row->touroku_date,$keitai_char),$keitai_char);
		$touroku_date = substr($touroku_date,0,4) . "/" . substr($touroku_date,4,2) . "/" . substr($touroku_date,6,2);
		
		//HTML組み立て
	//ログインしている場合は削除ボタン追加（ただし同一登録者の場合のみ）
	IF ($_SESSION["SHISETSU"]["USER_NM"] == $touroku_id){

		$view_html = "";
		$view_html .= "<P>訪問日：" . $houmon_ymd . "</p>\n";
		$view_html .= "<P>コメント：" . $comment . "</p>\n";
		$view_html .= "<P>総合ポイント：" . $sougou_p . "</p>\n";
		$view_html .= "<P>内輪ポイント：" . $utiwa_p . "</p>\n";
		$view_html .= "<P>登録者：(" . $touroku_id . ")<BR>(" . $touroku_date . ")</p>\n";
		$view_html .= "<form action=./view_houmon.php method=get>\n";
		$view_html .= "	  <input type=submit name=del value=\"削除\">\n";
		$view_html .= "	  <input type = hidden name=comment_no value=" . $comment_no . ">\n";
		$view_html .= "	  <input type = hidden name=no value=" . $no . ">\n";
		$view_html .= "	  <input type=\"hidden\" name=" . session_name() . " value=" . clean(conv(session_id(),$keitai_char),$keitai_char) . ">\n";
		$view_html .= "	  </form><HR>\n";
	}elseIf(chk_value($_SESSION["SHISETSU"]["ID"]) == TRUE){
$view_html = "";
$view_html.=<<<EOD
<P>訪問日：{$houmon_ymd}</p>
<P>コメント：{$comment}</p>
<P>総合ポイント：{$sougou_p}</p>
<P>内輪ポイント：{$utiwa_p}</p>
<P>登録者：({$touroku_id})<BR>({$touroku_date})</p><HR>
EOD;
}else{
$view_html.=<<<EOD
<P>訪問日：{$houmon_ymd}</p>
<P>コメント：{$comment}</p>
<P>総合ポイント：{$sougou_p}</p>
<P>登録者：({$touroku_id})<BR>({$touroku_date})</p><HR>
EOD;
}
	}
	If ($cnt == 0){
		$view_html = "<P>データなし</P>";
	}
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
<b><?=$title?></b> <br>
<hr>
<?=$view_html?>
<a href=./index.php?<?=$sid?>>TOPへ戻る</a>

</body>
</html>
