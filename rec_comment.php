<?php
//ˬ�䵭Ͽ��Ͽ
require_once('./lib/common.php');
//����Ƚ���ɲ�
If (chk_mobile() <> 0){
	//�����Ǥ�(�����Ϸ���Ω������ػߡ���
	header("Location: ./mb/index.php");
	exit;
}

//����������å�
chk_login();


//�ʲ�ǧ��OK��ν���

//�ͼ���
If (clean($_GET["no"],$web_char)){
	$no = clean($_GET["no"],$web_char);
}else{
	$no = clean($_POST["no"],$web_char);
}
If (clean($_GET["mode"],$web_char)){
	$mode = clean($_GET["mode"],$web_char);
}else{
	$mode = clean($_POST["mode"],$web_char);
}
	//��ʸ�����Ѵ�
	$mode = strtolower($mode);

If (ctype_digit($no) && $mode == "ins"){
//NO������̾��������

	//SQL
	$sql = "";
	$sql .= " select text from googlemap where no = ?" ;
	
	//�ѥ�᥿
	$param = array($no);
	
	//�¹�
	$ret = exec_sql($sql,$param,$db_char,$web_char,$conn);
	
	$cnt = 0;
	while ( $row = $ret->fetchRow() ) {
		$cnt ++;
		//�����ȥ����
		$title = clean(conv($row->text,$web_char),$web_char);
	}
	If ($cnt > 0){
		//�ǡ�������
		//�����ƥ�����
		$year = date('Y');
		$month = date('m');
		$day = date('j');
		$comment = "";
		//����ͥ��å�
		$sougou_point = 3;
		$utiwa_point = 3;
		
		//���顼��å��������
		$err_houmon = "";
		$err_comment = "";

	}
}
//����
If (ctype_digit($no) && $mode == "kousin"){
	//�ǡ���Get
	$year = clean($_POST["houmon_y"],$web_char);
	$month = clean($_POST["houmon_m"],$web_char);
	$day = clean($_POST["houmon_d"],$web_char);
	$comment = clean($_POST["comment"],$web_char);
	$sougou_point = clean($_POST["sougou_point"],$web_char);
	$utiwa_point = clean($_POST["utiwa_point"],$web_char);
	$title = clean($_POST["title"],$web_char);
	
	//�ǡ��������å�
	$flg = 0;
	$err_houmon = "";
	$err_comment = "";
	$cnt = 1;	//���顼�����å��򤹤�ȴ����
	//ˬ������
	If (chk_value($year) == FALSE || chk_value($month) == FALSE || chk_value($day) == FALSE){
		//���դ����äƤ��ʤ�
		$flg = 1;
		$err_houmon = "<font color=red>ˬ����������Ƥ�������</font>";
	}
	//�����ȥ����å�
	If(chk_value($comment) == FALSE){
		//�����Ȥ����äƤ��ʤ�
		$flg = 1;
		$err_comment = "<font color=red>�����Ȥ�����Ƥ�������</font>";
	}
	If ($flg==0){
		//OK
		//���å���������
		$_SESSION["SHISETSU_POSTDATA"]["year"] = $year;
		$_SESSION["SHISETSU_POSTDATA"]["month"] = $month;
		$_SESSION["SHISETSU_POSTDATA"]["day"] = $day;
		$_SESSION["SHISETSU_POSTDATA"]["comment"] = $comment;
		$_SESSION["SHISETSU_POSTDATA"]["sougou_point"] = $sougou_point;
		$_SESSION["SHISETSU_POSTDATA"]["utiwa_point"] = $utiwa_point;
		$_SESSION["SHISETSU_POSTDATA"]["no"] = $no;
		$_SESSION["SHISETSU_POSTDATA"]["title"]=$title;
		$_SESSION["SHISETSU_POSTDATA"]["status"] = "kakunin";
		//��ǧ�ڡ�����Go
		header("Location: rec_comment_kakunin.php");
		exit;

	}
}
//��äƤ�������
If ($_SESSION["SHISETSU_POSTDATA"]["status"] =="back"){
	//�ե�����ǡ�������
	$year = $_SESSION["SHISETSU_POSTDATA"]["year"];
	$month = $_SESSION["SHISETSU_POSTDATA"]["month"];
	$day = $_SESSION["SHISETSU_POSTDATA"]["day"];
	$comment = $_SESSION["SHISETSU_POSTDATA"]["comment"];
	$sougou_point = $_SESSION["SHISETSU_POSTDATA"]["sougou_point"];
	$utiwa_point = $_SESSION["SHISETSU_POSTDATA"]["utiwa_point"];
	$no = $_SESSION["SHISETSU_POSTDATA"]["no"];
	$title = $_SESSION["SHISETSU_POSTDATA"]["title"];
	//���ơ������ϥ��ꥢ
	$_SESSION["SHISETSU_POSTDATA"]["status"]= "";
	//�����å��򤹤�ȴ����
	$cnt = 1;
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=euc-jp">
<title></title>
</head>
<body>
<p>����ˬ�䵭Ͽ</p><br>
<hr>
<?If ($cnt>0){?>
<b><?=$title?></b> <br>
<hr>
<form action=./rec_comment.php method=post>
ˬ������<input type=text name="houmon_y" size="5" <?=set_ime("IME_OFF")?> value="<?=$year?>">ǯ

<input type=text name="houmon_m" size="3" <?=set_ime("IME_OFF")?> value="<?=$month?>">��
<input type=text name="houmon_d" size="3" <?=set_ime("IME_OFF")?> value="<?=$day?>">��<br>
<?=$err_houmon?><BR>
�����ȡ�<textarea name="comment" rows="4" <?=set_ime("IME_ON")?> ><?=$comment?></textarea>
<br>
<?=$err_comment?><BR>
���ɾ����<?=value_list("sougou_point",1,5,$sougou_point)?><br>
����ɾ����<?=value_list("utiwa_point",1,5,$utiwa_point)?><BR>
<input type = hidden name = "mode" value="kousin">
<input type = hidden name = "no" value=<?=$no?>>
<input type = hidden name = "title" value=<?=$title?>>
<input type="submit" name="submit" value="����">
</form>
<?php
}else{
?>
<P>�ǡ����ʤ�</P>
<?}?>
<br>
<a href=./index.php>TOP�����</a>

</body>
</himl>

