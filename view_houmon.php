<?php
//訪問暦一覧
require_once('./lib/common.php');
//機種判定追加
If (chk_mobile() <> 0){
	//携帯版へ(ここは携帯立ち入り禁止！）
	header("Location: ./mb/index.php");
	exit;
}

//値取得
If (clean($_GET["no"],$web_char)){
	$no = clean($_GET["no"],$web_char);
}else{
	$no = clean($_POST["no"],$web_char);
}
$comment_no = clean($_POST["comment_no"],$web_char);

If (ctype_digit($no)==TRUE){

	//SQL
	$sql = "";
	$sql .= " select text from googlemap where no = ?" ;
	
	//パラメタ
	$param = array($no);
	
	//実行
	$ret = exec_sql($sql,$param,$db_char,$web_char,$conn);
	
	$cnt = 0;
	while ( $row = $ret->fetchRow() ) {
		$cnt ++;
		//タイトル取得
		$title = clean(conv($row->text,$web_char),$web_char);
	}
	
//コメント削除処理
//コメントの表示時、削除を優先したいので、削除処理をコメントの読み込み前に行う。
	If (chk_value($_POST["del"]) == TRUE && chk_value($_SESSION["SHISETSU"]["ID"]) == TRUE && ctype_digit($comment_no) == TRUE){
		//削除
		$touroku_id = $_SESSION["SHISETSU"]["USER_NM"];
		
		//SQL
		$sql = "";
		$sql .= " delete from houmon where comment_no = ? and touroku_id = ?";

		//パラメタ
		$param = array($comment_no,$touroku_id);
		
		//実行
		$ret = exec_sql($sql,$param,$db_char,$web_char,$conn);
		
	}

	//コメント読み込み
	
	//SQL
	$sql = "";
	$sql = "select * from houmon where no = ? order by houmon_y DESC, houmon_m DESC, houmon_d DESC";
	
	//パラメタ
	$param = array($no);
	
	//実行
	$ret = exec_sql($sql,$param,$db_char,$web_char,$conn);

	$cnt = 0;
	//ログインしている場合はコメント削除ボタンを表示させる
	IF (chk_value($_SESSION["SHISETSU"]["ID"]) == TRUE){
$view_html=<<<EOD
 <table cellspacing="0" cellpadding="0" width="660" border="1">
  <tr>
    <th width="80">訪問日</th>
    <th width="200">コメント</th>
    <th width="90">総合ポイント</th>
    <th width="90">内輪ポイント</th>
    <th widhth="100">登録者</th>
    <th widhth="60">削除</th>
  </tr>
EOD;
}else{
$view_html=<<<EOD
 <table cellspacing="0" cellpadding="0" width="510" border="1">
  <tr>
    <th width="80">訪問日</th>
    <th width="200">コメント</th>
    <th width="90">総合ポイント</th>
    <th widhth="100">登録者</th>
  </tr>
EOD;
}
	while ( $row = $ret->fetchRow() ) {
		$cnt ++;
		//訪問日取得
		$houmon_ymd = clean(conv($row->houmon_y,$web_char),$web_char) . "/" ;
		$houmon_ymd .= clean(conv($row->houmon_m,$web_char),$web_char) . "/";
		$houmon_ymd .= clean(conv($row->houmon_d,$web_char),$web_char);
		//コメント
		$comment = clean(conv($row->comment,$web_char),$web_char);
		$comment = cr_to_br($comment);
		//総合ポイント
		$sougou_p = clean(conv($row->sougou_point,$web_char),$web_char);
		//内輪ポイント
		$utiwa_p = clean(conv($row->utiwa_point,$web_char),$web_char);
		//登録者
		$touroku_id = clean(conv($row->touroku_id,$web_char),$web_char);
		//コメントNO
		$comment_no = clean(conv($row->comment_no,$web_char),$web_char);
		//登録日
		$touroku_date = clean(conv($row->touroku_date,$web_char),$web_char);
		$touroku_date = substr($touroku_date,0,4) . "/" . substr($touroku_date,4,2) . "/" . substr($touroku_date,6,2);
		
		//HTML組み立て
	//ログインしている場合は削除ボタン追加（ただし同一登録者の場合のみ）
	IF ($_SESSION["SHISETSU"]["USER_NM"] == $touroku_id){
$view_html.=<<<EOD
  <tr>
    <td width="80">
      <p align="center">{$houmon_ymd}</p></td>
    <td width="200">
      <p align="left">{$comment}</p>
</td>
    <td width="90">
      <p align="center">{$sougou_p}</p></td>
    <td width="90">
      <p align="center">{$utiwa_p}</p></td>
    <td width="100">
      <p align="center">{$touroku_id}<BR>({$touroku_date})</p></td>
    <td width="60">
      <p align="center"><form action=./view_houmon.php method=post>
	  <input type=submit name=del value="削除" onclick="return confirm('本当に削除しますか？')">
	  <input type = hidden name=comment_no value={$comment_no}>
	  <input type = hidden name=no value={$no}>
	  </form></p></td>
  </tr>
EOD;
}elseIf(chk_value($_SESSION["SHISETSU"]["ID"]) == TRUE){
$view_html.=<<<EOD
  <tr>
    <td width="80">
      <p align="center">{$houmon_ymd}</p></td>
    <td width="200">
      <p align="left">{$comment}</p>
</td>
    <td width="90">
      <p align="center">{$sougou_p}</p></td>
    <td width="90">
      <p align="center">{$utiwa_p}</p></td>
    <td width="100">
      <p align="center">{$touroku_id}<BR>({$touroku_date})</p></td>
	<td width="60">
		<p align="center">　</P></td>
  </tr>
EOD;
}else{	
$view_html.=<<<EOD
  <tr>
    <td width="80">
      <p align="center">{$houmon_ymd}</p></td>
    <td width="200">
      <p align="left">{$comment}</p>
</td>
    <td width="90">
      <p align="center">{$sougou_p}</p></td>
    <td width="100">
      <p align="center">{$touroku_id}<BR>({$touroku_date})</p></td>
  </tr>		
EOD;
}
	}
	$view_html .= "</table>";
	If ($cnt == 0){
		$view_html = "<P>データなし</P>";
	}
}


?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=euc-jp">
<title></title>
</head>
<body>
<p>施設訪問記録</p>
<hr>
<b><?=$title?></b> <br>
<hr>
<?=$view_html?>
<br>
<a href=./index.php>TOPへ戻る</a>

</body>
</html>
