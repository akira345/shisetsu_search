<?php
//携帯版施設検索(内輪Point)

require_once ('../lib/common.php');

//機種判定追加
If (chk_mobile() == 0){
	//PC版へ
	header("Location: ../index.php");
	exit;
}
//外部変数Get

$mode = clean($_GET["mode"],$keitai_char);
//小文字に変換
$mode = strtolower($mode);

//初期か
$cnt = 0;
$view_html = "";
//ログインチェック
chk_login();


If ($mode == "upoint"){
	//内輪ポイントで探す
	//内輪ポイントの平均が高いものから出す
	//検索開始
	$sql = "";
	$sql .= " select m . * , avg ( h . utiwa_point ) as heikin ";
    $sql .= " from houmon h , googlemap m ";
    $sql .= " where ";
    $sql .= " m . no = h . no ";
    $sql .= " group by m . no ";
    $sql .= " order by avg ( h . utiwa_point ) DESC ";
	
	//パラメタ
	$param = array();
	//実行
	$ret = exec_sql($sql,$param,$db_char,$keitai_char,$conn);
	
	while ( $row = $ret->fetchRow() ) {
		$cnt ++;
		//タイトルとNO取得
		$title = clean(conv($row->text,$keitai_char),$keitai_char);
		$no = clean(conv($row->no,$keitai_char),$keitai_char);
		$heikin = clean(conv($row->heikin,$keitai_char),$keitai_char);
		//Get文字列構築
		$params = array(
			'no' => $no
		);
		
		$view_html .= "<a href=./ksearch.php?" . $sid . "&" . http_build_query($params) . ">" . $title . "(" . $heikin . ")" . "</a><BR>\n";
	}
	If ($cnt == 0){
		$view_html = "<P>該当データなし</P>\n";
	}
}

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=shift_jis">
<title></title>
</head>
<body>
<P>施設検索システム</P>
<hr>
<form action=ksearch2.php method=get>
<input type=hidden name=mode value=<?=$mode?>>
<input type="hidden" name="<?=session_name()?>" value="<?=clean(conv(session_id(),$keitai_char),$keitai_char)?>">

<?=$view_html?>
</form>
<a href=./index.php?<?=$sid?>>TOPへ戻る</a>

</body>
</html>