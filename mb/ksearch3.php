<?php
//�͈͎w�茟��

require_once('../lib/common.php');

If (chk_mobile() == 0){
	//PC�ł�(������PC��������֎~�I�j
	header("Location: ../index.php");
	exit;
}


//�f�[�^�擾
$x = clean($_GET["x"],$keitai_char);
$y = clean($_GET["y"],$keitai_char);
$no = clean($_GET["no"],$keitai_char);
$kyori = clean($_GET["kyori"],$keitai_char);

//������
$cnt = 0;

If ((chk_value($x) == TRUE) && (chk_value($y) == TRUE) && (chk_value($kyori) == TRUE) && (chk_value($no) == TRUE)){
	If ((is_numeric($x) == TRUE) && (is_numeric($y) == TRUE) && (is_numeric($kyori) == TRUE) && (ctype_digit($no) == TRUE)){
		//�w�苗���v�Z
		$kyori = floor($kyori);//�����ɂ���
		$sql = "";
		$sql .= " select * from googlemap where no <> ?";
		
		//�p�����^
		$param = array($no);
		//���s
		$ret = exec_sql($sql,$param,$db_char,$keitai_char,$conn);

		while ( $row = $ret->fetchRow() ) {
			//���W�f�[�^�擾
			$xx = clean(conv($row->x,$keitai_char),$keitai_char);
			$yy = clean(conv($row->y,$keitai_char),$keitai_char);
			//�����𑪒�
			If (cal_length($x,$y,$xx,$yy) <= $kyori){
				$cnt ++;
				//�^�C�g����NO�擾
				$title = clean(conv($row->text,$keitai_char),$keitai_char);
				$no = clean(conv($row->no,$keitai_char),$keitai_char);
				//Get������\�z
				$params = array(
					'no' => $no
				);

				$view_html .= "<a href=./ksearch.php?" .$sid . "&" . http_build_query($params) . ">" . $title . "(" . cal_length($x,$y,$xx,$yy) . "Km) </a><BR>\n";
			}
		}
		If ($cnt == 0){
			$view_html = "<P>�Y���Ł[���Ȃ�</P>";
		}
	}else{
		$view_html = "<P>�Y���Ł[���Ȃ�</P>";
	}
}else{
		$view_html = "<P>�Y���Ł[���Ȃ�</P>";
}

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=shift_jis">
<title></title>
</head>
<body>
<P>�{�݌����V�X�e��</P>
<hr>
���a<?=floor($kyori)?>Km���ɂ���{�݂͈ȉ��̒ʂ�ł��B<BR>
<font color=red>���ӁI�����͖ڈ��ł��B���\�A�o�E�g�ł��B</font><HR><BR>
<?=$view_html?>
</form>
</body>
</html>
