<?php
//�g�єŎ{�݌���(����Point)

require_once ('../lib/common.php');

//�@�픻��ǉ�
If (chk_mobile() == 0){
	//PC�ł�
	header("Location: ../index.php");
	exit;
}
//�O���ϐ�Get

$mode = clean($_GET["mode"],$keitai_char);
//�������ɕϊ�
$mode = strtolower($mode);

//������
$cnt = 0;
$view_html = "";
//���O�C���`�F�b�N
chk_login();


If ($mode == "upoint"){
	//���փ|�C���g�ŒT��
	//���փ|�C���g�̕��ς��������̂���o��
	//�����J�n
	$sql = "";
	$sql .= " select m . * , avg ( h . utiwa_point ) as heikin ";
    $sql .= " from houmon h , googlemap m ";
    $sql .= " where ";
    $sql .= " m . no = h . no ";
    $sql .= " group by m . no ";
    $sql .= " order by avg ( h . utiwa_point ) DESC ";
	
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
<form action=ksearch2.php method=get>
<input type=hidden name=mode value=<?=$mode?>>
<input type="hidden" name="<?=session_name()?>" value="<?=clean(conv(session_id(),$keitai_char),$keitai_char)?>">

<?=$view_html?>
</form>
<a href=./index.php?<?=$sid?>>TOP�֖߂�</a>

</body>
</html>