<?php
//ˬ�䵭Ͽ��Ͽ��ǧ
require_once('./lib/common.php');
//����Ƚ���ɲ�
If (chk_mobile() <> 0){
	//�����Ǥ�(�����Ϸ���Ω������ػߡ���
	header("Location: ./mb/index.php");
	exit;
}

//����������å�
chk_login();

If ($_SESSION["SHISETSU_POSTDATA"]["status"] != "kakunin"){
	//������������
	header("Location: index.php");
	exit;
}

//���å�����ѿ�Ÿ��
	$year = $_SESSION["SHISETSU_POSTDATA"]["year"];
	$month = $_SESSION["SHISETSU_POSTDATA"]["month"];
	$day = $_SESSION["SHISETSU_POSTDATA"]["day"];
	$comment = $_SESSION["SHISETSU_POSTDATA"]["comment"];
	$sougou_point = $_SESSION["SHISETSU_POSTDATA"]["sougou_point"];
	$utiwa_point = $_SESSION["SHISETSU_POSTDATA"]["utiwa_point"];
	$title = $_SESSION["SHISETSU_POSTDATA"]["title"];
	$no = $_SESSION["SHISETSU_POSTDATA"]["no"];
//������
If (chk_value($_POST["back"]) == TRUE){
	$_SESSION["SHISETSU_POSTDATA"]["status"] = "back";
	header("Location: rec_comment.php");
	exit;
}
//��������
If(chk_value($_POST["submit"]) == TRUE){
	$touroku_id = $_SESSION["SHISETSU"]["USER_NM"];
	
	//SQL
	$sql = "";
	$sql .= " insert into houmon (no,houmon_y,houmon_m,houmon_d,comment,sougou_point,utiwa_point,touroku_id,touroku_date,touroku_time) ";
	$sql .= " values ";
	$sql .= " (?,?,?,?,?,?,?,?,?,?)";

	//�ѥ�᥿
	$param = array($no,$year,$month,$day,$comment,$sougou_point,$utiwa_point,$touroku_id,$touroku_date,$touroku_time);
	
	//�¹�
	$ret = exec_sql($sql,$param,$db_char,$web_char,$conn);

	$_SESSION["SHISETSU_POSTDATA"] = NULL;

//��å������ҥ��ɥ������
print<<<EOD
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=euc-jp">
<title></title>
</head>
<body>
<p>��Ͽ��λ</P>
<BR>
<a href=./index.php>TOP�����</a>

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
<p>����ˬ�䵭Ͽ(��ǧ��
<hr>
<b><?=$title?></b> <br>

<hr>
ˬ������<?=$year?>ǯ <?=$month?>��<?=$day?>��<br>
�����ȡ�<?=cr_to_br($comment)?><br>
���ɾ����<?=$sougou_point?><br>
����ɾ����<?=$utiwa_point?><br>
<form action=./rec_comment_kakunin.php method=post>
<input type="submit" value="���" name="back">��<input type="submit" value="����" name="submit">
</form>
<br>
<a href=./index.php>TOP�����</a>

</body>
</html>

