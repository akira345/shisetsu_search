<?php
//�{�݃}�[�J�[�폜

require_once('../lib/common.php');

If (chk_mobile() == 0){
	//PC�ł�(������PC��������֎~�I�j
	header("Location: ../index.php");
	exit;
}

//���O�C���`�F�b�N
chk_login();

//�p�����^Get
$no = clean($_GET["no"],$keitai_char);

//������
$view_html = "";

If (ctype_digit($no) == TRUE){
	//�^�C�g���ǂݍ���

	//SQL
	$sql = "";
	$sql = "select * from googlemap where no = ?";

	//�p�����^
	$param = array($no);
	
	//���s
	$ret = exec_sql($sql,$param,$db_char,$keitai_char,$conn);
	
	$cnt = 0;
	while ( $row = $ret->fetchRow() ) {
		$cnt ++;
		//�^�C�g��
		$title = clean(conv($row->text,$keitai_char),$keitai_char);
	}
	if($cnt > 0){
		//HTML�g�ݗ���
		$view_html = "";
		$view_html .= "<P>" .$title."</p>\n";
		$view_html .= "<HR>\n";
		$view_html .= "<BR>\n";
		$view_html .= "<p>���̃}�[�J�[���폜���܂����H\n";
		$view_html .= "<form action=./del_mark.php method=get>\n";
		$view_html .= "	  <input type=submit name=del value=\"�폜\">\n";
		$view_html .= "	  <input type = hidden name=no value=" .$no .">\n";
		$view_html .= "	  <input type=\"hidden\" name=". session_name() ." value=" . clean(conv(session_id(),$keitai_char),$keitai_char) .">\n";
		$view_html .= "	  </form>\n";
	}else{
		$view_html = "<P>�f�[�^�Ȃ�</P>";
	}	
	If (chk_value($_GET["del"]) == TRUE ){
		$view_html = "";
		//�폜����
		//�o�^��ID�̓��ꐳ�`�F�b�N
		$touroku_id = clean($_SESSION["SHISETSU"]["USER_NM"],$keitai_char);
	
		//SQL
		$sql = "";
		$sql .= " delete from googlemap where no = ? and touroku_id = ?";
	
		//�p�����^
		$param = array($no,$touroku_id);
		
		//���s
		$ret = exec_sql($sql,$param,$db_char,$keitai_char,$conn);
	
		$sid = clean(conv(SID,$keitai_char),$keitai_char);//�Z�b�V����ID
		//HTML�\�z
		$view_html = "";
		$view_html .="<P>�폜����</P>\n";
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
<p>�{�݃}�[�J�[�폜</p>
<hr>
<?=$view_html?>
<a href=./index.php?<?=$sid?>>TOP�֖߂�</a>

</body>
</html>
