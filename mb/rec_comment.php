<?php
//�K��L�^�o�^
require_once('../lib/common.php');
//�@�픻��ǉ�

If (chk_mobile() == 0){
	//PC�ł�(������PC��������֎~�I�j
	header("Location: ../index.php");
	exit;
}

//���O�C���`�F�b�N
chk_login();


//�ȉ��F��OK��̏���

//�l�擾
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

//�������ɕϊ�
$mode = strtolower($mode);


If (ctype_digit($no) == TRUE && $mode == "ins"){
//NO���{�ݖ������J�n
	$sql = "";
	$sql .= " select text from googlemap where no = ?" ;

	//�p�����^
		$param = array($no);
	//���s
		$ret = exec_sql($sql,$param,$db_char,$keitai_char,$conn);

	$cnt = 0;
	while ( $row = $ret->fetchRow() ) {
		$cnt ++;
		//�^�C�g���擾
		$title = clean(conv($row->text,$keitai_char),$keitai_char);
	}
	If ($cnt > 0){
		//�f�[�^����(�����l�ݒ�)
		//�V�X�e�����t
		$year = date('Y');
		$month = date('m');
		$day = date('j');
		$comment = "";
		$sougou_point = 3;
		$utiwa_point = 3;
		//�G���[���b�Z�[�W����
		$err_houmon = "";
		$err_comment = "";

	}
}
//�X�V
If (ctype_digit($no) == TRUE && $mode == "kousin"){
	//�f�[�^Get
	$year = clean($_GET["houmon_y"],$keitai_char);
	$month = clean($_GET["houmon_m"],$keitai_char);
	$day = clean($_GET["houmon_d"],$keitai_char);
	$comment = clean($_GET["comment"],$keitai_char);
	$sougou_point = clean($_GET["sougou_point"],$keitai_char);
	$utiwa_point = clean($_GET["utiwa_point"],$keitai_char);
	$title = clean($_GET["title"],$keitai_char);
	
	//�f�[�^�`�F�b�N
	$flg = 0;
	$err_houmon = "";
	$err_comment = "";
	$cnt = 1;
	//�K����t
	If (chk_value($year) ==FALSE || chk_value($month) == FALSE || chk_value($day) == FALSE){
		//���t�������Ă��Ȃ�
		$flg = 1;
		$err_houmon = "<font color=red>�K��������Ă�������</font>";
	}
	//�R�����g�`�F�b�N
	If(chk_value($comment) == FALSE){
		//�R�����g�������Ă��Ȃ�
		$flg = 1;
		$err_comment = "<font color=red>�R�����g�����Ă�������</font>";
	}
	If ($flg==0){
		//OK
		//�Z�b�V�����֑ޔ�
		$_SESSION["SHISETSU_POSTDATA"]["year"] = $year;
		$_SESSION["SHISETSU_POSTDATA"]["month"] = $month;
		$_SESSION["SHISETSU_POSTDATA"]["day"] = $day;
		$_SESSION["SHISETSU_POSTDATA"]["comment"] = $comment;
		$_SESSION["SHISETSU_POSTDATA"]["sougou_point"] = $sougou_point;
		$_SESSION["SHISETSU_POSTDATA"]["utiwa_point"] = $utiwa_point;
		$_SESSION["SHISETSU_POSTDATA"]["no"] = $no;
		$_SESSION["SHISETSU_POSTDATA"]["title"]=$title;
		$_SESSION["SHISETSU_POSTDATA"]["status"] = "kakunin";
		//�m�F�y�[�W��Go
		header("Location: rec_comment_kakunin.php?".$sid);
		exit;

	}
}
//�߂��Ă�������
If ($_SESSION["SHISETSU_POSTDATA"]["status"] =="back"){
	//�t�H�[���f�[�^����
	$year = $_SESSION["SHISETSU_POSTDATA"]["year"];
	$month = $_SESSION["SHISETSU_POSTDATA"]["month"];
	$day = $_SESSION["SHISETSU_POSTDATA"]["day"];
	$comment = $_SESSION["SHISETSU_POSTDATA"]["comment"];
	$sougou_point = $_SESSION["SHISETSU_POSTDATA"]["sougou_point"];
	$utiwa_point = $_SESSION["SHISETSU_POSTDATA"]["utiwa_point"];
	$no = $_SESSION["SHISETSU_POSTDATA"]["no"];
	$title = $_SESSION["SHISETSU_POSTDATA"]["title"];
	//�X�e�[�^�X�̓N���A
	$_SESSION["SHISETSU_POSTDATA"]["status"]= "";
	//�`�F�b�N�����蔲����
	$cnt = 1;
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=shift_jis">
<title></title>
</head>
<body>
<p>�{�ݖK��L�^</p><br>
<hr>
<?If ($cnt>0){?>
<b><?=$title?></b> <br>
<hr>
<form action="rec_comment.php" method="get">
�K����F<input type=text name="houmon_y" size="5" <?=set_ime("IME_OFF")?> value="<?=$year?>">�N

<input type=text name="houmon_m" size="3" <?=set_ime("IME_OFF")?> value="<?=$month?>">��
<input type=text name="houmon_d" size="3" <?=set_ime("IME_OFF")?> value="<?=$day?>">��<br>
<?=$err_houmon?><BR>
�R�����g�F<textarea name="comment" rows="4" <?=set_ime("IME_ON")?> ><?=$comment?></textarea>
<br>
<?=$err_comment?><BR>
�����]���F<?=value_list("sougou_point",1,5,$sougou_point)?><br>
���֕]���F<?=value_list("utiwa_point",1,5,$utiwa_point)?><BR>
<input type = hidden name = "mode" value="kousin">
<input type = hidden name = "no" value=<?=$no?>>
<input type = hidden name = "title" value=<?=$title?>>
<input type="hidden" name="<?=session_name()?>" value="<?=clean(conv(session_id(),$keitai_char),$keitai_char)?>">

<input type="submit" name="submit" value="�o�^">
</form>
<?php
}else{
?>
<P>�Ł[���Ȃ�</P>
<?}?>
</body>
</html>

