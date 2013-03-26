<?php
//�{�ݖK�◚���폜

require_once('../lib/common.php');

If (chk_mobile() == 0){
	//PC�ł�(������PC��������֎~�I�j
	header("Location: ../index.php");
	exit;
}

//���O�C���`�F�b�N
chk_login();

//�p�����^Get
$comment_no = clean($_GET["comment_no"],$keitai_char);

//������
$view_html = "";

If (ctype_digit($comment_no) == TRUE){
	//�R�����g�ǂݍ���

	//SQL
	$sql = "";
	$sql = "select * from houmon where comment_no = ?";
	
	//�p�����^
	$param = array($comment_no);

	//���s
	$ret = exec_sql($sql,$param,$db_char,$keitai_char,$conn);	

	$cnt = 0;
	while ( $row = $ret->fetchRow() ) {
		$cnt ++;
		//�K����擾
		$houmon_ymd = clean(conv($row->houmon_y,$keitai_char),$keitai_char) . "/";
		$houmon_ymd .= clean(conv($row->houmon_m,$keitai_char),$keitai_char) . "/";
		$houmon_ymd .= clean(conv($row->houmon_d,$keitai_char),$keitai_char);
		//�R�����g
		$comment = clean(conv($row->comment,$keitai_char),$keitai_char);
		$comment = cr_to_br($comment);
		//�����|�C���g
		$sougou_p = clean(conv($row->sougou_point,$keitai_char),$keitai_char);
		//���փ|�C���g
		$utiwa_p = clean(conv($row->utiwa_point,$keitai_char),$keitai_char);
		//�o�^��
		$touroku_id = clean(conv($row->touroku_id,$keitai_char),$keitai_char);
		//�R�����gNO
		$comment_no = clean(conv($row->comment_no,$keitai_char),$keitai_char);
	}
	if($cnt > 0){
		//HTML�g�ݗ���

		$view_html = "";
		$view_html .= "<P>�K����F" . $houmon_ymd . "</p>\n";
		$view_html .= "<P>�R�����g�F" . $comment . "</p>\n";
		$view_html .= "<P>�����|�C���g�F"  . $sougou_p . "</p>\n";
		$view_html .= "<P>���փ|�C���g�F" . $utiwa_p . "</p>\n";
		$view_html .= "<P>�o�^�ҁF(" . $touroku_id . ")</p>\n";
		$view_html .= "<BR>\n";
		$view_html .= "<p>�K��L�^���폜���܂����H\n";
		$view_html .= "<form action=./del_houmon.php method=get>\n";
		$view_html .= "	  <input type=submit name=del value=\"�폜\">\n";
		$view_html .= "	  <input type = hidden name=comment_no value=" . $comment_no . ">\n";
		$view_html .= "	  <input type=\"hidden\" name=" . session_name() . " value=" . clean(conv(session_id(),$keitai_char),$keitai_char) . ">\n";
		$view_html .= "	  </form>\n";
	}else{
		$view_html = "<P>�f�[�^�Ȃ�</P>";
	}	
	If (chk_value($_GET["del"]) == TRUE ){
		$view_html = "";
		//�폜����
		//�o�^��ID�̓��ꐳ�`�F�b�N
		$touroku_id = $_SESSION["SHISETSU"]["USER_NM"];
	
		//SQL
		$sql = "";
		$sql .= " delete from houmon where comment_no = ? and touroku_id = ?";
	
		//�p�����^
		$param = array($comment_no,$touroku_id);
		
		//���s
		$ret = exec_sql($sql,$param,$db_char,$keitai_char,$conn);

	
		//HTML�\�z
		$view_html = "";
		$view_html .= "<P>�폜����</P>\n";
		$view_html .= "<BR>\n";
	
	}
}else{
	$view_html = "<P>�f�[�^�Ȃ�</P>";
}	


?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=shift_jis">
<title></title>
</head>
<body>
<p>�{�ݖK��L�^</p>
<hr>
<?=$view_html?>
<a href=./index.php?<?=$sid?>>TOP�֖߂�</a>

</body>
</html>
