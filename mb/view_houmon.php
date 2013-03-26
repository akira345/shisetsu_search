<?php
//�K���ꗗ
require_once('../lib/common.php');

//�@�픻��ǉ�
If (chk_mobile() == 0){
	//PC�ł�(������PC��������֎~�I�j
	header("Location: ../index.php");
	exit;
}

//�l�擾
If (clean($_GET["no"],$keitai_char)){
	$no = clean($_GET["no"],$keitai_char);
}else{
	$no = clean($_POST["no"],$keitai_char);
}

If (ctype_digit($no) == TRUE){
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
//�R�����g�폜����
	If (chk_value($_GET["del"]) == TRUE && chk_value($_SESSION["SHISETSU"]["ID"]) == TRUE){
		//�폜
		//��͂肢���Ȃ�폜�͕s�����C������̂Ŋm�F�y�[�W�����܂�
		$comment_no = clean($_GET["comment_no"],$keitai_char);
		
		//Get������\�z
		$params = array(
			'comment_no' => $comment_no
		);
				
		header("Location: ./del_houmon.php?" . $sid . "&" . http_build_query($params) );
		exit;
	}

	//�R�����g�ǂݍ���
	$sql = "";
	$sql = "select * from houmon where no = ? order by houmon_y DESC, houmon_m DESC, houmon_d DESC";
	
	//�p�����^
		$param = array($no);
	
	//���s
		$ret = exec_sql($sql,$param,$db_char,$keitai_char,$conn);
	
	$cnt = 0;
	//���O�C�����Ă���ꍇ�̓R�����g�폜�{�^����\��������
	while ( $row = $ret->fetchRow() ) {
		$cnt ++;
		//�K����擾
		$houmon_ymd = clean(conv($row->houmon_y,$keitai_char),$keitai_char);
		$houmon_ymd .= "/" . clean(conv($row->houmon_m,$keitai_char),$keitai_char);
		$houmon_ymd .= "/" . clean(conv($row->houmon_d,$keitai_char),$keitai_char);
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
		//�o�^��
		$touroku_date = clean(conv($row->touroku_date,$keitai_char),$keitai_char);
		$touroku_date = substr($touroku_date,0,4) . "/" . substr($touroku_date,4,2) . "/" . substr($touroku_date,6,2);
		
		//HTML�g�ݗ���
	//���O�C�����Ă���ꍇ�͍폜�{�^���ǉ��i����������o�^�҂̏ꍇ�̂݁j
	IF ($_SESSION["SHISETSU"]["USER_NM"] == $touroku_id){

		$view_html = "";
		$view_html .= "<P>�K����F" . $houmon_ymd . "</p>\n";
		$view_html .= "<P>�R�����g�F" . $comment . "</p>\n";
		$view_html .= "<P>�����|�C���g�F" . $sougou_p . "</p>\n";
		$view_html .= "<P>���փ|�C���g�F" . $utiwa_p . "</p>\n";
		$view_html .= "<P>�o�^�ҁF(" . $touroku_id . ")<BR>(" . $touroku_date . ")</p>\n";
		$view_html .= "<form action=./view_houmon.php method=get>\n";
		$view_html .= "	  <input type=submit name=del value=\"�폜\">\n";
		$view_html .= "	  <input type = hidden name=comment_no value=" . $comment_no . ">\n";
		$view_html .= "	  <input type = hidden name=no value=" . $no . ">\n";
		$view_html .= "	  <input type=\"hidden\" name=" . session_name() . " value=" . clean(conv(session_id(),$keitai_char),$keitai_char) . ">\n";
		$view_html .= "	  </form><HR>\n";
	}elseIf(chk_value($_SESSION["SHISETSU"]["ID"]) == TRUE){
$view_html = "";
$view_html.=<<<EOD
<P>�K����F{$houmon_ymd}</p>
<P>�R�����g�F{$comment}</p>
<P>�����|�C���g�F{$sougou_p}</p>
<P>���փ|�C���g�F{$utiwa_p}</p>
<P>�o�^�ҁF({$touroku_id})<BR>({$touroku_date})</p><HR>
EOD;
}else{
$view_html.=<<<EOD
<P>�K����F{$houmon_ymd}</p>
<P>�R�����g�F{$comment}</p>
<P>�����|�C���g�F{$sougou_p}</p>
<P>�o�^�ҁF({$touroku_id})<BR>({$touroku_date})</p><HR>
EOD;
}
	}
	If ($cnt == 0){
		$view_html = "<P>�f�[�^�Ȃ�</P>";
	}
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
<b><?=$title?></b> <br>
<hr>
<?=$view_html?>
<a href=./index.php?<?=$sid?>>TOP�֖߂�</a>

</body>
</html>
