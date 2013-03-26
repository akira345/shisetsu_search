<?php
//訪問記録登録確認
require_once('./lib/common.php');
//機種判定追加
If (chk_mobile() <> 0){
	//携帯版へ(ここは携帯立ち入り禁止！）
	header("Location: ./mb/index.php");
	exit;
}

//ログインチェック
chk_login();

If ($_SESSION["SHISETSU_POSTDATA"]["status"] != "kakunin"){
	//不正アクセス
	header("Location: index.php");
	exit;
}

//セッション変数展開
	$year = $_SESSION["SHISETSU_POSTDATA"]["year"];
	$month = $_SESSION["SHISETSU_POSTDATA"]["month"];
	$day = $_SESSION["SHISETSU_POSTDATA"]["day"];
	$comment = $_SESSION["SHISETSU_POSTDATA"]["comment"];
	$sougou_point = $_SESSION["SHISETSU_POSTDATA"]["sougou_point"];
	$utiwa_point = $_SESSION["SHISETSU_POSTDATA"]["utiwa_point"];
	$title = $_SESSION["SHISETSU_POSTDATA"]["title"];
	$no = $_SESSION["SHISETSU_POSTDATA"]["no"];
//戻る処理
If (chk_value($_POST["back"]) == TRUE){
	$_SESSION["SHISETSU_POSTDATA"]["status"] = "back";
	header("Location: rec_comment.php");
	exit;
}
//更新処理
If(chk_value($_POST["submit"]) == TRUE){
	$touroku_id = $_SESSION["SHISETSU"]["USER_NM"];
	
	//SQL
	$sql = "";
	$sql .= " insert into houmon (no,houmon_y,houmon_m,houmon_d,comment,sougou_point,utiwa_point,touroku_id,touroku_date,touroku_time) ";
	$sql .= " values ";
	$sql .= " (?,?,?,?,?,?,?,?,?,?)";

	//パラメタ
	$param = array($no,$year,$month,$day,$comment,$sougou_point,$utiwa_point,$touroku_id,$touroku_date,$touroku_time);
	
	//実行
	$ret = exec_sql($sql,$param,$db_char,$web_char,$conn);

	$_SESSION["SHISETSU_POSTDATA"] = NULL;

//メッセージヒアドキュメント
print<<<EOD
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=euc-jp">
<title></title>
</head>
<body>
<p>登録完了</P>
<BR>
<a href=./index.php>TOPへ戻る</a>

</body>
</html>
EOD;
exit;

}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=euc-jp">
<title></title>
</head>
<body>
<p>施設訪問記録(確認）
<hr>
<b><?=$title?></b> <br>

<hr>
訪問日：<?=$year?>年 <?=$month?>月<?=$day?>日<br>
コメント：<?=cr_to_br($comment)?><br>
総合評価：<?=$sougou_point?><br>
内輪評価：<?=$utiwa_point?><br>
<form action=./rec_comment_kakunin.php method=post>
<input type="submit" value="戻る" name="back">　<input type="submit" value="更新" name="submit">
</form>
<br>
<a href=./index.php>TOPへ戻る</a>

</body>
</html>

