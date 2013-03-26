<?php
//携帯版施設検索
//各種検索システム

require_once ('../lib/common.php');

//機種判定追加
If (chk_mobile() == 0){
	//PC版へ
	header("Location: ../index.php");
	exit;
}

$mode = clean($_GET["mode"],$keitai_char);
//外部変数Get
$name = clean($_GET["name"],$keitai_char);
$todofuken = clean($_GET["todofuken"],$keitai_char);
$sikugun = clean($_GET["sikugun"],$keitai_char);
$jyusyo = clean($_GET["sikugun"],$keitai_char);
$no = clean($_GET["no"],$keitai_char);
//小文字に変換
$mode = strtolower($mode);

//初期か
$cnt = 0;
$view_html = "";

If (ctype_digit($no) == TRUE){
	//番号指定
	
	//SQL
	$sql = "";
	$sql .= "select * from googlemap where no = ?";
	
	//パラメタ
		$param = array($no);
	//実行
		$ret = exec_sql($sql,$param,$db_char,$keitai_char,$conn);
	
	while ( $row = $ret->fetchRow() ) {
		$cnt ++;
		//タイトル
		$title = clean(conv($row->text,$keitai_char),$keitai_char);

		//住所
		$jyusyo = clean(conv($row->todofuken,$keitai_char),$keitai_char);
		$jyusyo .= clean(conv($row->sikugun,$keitai_char),$keitai_char);
		$jyusyo .= clean(conv($row->jyusyo,$keitai_char),$keitai_char);
		//コメント
		$comment = clean(conv($row->comment,$keitai_char),$keitai_char);
		$comment = cr_to_br($comment);	//改行をBRにする
		//登録者
		$touroku_id = clean(conv($row->touroku_id,$keitai_char),$keitai_char);
		//座標データ(一応・・・)
		$x = clean(conv($row->x,$keitai_char),$keitai_char);
		$y = clean(conv($row->y,$keitai_char),$keitai_char);
		//登録日
		$touroku_date = clean(conv($row->touroku_date,$keitai_char),$keitai_char);
		$touroku_date = substr($touroku_date,0,4) . "/" . substr($touroku_date,4,2) . "/" . substr($touroku_date,6,2);
		//HTML組み立て
		$view_html .= "<b>" . $title . "</b>\n";
		$view_html .= "<hr>\n";
		$view_html .= "住所：" . $jyusyo . "<br>\n";
		$view_html .= "コメント：<br>";
		$view_html .= $comment . "<BR>";
		$view_html .= "(" . $touroku_id . ")<BR>(". $touroku_date . ")" ;
		$view_html .= "<BR><HR>";
		//ログインしていた場合以下のリンク追加
		If (chk_value($_SESSION["SHISETSU"]["ID"]) == TRUE){
			//Get文字列構築
			$params = array(
				'mode' => 'ins',
				'no' => $no
			);
		
			$view_html .= " <a href=rec_comment.php?" . $sid . "&" . http_build_query($params) . ">訪問履歴作成</a><br>";
		}
		If (cnt_houmon($no,$conn)>0){
			//訪問履歴が存在した場合表示
			//Get文字列構築
			$params = array(
				'no' => $no
			);
			
			$view_html .= " <a href=view_houmon.php?" . $sid . "&" . http_build_query($params) . ">訪問履歴を見る</a><br>";
		}
		If ($touroku_id == $_SESSION["SHISETSU"]["USER_NM"]){
			//登録した人が同一なら削除表示
			//Get文字列構築
			$params = array(
				'no' => $no
			);
			
			$view_html .= " <a href=del_mark.php?" .$sid . "&" . http_build_query($params) . ">この施設を削除する</a><br>";
		}
		//このポイントから半径ｎKmの地点を検索する
		$view_html .= " <form action=ksearch3.php method=get> ";
		$view_html .= " <p>この地点より半径<input type=text name=kyori " . set_ime("NUMBER") . " size=4 >Kmの施設を検索</P>";
		$view_html .= " <input type=hidden name=x value=" . $x . ">";
		$view_html .= " <input type=hidden name=y value=" . $y . ">";
		$view_html .= " <input type=hidden name=no value=" . $no . ">";
		$view_html .= " <input type=hidden name=" . session_name() . " value=" . clean(conv(session_id(),$keitai_char),$keitai_char) . ">";
		$view_html .= " <input type=submit name=submit value=\"検索\">";
		$view_html .= " </form>";

	}
	If ($cnt == 0){
		$view_html = "<P>該当データなし</P>";
	}
	
}

//モードにより処理を振り分け
If ($mode == "name"){
	//施設名で検索
	If (chk_value($name) == TRUE){
	
		//検索開始
		//SQL
		$sql = "";
		$sql .= "select * from googlemap where text like ?";

		//パラメタ 
		$param = array("%" . $name . "%");
		//実行
		$ret = exec_sql($sql,$param,$db_char,$keitai_char,$conn);

		while ( $row = $ret->fetchRow() ) {
			$cnt ++;
			//タイトルとNO取得
			$title = clean(conv($row->text,$keitai_char),$keitai_char);
			$no = clean(conv($row->no,$keitai_char),$keitai_char);
			//Get文字列構築
			$params = array(
				'no' => $no
			);
			$view_html .= "<a href=./ksearch.php?" . $sid . "&" . http_build_query($params) . ">" . $title . "</a><BR>\n";
		}
		If ($cnt == 0){
			$view_html = "<P>該当データなし</P>\n";
		}
	}else{
		$view_html .= "<form action=ksearch.php method=get>";
		$view_html .= "<input type=hidden name=mode value=" . $mode . ">";
		$view_html .= "<input type=\"hidden\" name=\"". session_name() . "\" value=\"" . clean(conv(session_id(),$keitai_char),$keitai_char) . "\">";

		$view_html .= "<p>施設名を入力（部分一致）<br>\n";
		$view_html .= "<input size=\"10\" name=\"name\" " . set_ime("IME_ON") . " ><br></p>\n";
		$view_html .= "<input type=submit name=submit value=\"検索\">\n";
		$view_html .= "</form>";
	}
}
If ($mode == "jyusyo"){
	//住所で検索
	If (chk_value($todofuken) == TRUE){
	
		//検索開始
		$sql = "";
		$sql .= "select * from googlemap where todofuken like ?";
		
		//パラメタ
		$param = array("%" . $todofuken . "%"); 

		//実行
		$ret = exec_sql($sql,$param,$db_char,$keitai_char,$conn);
		
		while ( $row = $ret->fetchRow() ) {
			$cnt ++;
			//タイトルとNO取得
			$title = clean(conv($row->text,$keitai_char),$keitai_char);
			$no = clean(conv($row->no,$keitai_char),$keitai_char);

			//Get文字列構築
			$params = array(
				'no' => $no
			);
			
			$view_html .= "<a href=./ksearch.php?" . $sid . "&" . http_build_query($params) . ">" . $title . "</a><BR>\n";
		}
		If ($cnt == 0){
			$view_html = "<P>該当データなし</P>\n";
		}
	}else{
		//現在登録されている都道府県を表示
		$sql = "";
		$sql .= "select todofuken from googlemap group by todofuken";

		//パラメタ
		$param = array();
		//実行
		$ret = exec_sql($sql,$param,$db_char,$keitai_char,$conn);
		
		$view_html .= "<form action=ksearch.php method=get>";
		$view_html .= "<input type=hidden name=mode value=" . $mode . ">";
		$view_html .= "<input type=\"hidden\" name=\"" . session_name() . "\" value=\"" . clean(conv(session_id(),$keitai_char),$keitai_char) . "\">";

		$view_html .= "<p>都道府県選択<br>\n";
		$view_html .="<select name=todofuken>\n";
		while ( $row = $ret->fetchRow() ) {
			$cnt ++;
			//都道府県取得
			$todofuken = clean(conv($row->todofuken,$keitai_char),$keitai_char);
			$view_html .= "<option value=\"" . $todofuken . "\">" . $todofuken . "</option>\n";
		}
		$view_html .="</select>\n";
		$view_html .= "<input type=submit name=submit value=\"検索\">\n";
		$view_html .= "</form>";


		If ($cnt == 0){
			$view_html = "<P>該当データなし</P>\n";
		}
	}
}
If ($mode == "point"){
	//ポイントで探す
	//総合ポイントの平均が高いものから出す
	//検索開始
	$sql = "";
	$sql .= " select m . * , avg ( h . sougou_point ) as heikin ";
    $sql .= " from houmon h , googlemap m ";
    $sql .= " where ";
    $sql .= " m . no = h . no ";
    $sql .= " group by m . no ";
    $sql .= " order by avg ( h . sougou_point ) DESC ";

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

<?=$view_html?>
<a href=./index.php?<?=$sid?>>TOPへ戻る</a>
</body>
</html>
