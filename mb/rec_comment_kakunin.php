<?php
//�K��L�^�o�^�m�F
require_once('../lib/common.php');
//�@�픻��ǉ�
If (chk_mobile() == 0){
	//PC�ł�(������PC��������֎~�I�j
	header("Location: ../index.php");
	exit;
}

//���O�C���`�F�b�N
chk_login();

If ($_SESSION["SHISETSU_POSTDATA"]["status"] <> "kakunin"){
	//�s���A�N�Z�X
	header("Location: index.php");
	exit;
}
//�Z�b�V�����ϐ��W�J
	$year = $_SESSION["SHISETSU_POSTDATA"]["year"];
	$month = $_SESSION["SHISETSU_POSTDATA"]["month"];
	$day = $_SESSION["SHISETSU_POSTDATA"]["day"];
	$comment = $_SESSION["SHISETSU_POSTDATA"]["comment"];
	$sougou_point = $_SESSION["SHISETSU_POSTDATA"]["sougou_point"];
	$utiwa_point = $_SESSION["SHISETSU_POSTDATA"]["utiwa_point"];
	$title = $_SESSION["SHISETSU_POSTDATA"]["title"];
	$no = $_SESSION["SHISETSU_POSTDATA"]["no"];
//�߂鏈��
If (chk_value($_GET["back"]) == TRUE){
	$_SESSION["SHISETSU_POSTDATA"]["status"] = "back";
	header("Location: rec_comment.php?".$sid);
	exit;
}
//�X�V����
If(chk_value($_GET["submit"]) == TRUE){
	$touroku_id = $_SESSION["SHISETSU"]["USER_NM"];
	$sql = "";
	$sql .= " insert into houmon (no,houmon_y,houmon_m,houmon_d,comment,sougou_point,utiwa_point,touroku_id,touroku_date,touroku_time) ";
	$sql .= " values ";
	$sql .= " (?,?,?,?,?,?,?,?,?,?)";

	//�p�����^
	$param = array($no,$year,$month,$day,$comment,$sougou_point,$utiwa_point,$touroku_id,$touroku_date,$touroku_time);

	//���s
	$ret = exec_sql($sql,$param,$db_char,$keitai_char,$conn);

	$_SESSION["SHISETSU_POSTDATA"] = NULL;

print<<<EOD
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=shift_jis">
<title></title>
</head>
<body>
<p>�o�^����</P>
<BR>
<a href=./index.php?{$sid}>TOP�֖߂�</a>
</body>
</html>
EOD;
exit;

}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=SJIS">
<title></title>
</head>
<body>
<p>�{�ݖK��L�^(�m�F�j
<hr>
<b><?=$title?></b> <br>

<hr>
�K����F<?=$year?>�N <?=$month?>��<?=$day?>��<br>
�R�����g�F<?=cr_to_br($comment)?><br>
�����]���F<?=$sougou_point?><br>
���֕]���F<?=$utiwa_point?><br>
<form action=./rec_comment_kakunin.php method=get>
<input type="submit" value="�߂�" name="back">�@<input type="submit" value="�X�V" name="submit">
<input type="hidden" name="<?=session_name()?>" value="<?=clean(conv(session_id(),$keitai_char),$keitai_char)?>">

</form>
</body>
</html>

