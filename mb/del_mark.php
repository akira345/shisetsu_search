<?php
//施設マーカー削除

require_once('../lib/common.php');

If (chk_mobile() == 0){
	//PC版へ(ここはPC立ち入り禁止！）
	header("Location: ../index.php");
	exit;
}

//ログインチェック
chk_login();

//パラメタGet
$no = clean($_GET["no"],$keitai_char);

//初期化
$view_html = "";

If (ctype_digit($no) == TRUE){
	//タイトル読み込み

	//SQL
	$sql = "";
	$sql = "select * from googlemap where no = ?";

	//パラメタ
	$param = array($no);
	
	//実行
	$ret = exec_sql($sql,$param,$db_char,$keitai_char,$conn);
	
	$cnt = 0;
	while ( $row = $ret->fetchRow() ) {
		$cnt ++;
		//タイトル
		$title = clean(conv($row->text,$keitai_char),$keitai_char);
	}
	if($cnt > 0){
		//HTML組み立て
		$view_html = "";
		$view_html .= "<P>" .$title."</p>\n";
		$view_html .= "<HR>\n";
		$view_html .= "<BR>\n";
		$view_html .= "<p>このマーカーを削除しますか？\n";
		$view_html .= "<form action=./del_mark.php method=get>\n";
		$view_html .= "	  <input type=submit name=del value=\"削除\">\n";
		$view_html .= "	  <input type = hidden name=no value=" .$no .">\n";
		$view_html .= "	  <input type=\"hidden\" name=". session_name() ." value=" . clean(conv(session_id(),$keitai_char),$keitai_char) .">\n";
		$view_html .= "	  </form>\n";
	}else{
		$view_html = "<P>データなし</P>";
	}	
	If (chk_value($_GET["del"]) == TRUE ){
		$view_html = "";
		//削除処理
		//登録者IDの同一正チェック
		$touroku_id = clean($_SESSION["SHISETSU"]["USER_NM"],$keitai_char);
	
		//SQL
		$sql = "";
		$sql .= " delete from googlemap where no = ? and touroku_id = ?";
	
		//パラメタ
		$param = array($no,$touroku_id);
		
		//実行
		$ret = exec_sql($sql,$param,$db_char,$keitai_char,$conn);
	
		$sid = clean(conv(SID,$keitai_char),$keitai_char);//セッションID
		//HTML構築
		$view_html = "";
		$view_html .="<P>削除完了</P>\n";
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
<p>施設マーカー削除</p>
<hr>
<?=$view_html?>
<a href=./index.php?<?=$sid?>>TOPへ戻る</a>

</body>
</html>
