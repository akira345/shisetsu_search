<?php
//ˬ�������
require_once('./lib/common.php');
//����Ƚ���ɲ�
If (chk_mobile() <> 0){
	//�����Ǥ�(�����Ϸ���Ω������ػߡ���
	header("Location: ./mb/index.php");
	exit;
}

//�ͼ���
If (clean($_GET["no"],$web_char)){
	$no = clean($_GET["no"],$web_char);
}else{
	$no = clean($_POST["no"],$web_char);
}
$comment_no = clean($_POST["comment_no"],$web_char);

If (ctype_digit($no)==TRUE){

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
	
//�����Ⱥ������
//�����Ȥ�ɽ�����������ͥ�褷�����Τǡ���������򥳥��Ȥ��ɤ߹������˹Ԥ���
	If (chk_value($_POST["del"]) == TRUE && chk_value($_SESSION["SHISETSU"]["ID"]) == TRUE && ctype_digit($comment_no) == TRUE){
		//���
		$touroku_id = $_SESSION["SHISETSU"]["USER_NM"];
		
		//SQL
		$sql = "";
		$sql .= " delete from houmon where comment_no = ? and touroku_id = ?";

		//�ѥ�᥿
		$param = array($comment_no,$touroku_id);
		
		//�¹�
		$ret = exec_sql($sql,$param,$db_char,$web_char,$conn);
		
	}

	//�������ɤ߹���
	
	//SQL
	$sql = "";
	$sql = "select * from houmon where no = ? order by houmon_y DESC, houmon_m DESC, houmon_d DESC";
	
	//�ѥ�᥿
	$param = array($no);
	
	//�¹�
	$ret = exec_sql($sql,$param,$db_char,$web_char,$conn);

	$cnt = 0;
	//�����󤷤Ƥ�����ϥ����Ⱥ���ܥ����ɽ��������
	IF (chk_value($_SESSION["SHISETSU"]["ID"]) == TRUE){
$view_html=<<<EOD
 <table cellspacing="0" cellpadding="0" width="660" border="1">
  <tr>
    <th width="80">ˬ����</th>
    <th width="200">������</th>
    <th width="90">���ݥ����</th>
    <th width="90">���إݥ����</th>
    <th widhth="100">��Ͽ��</th>
    <th widhth="60">���</th>
  </tr>
EOD;
}else{
$view_html=<<<EOD
 <table cellspacing="0" cellpadding="0" width="510" border="1">
  <tr>
    <th width="80">ˬ����</th>
    <th width="200">������</th>
    <th width="90">���ݥ����</th>
    <th widhth="100">��Ͽ��</th>
  </tr>
EOD;
}
	while ( $row = $ret->fetchRow() ) {
		$cnt ++;
		//ˬ��������
		$houmon_ymd = clean(conv($row->houmon_y,$web_char),$web_char) . "/" ;
		$houmon_ymd .= clean(conv($row->houmon_m,$web_char),$web_char) . "/";
		$houmon_ymd .= clean(conv($row->houmon_d,$web_char),$web_char);
		//������
		$comment = clean(conv($row->comment,$web_char),$web_char);
		$comment = cr_to_br($comment);
		//���ݥ����
		$sougou_p = clean(conv($row->sougou_point,$web_char),$web_char);
		//���إݥ����
		$utiwa_p = clean(conv($row->utiwa_point,$web_char),$web_char);
		//��Ͽ��
		$touroku_id = clean(conv($row->touroku_id,$web_char),$web_char);
		//������NO
		$comment_no = clean(conv($row->comment_no,$web_char),$web_char);
		//��Ͽ��
		$touroku_date = clean(conv($row->touroku_date,$web_char),$web_char);
		$touroku_date = substr($touroku_date,0,4) . "/" . substr($touroku_date,4,2) . "/" . substr($touroku_date,6,2);
		
		//HTML�Ȥ�Ω��
	//�����󤷤Ƥ�����Ϻ���ܥ����ɲáʤ�����Ʊ����Ͽ�Ԥξ��Τߡ�
	IF ($_SESSION["SHISETSU"]["USER_NM"] == $touroku_id){
$view_html.=<<<EOD
  <tr>
    <td width="80">
      <p align="center">{$houmon_ymd}</p></td>
    <td width="200">
      <p align="left">{$comment}</p>
</td>
    <td width="90">
      <p align="center">{$sougou_p}</p></td>
    <td width="90">
      <p align="center">{$utiwa_p}</p></td>
    <td width="100">
      <p align="center">{$touroku_id}<BR>({$touroku_date})</p></td>
    <td width="60">
      <p align="center"><form action=./view_houmon.php method=post>
	  <input type=submit name=del value="���" onclick="return confirm('�����˺�����ޤ�����')">
	  <input type = hidden name=comment_no value={$comment_no}>
	  <input type = hidden name=no value={$no}>
	  </form></p></td>
  </tr>
EOD;
}elseIf(chk_value($_SESSION["SHISETSU"]["ID"]) == TRUE){
$view_html.=<<<EOD
  <tr>
    <td width="80">
      <p align="center">{$houmon_ymd}</p></td>
    <td width="200">
      <p align="left">{$comment}</p>
</td>
    <td width="90">
      <p align="center">{$sougou_p}</p></td>
    <td width="90">
      <p align="center">{$utiwa_p}</p></td>
    <td width="100">
      <p align="center">{$touroku_id}<BR>({$touroku_date})</p></td>
	<td width="60">
		<p align="center">��</P></td>
  </tr>
EOD;
}else{	
$view_html.=<<<EOD
  <tr>
    <td width="80">
      <p align="center">{$houmon_ymd}</p></td>
    <td width="200">
      <p align="left">{$comment}</p>
</td>
    <td width="90">
      <p align="center">{$sougou_p}</p></td>
    <td width="100">
      <p align="center">{$touroku_id}<BR>({$touroku_date})</p></td>
  </tr>		
EOD;
}
	}
	$view_html .= "</table>";
	If ($cnt == 0){
		$view_html = "<P>�ǡ����ʤ�</P>";
	}
}


?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=euc-jp">
<title></title>
</head>
<body>
<p>����ˬ�䵭Ͽ</p>
<hr>
<b><?=$title?></b> <br>
<hr>
<?=$view_html?>
<br>
<a href=./index.php>TOP�����</a>

</body>
</html>
