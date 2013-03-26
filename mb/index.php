<?php
//携帯版

require_once('../lib/common.php');
//ログインチェック
If(chk_value($_SESSION["SHISETSU"]["ID"]) == FALSE){
	require_once('./login.php');
}

//機種判定追加
If (chk_mobile() == 0){
	//PC版へ
	header("Location: ../index.php");
	exit;
}

//簡単ログイン登録システム
If (chk_value($_POST["submit"]) == TRUE && chk_value($_SESSION["SHISETSU"]["ID"]) == TRUE){
	  $obj = new MobileInformation($_SERVER["REMOTE_ADDR"],$_SERVER["HTTP_USER_AGENT"]);
  	  $mobile_key = $obj->IndividualNum();
	If (chk_value($mobile_key) == TRUE){
	//一応すでに登録が無いかチェックしておく
		$sql = "";
		$sql .= " select count(*) as cnt from login_user where mb_key = ?";
		//パラメタ
		$param = array($mobile_key);
		//実行
		$ret = exec_sql($sql,$param,$db_char,$keitai_char,$conn);
		while ( $row = $ret->fetchRow() ) {
			$cnt = clean(conv($row->cnt,$keitai_char),$keitai_char);
		}
		If ($cnt > 0){
			$err = "すでに登録がありました。";
		}else{
		
			$sql = "";
			$sql .= " update login_user set mb_key = ? where id = ? ";
			//パラメタ
			$param = array($mobile_key,$_SESSION["SHISETSU"]["ID"]);
			//実行
			$ret = exec_sql($sql,$param,$db_char,$keitai_char,$conn);
			
			$err = "登録完了！";
		}
	}else{
		$err = "登録に失敗。携帯の認証情報取得に失敗しました";
	}
}

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=shift_jis">
<title></title>
</head>
<body>
<p>施設検索システム（携帯版）<br>
現在登録されている施設より検索します。<br>
検索条件を指定してください<BR>
<HR>
<a href=./ksearch.php?<?=$sid?>&mode=name>施設名で調べる</a><BR>
<a href=./ksearch.php?<?=$sid?>&mode=jyusyo>住所で調べる</a><BR>
<a href=./ksearch.php?<?=$sid?>&mode=point>評価ポイントで調べる</a><BR>
<?php
	//認証が通っていれば内輪ポイント検索表示
	If(isset($_SESSION["SHISETSU"]["ID"])){
?>
<a href=./ksearch2.php?<?=$sid?>&mode=upoint>内輪ポイントで調べる</a><BR>
<HR>
<a href=./ktouroku.php?<?=$sid?>&>新しい施設を登録する</a>
<BR>
<hr>
簡単ログインに登録する。
<form method="POST" action="./index.php" utn>
  <input type="submit" name="submit" value="登録" />
  <input type=hidden name="<?=session_name()?>" value="<?=clean(conv(session_id(),$keitai_char),$keitai_char)?>">
</form>
<?=$err?>
<?}?>
</body>
</html>
