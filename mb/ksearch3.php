<?php
//範囲指定検索

require_once('../lib/common.php');

If (chk_mobile() == 0){
	//PC版へ(ここはPC立ち入り禁止！）
	header("Location: ../index.php");
	exit;
}


//データ取得
$x = clean($_GET["x"],$keitai_char);
$y = clean($_GET["y"],$keitai_char);
$no = clean($_GET["no"],$keitai_char);
$kyori = clean($_GET["kyori"],$keitai_char);

//初期化
$cnt = 0;

If ((chk_value($x) == TRUE) && (chk_value($y) == TRUE) && (chk_value($kyori) == TRUE) && (chk_value($no) == TRUE)){
	If ((is_numeric($x) == TRUE) && (is_numeric($y) == TRUE) && (is_numeric($kyori) == TRUE) && (ctype_digit($no) == TRUE)){
		//指定距離計算
		$kyori = floor($kyori);//整数にする
		$sql = "";
		$sql .= " select * from googlemap where no <> ?";
		
		//パラメタ
		$param = array($no);
		//実行
		$ret = exec_sql($sql,$param,$db_char,$keitai_char,$conn);

		while ( $row = $ret->fetchRow() ) {
			//座標データ取得
			$xx = clean(conv($row->x,$keitai_char),$keitai_char);
			$yy = clean(conv($row->y,$keitai_char),$keitai_char);
			//距離を測定
			If (cal_length($x,$y,$xx,$yy) <= $kyori){
				$cnt ++;
				//タイトルとNO取得
				$title = clean(conv($row->text,$keitai_char),$keitai_char);
				$no = clean(conv($row->no,$keitai_char),$keitai_char);
				//Get文字列構築
				$params = array(
					'no' => $no
				);

				$view_html .= "<a href=./ksearch.php?" .$sid . "&" . http_build_query($params) . ">" . $title . "(" . cal_length($x,$y,$xx,$yy) . "Km) </a><BR>\n";
			}
		}
		If ($cnt == 0){
			$view_html = "<P>該当でーたなし</P>";
		}
	}else{
		$view_html = "<P>該当でーたなし</P>";
	}
}else{
		$view_html = "<P>該当でーたなし</P>";
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
半径<?=floor($kyori)?>Km内にある施設は以下の通りです。<BR>
<font color=red>注意！距離は目安です。結構アバウトです。</font><HR><BR>
<?=$view_html?>
</form>
</body>
</html>
