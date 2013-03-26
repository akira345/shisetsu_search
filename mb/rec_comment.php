<?php
//訪問記録登録
require_once('../lib/common.php');
//機種判定追加

If (chk_mobile() == 0){
	//PC版へ(ここはPC立ち入り禁止！）
	header("Location: ../index.php");
	exit;
}

//ログインチェック
chk_login();


//以下認証OK後の処理

//値取得
If (clean($_GET["no"],$keitai_char)){
	$no = clean($_GET["no"],$keitai_char);
}else{
	$no = clean($_POST["no"],$keitai_char);
}
If (clean($_GET["mode"],$keitai_char)){
	$mode = clean($_GET["mode"],$keitai_char);
}else{
	$mode = clean($_POST["mode"],$keitai_char);
}

//小文字に変換
$mode = strtolower($mode);


If (ctype_digit($no) == TRUE && $mode == "ins"){
//NOより施設名検索開始
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
	If ($cnt > 0){
		//データあり(初期値設定)
		//システム日付
		$year = date('Y');
		$month = date('m');
		$day = date('j');
		$comment = "";
		$sougou_point = 3;
		$utiwa_point = 3;
		//エラーメッセージ解除
		$err_houmon = "";
		$err_comment = "";

	}
}
//更新
If (ctype_digit($no) == TRUE && $mode == "kousin"){
	//データGet
	$year = clean($_GET["houmon_y"],$keitai_char);
	$month = clean($_GET["houmon_m"],$keitai_char);
	$day = clean($_GET["houmon_d"],$keitai_char);
	$comment = clean($_GET["comment"],$keitai_char);
	$sougou_point = clean($_GET["sougou_point"],$keitai_char);
	$utiwa_point = clean($_GET["utiwa_point"],$keitai_char);
	$title = clean($_GET["title"],$keitai_char);
	
	//データチェック
	$flg = 0;
	$err_houmon = "";
	$err_comment = "";
	$cnt = 1;
	//訪問日付
	If (chk_value($year) ==FALSE || chk_value($month) == FALSE || chk_value($day) == FALSE){
		//日付が入っていない
		$flg = 1;
		$err_houmon = "<font color=red>訪問日を入れてください</font>";
	}
	//コメントチェック
	If(chk_value($comment) == FALSE){
		//コメントが入っていない
		$flg = 1;
		$err_comment = "<font color=red>コメントを入れてください</font>";
	}
	If ($flg==0){
		//OK
		//セッションへ退避
		$_SESSION["SHISETSU_POSTDATA"]["year"] = $year;
		$_SESSION["SHISETSU_POSTDATA"]["month"] = $month;
		$_SESSION["SHISETSU_POSTDATA"]["day"] = $day;
		$_SESSION["SHISETSU_POSTDATA"]["comment"] = $comment;
		$_SESSION["SHISETSU_POSTDATA"]["sougou_point"] = $sougou_point;
		$_SESSION["SHISETSU_POSTDATA"]["utiwa_point"] = $utiwa_point;
		$_SESSION["SHISETSU_POSTDATA"]["no"] = $no;
		$_SESSION["SHISETSU_POSTDATA"]["title"]=$title;
		$_SESSION["SHISETSU_POSTDATA"]["status"] = "kakunin";
		//確認ページへGo
		header("Location: rec_comment_kakunin.php?".$sid);
		exit;

	}
}
//戻ってきた処理
If ($_SESSION["SHISETSU_POSTDATA"]["status"] =="back"){
	//フォームデータ復元
	$year = $_SESSION["SHISETSU_POSTDATA"]["year"];
	$month = $_SESSION["SHISETSU_POSTDATA"]["month"];
	$day = $_SESSION["SHISETSU_POSTDATA"]["day"];
	$comment = $_SESSION["SHISETSU_POSTDATA"]["comment"];
	$sougou_point = $_SESSION["SHISETSU_POSTDATA"]["sougou_point"];
	$utiwa_point = $_SESSION["SHISETSU_POSTDATA"]["utiwa_point"];
	$no = $_SESSION["SHISETSU_POSTDATA"]["no"];
	$title = $_SESSION["SHISETSU_POSTDATA"]["title"];
	//ステータスはクリア
	$_SESSION["SHISETSU_POSTDATA"]["status"]= "";
	//チェックをすり抜ける
	$cnt = 1;
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=shift_jis">
<title></title>
</head>
<body>
<p>施設訪問記録</p><br>
<hr>
<?If ($cnt>0){?>
<b><?=$title?></b> <br>
<hr>
<form action="rec_comment.php" method="get">
訪問日：<input type=text name="houmon_y" size="5" <?=set_ime("IME_OFF")?> value="<?=$year?>">年

<input type=text name="houmon_m" size="3" <?=set_ime("IME_OFF")?> value="<?=$month?>">月
<input type=text name="houmon_d" size="3" <?=set_ime("IME_OFF")?> value="<?=$day?>">日<br>
<?=$err_houmon?><BR>
コメント：<textarea name="comment" rows="4" <?=set_ime("IME_ON")?> ><?=$comment?></textarea>
<br>
<?=$err_comment?><BR>
総合評価：<?=value_list("sougou_point",1,5,$sougou_point)?><br>
内輪評価：<?=value_list("utiwa_point",1,5,$utiwa_point)?><BR>
<input type = hidden name = "mode" value="kousin">
<input type = hidden name = "no" value=<?=$no?>>
<input type = hidden name = "title" value=<?=$title?>>
<input type="hidden" name="<?=session_name()?>" value="<?=clean(conv(session_id(),$keitai_char),$keitai_char)?>">

<input type="submit" name="submit" value="登録">
</form>
<?php
}else{
?>
<P>でーたなし</P>
<?}?>
</body>
</html>

