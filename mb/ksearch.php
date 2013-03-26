<?php
//�g�єŎ{�݌���
//�e�팟���V�X�e��

require_once ('../lib/common.php');

//�@�픻��ǉ�
If (chk_mobile() == 0){
	//PC�ł�
	header("Location: ../index.php");
	exit;
}

$mode = clean($_GET["mode"],$keitai_char);
//�O���ϐ�Get
$name = clean($_GET["name"],$keitai_char);
$todofuken = clean($_GET["todofuken"],$keitai_char);
$sikugun = clean($_GET["sikugun"],$keitai_char);
$jyusyo = clean($_GET["sikugun"],$keitai_char);
$no = clean($_GET["no"],$keitai_char);
//�������ɕϊ�
$mode = strtolower($mode);

//������
$cnt = 0;
$view_html = "";

If (ctype_digit($no) == TRUE){
	//�ԍ��w��
	
	//SQL
	$sql = "";
	$sql .= "select * from googlemap where no = ?";
	
	//�p�����^
		$param = array($no);
	//���s
		$ret = exec_sql($sql,$param,$db_char,$keitai_char,$conn);
	
	while ( $row = $ret->fetchRow() ) {
		$cnt ++;
		//�^�C�g��
		$title = clean(conv($row->text,$keitai_char),$keitai_char);

		//�Z��
		$jyusyo = clean(conv($row->todofuken,$keitai_char),$keitai_char);
		$jyusyo .= clean(conv($row->sikugun,$keitai_char),$keitai_char);
		$jyusyo .= clean(conv($row->jyusyo,$keitai_char),$keitai_char);
		//�R�����g
		$comment = clean(conv($row->comment,$keitai_char),$keitai_char);
		$comment = cr_to_br($comment);	//���s��BR�ɂ���
		//�o�^��
		$touroku_id = clean(conv($row->touroku_id,$keitai_char),$keitai_char);
		//���W�f�[�^(�ꉞ�E�E�E)
		$x = clean(conv($row->x,$keitai_char),$keitai_char);
		$y = clean(conv($row->y,$keitai_char),$keitai_char);
		//�o�^��
		$touroku_date = clean(conv($row->touroku_date,$keitai_char),$keitai_char);
		$touroku_date = substr($touroku_date,0,4) . "/" . substr($touroku_date,4,2) . "/" . substr($touroku_date,6,2);
		//HTML�g�ݗ���
		$view_html .= "<b>" . $title . "</b>\n";
		$view_html .= "<hr>\n";
		$view_html .= "�Z���F" . $jyusyo . "<br>\n";
		$view_html .= "�R�����g�F<br>";
		$view_html .= $comment . "<BR>";
		$view_html .= "(" . $touroku_id . ")<BR>(". $touroku_date . ")" ;
		$view_html .= "<BR><HR>";
		//���O�C�����Ă����ꍇ�ȉ��̃����N�ǉ�
		If (chk_value($_SESSION["SHISETSU"]["ID"]) == TRUE){
			//Get������\�z
			$params = array(
				'mode' => 'ins',
				'no' => $no
			);
		
			$view_html .= " <a href=rec_comment.php?" . $sid . "&" . http_build_query($params) . ">�K�◚���쐬</a><br>";
		}
		If (cnt_houmon($no,$conn)>0){
			//�K�◚�������݂����ꍇ�\��
			//Get������\�z
			$params = array(
				'no' => $no
			);
			
			$view_html .= " <a href=view_houmon.php?" . $sid . "&" . http_build_query($params) . ">�K�◚��������</a><br>";
		}
		If ($touroku_id == $_SESSION["SHISETSU"]["USER_NM"]){
			//�o�^�����l������Ȃ�폜�\��
			//Get������\�z
			$params = array(
				'no' => $no
			);
			
			$view_html .= " <a href=del_mark.php?" .$sid . "&" . http_build_query($params) . ">���̎{�݂��폜����</a><br>";
		}
		//���̃|�C���g���甼�a��Km�̒n�_����������
		$view_html .= " <form action=ksearch3.php method=get> ";
		$view_html .= " <p>���̒n�_��蔼�a<input type=text name=kyori " . set_ime("NUMBER") . " size=4 >Km�̎{�݂�����</P>";
		$view_html .= " <input type=hidden name=x value=" . $x . ">";
		$view_html .= " <input type=hidden name=y value=" . $y . ">";
		$view_html .= " <input type=hidden name=no value=" . $no . ">";
		$view_html .= " <input type=hidden name=" . session_name() . " value=" . clean(conv(session_id(),$keitai_char),$keitai_char) . ">";
		$view_html .= " <input type=submit name=submit value=\"����\">";
		$view_html .= " </form>";

	}
	If ($cnt == 0){
		$view_html = "<P>�Y���f�[�^�Ȃ�</P>";
	}
	
}

//���[�h�ɂ�菈����U�蕪��
If ($mode == "name"){
	//�{�ݖ��Ō���
	If (chk_value($name) == TRUE){
	
		//�����J�n
		//SQL
		$sql = "";
		$sql .= "select * from googlemap where text like ?";

		//�p�����^ 
		$param = array("%" . $name . "%");
		//���s
		$ret = exec_sql($sql,$param,$db_char,$keitai_char,$conn);

		while ( $row = $ret->fetchRow() ) {
			$cnt ++;
			//�^�C�g����NO�擾
			$title = clean(conv($row->text,$keitai_char),$keitai_char);
			$no = clean(conv($row->no,$keitai_char),$keitai_char);
			//Get������\�z
			$params = array(
				'no' => $no
			);
			$view_html .= "<a href=./ksearch.php?" . $sid . "&" . http_build_query($params) . ">" . $title . "</a><BR>\n";
		}
		If ($cnt == 0){
			$view_html = "<P>�Y���f�[�^�Ȃ�</P>\n";
		}
	}else{
		$view_html .= "<form action=ksearch.php method=get>";
		$view_html .= "<input type=hidden name=mode value=" . $mode . ">";
		$view_html .= "<input type=\"hidden\" name=\"". session_name() . "\" value=\"" . clean(conv(session_id(),$keitai_char),$keitai_char) . "\">";

		$view_html .= "<p>�{�ݖ�����́i������v�j<br>\n";
		$view_html .= "<input size=\"10\" name=\"name\" " . set_ime("IME_ON") . " ><br></p>\n";
		$view_html .= "<input type=submit name=submit value=\"����\">\n";
		$view_html .= "</form>";
	}
}
If ($mode == "jyusyo"){
	//�Z���Ō���
	If (chk_value($todofuken) == TRUE){
	
		//�����J�n
		$sql = "";
		$sql .= "select * from googlemap where todofuken like ?";
		
		//�p�����^
		$param = array("%" . $todofuken . "%"); 

		//���s
		$ret = exec_sql($sql,$param,$db_char,$keitai_char,$conn);
		
		while ( $row = $ret->fetchRow() ) {
			$cnt ++;
			//�^�C�g����NO�擾
			$title = clean(conv($row->text,$keitai_char),$keitai_char);
			$no = clean(conv($row->no,$keitai_char),$keitai_char);

			//Get������\�z
			$params = array(
				'no' => $no
			);
			
			$view_html .= "<a href=./ksearch.php?" . $sid . "&" . http_build_query($params) . ">" . $title . "</a><BR>\n";
		}
		If ($cnt == 0){
			$view_html = "<P>�Y���f�[�^�Ȃ�</P>\n";
		}
	}else{
		//���ݓo�^����Ă���s���{����\��
		$sql = "";
		$sql .= "select todofuken from googlemap group by todofuken";

		//�p�����^
		$param = array();
		//���s
		$ret = exec_sql($sql,$param,$db_char,$keitai_char,$conn);
		
		$view_html .= "<form action=ksearch.php method=get>";
		$view_html .= "<input type=hidden name=mode value=" . $mode . ">";
		$view_html .= "<input type=\"hidden\" name=\"" . session_name() . "\" value=\"" . clean(conv(session_id(),$keitai_char),$keitai_char) . "\">";

		$view_html .= "<p>�s���{���I��<br>\n";
		$view_html .="<select name=todofuken>\n";
		while ( $row = $ret->fetchRow() ) {
			$cnt ++;
			//�s���{���擾
			$todofuken = clean(conv($row->todofuken,$keitai_char),$keitai_char);
			$view_html .= "<option value=\"" . $todofuken . "\">" . $todofuken . "</option>\n";
		}
		$view_html .="</select>\n";
		$view_html .= "<input type=submit name=submit value=\"����\">\n";
		$view_html .= "</form>";


		If ($cnt == 0){
			$view_html = "<P>�Y���f�[�^�Ȃ�</P>\n";
		}
	}
}
If ($mode == "point"){
	//�|�C���g�ŒT��
	//�����|�C���g�̕��ς��������̂���o��
	//�����J�n
	$sql = "";
	$sql .= " select m . * , avg ( h . sougou_point ) as heikin ";
    $sql .= " from houmon h , googlemap m ";
    $sql .= " where ";
    $sql .= " m . no = h . no ";
    $sql .= " group by m . no ";
    $sql .= " order by avg ( h . sougou_point ) DESC ";

	//�p�����^
	$param = array();
	//���s
	$ret = exec_sql($sql,$param,$db_char,$keitai_char,$conn);
	
	while ( $row = $ret->fetchRow() ) {
		$cnt ++;
		//�^�C�g����NO�擾
		$title = clean(conv($row->text,$keitai_char),$keitai_char);
		$no = clean(conv($row->no,$keitai_char),$keitai_char);
		$heikin = clean(conv($row->heikin,$keitai_char),$keitai_char);
		//Get������\�z
			$params = array(
				'no' => $no
			);

		
		$view_html .= "<a href=./ksearch.php?" . $sid . "&" . http_build_query($params) . ">" . $title . "(" . $heikin . ")" . "</a><BR>\n";
	}
	If ($cnt == 0){
		$view_html = "<P>�Y���f�[�^�Ȃ�</P>\n";
	}
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

<?=$view_html?>
<a href=./index.php?<?=$sid?>>TOP�֖߂�</a>
</body>
</html>
