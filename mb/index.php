<?php
//�g�є�

require_once('../lib/common.php');
//���O�C���`�F�b�N
If(chk_value($_SESSION["SHISETSU"]["ID"]) == FALSE){
	require_once('./login.php');
}

//�@�픻��ǉ�
If (chk_mobile() == 0){
	//PC�ł�
	header("Location: ../index.php");
	exit;
}

//�ȒP���O�C���o�^�V�X�e��
If (chk_value($_POST["submit"]) == TRUE && chk_value($_SESSION["SHISETSU"]["ID"]) == TRUE){
	  $obj = new MobileInformation($_SERVER["REMOTE_ADDR"],$_SERVER["HTTP_USER_AGENT"]);
  	  $mobile_key = $obj->IndividualNum();
	If (chk_value($mobile_key) == TRUE){
	//�ꉞ���łɓo�^���������`�F�b�N���Ă���
		$sql = "";
		$sql .= " select count(*) as cnt from login_user where mb_key = ?";
		//�p�����^
		$param = array($mobile_key);
		//���s
		$ret = exec_sql($sql,$param,$db_char,$keitai_char,$conn);
		while ( $row = $ret->fetchRow() ) {
			$cnt = clean(conv($row->cnt,$keitai_char),$keitai_char);
		}
		If ($cnt > 0){
			$err = "���łɓo�^������܂����B";
		}else{
		
			$sql = "";
			$sql .= " update login_user set mb_key = ? where id = ? ";
			//�p�����^
			$param = array($mobile_key,$_SESSION["SHISETSU"]["ID"]);
			//���s
			$ret = exec_sql($sql,$param,$db_char,$keitai_char,$conn);
			
			$err = "�o�^�����I";
		}
	}else{
		$err = "�o�^�Ɏ��s�B�g�т̔F�؏��擾�Ɏ��s���܂���";
	}
}

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=shift_jis">
<title></title>
</head>
<body>
<p>�{�݌����V�X�e���i�g�єŁj<br>
���ݓo�^����Ă���{�݂�茟�����܂��B<br>
�����������w�肵�Ă�������<BR>
<HR>
<a href=./ksearch.php?<?=$sid?>&mode=name>�{�ݖ��Œ��ׂ�</a><BR>
<a href=./ksearch.php?<?=$sid?>&mode=jyusyo>�Z���Œ��ׂ�</a><BR>
<a href=./ksearch.php?<?=$sid?>&mode=point>�]���|�C���g�Œ��ׂ�</a><BR>
<?php
	//�F�؂��ʂ��Ă���Γ��փ|�C���g�����\��
	If(isset($_SESSION["SHISETSU"]["ID"])){
?>
<a href=./ksearch2.php?<?=$sid?>&mode=upoint>���փ|�C���g�Œ��ׂ�</a><BR>
<HR>
<a href=./ktouroku.php?<?=$sid?>&>�V�����{�݂�o�^����</a>
<BR>
<hr>
�ȒP���O�C���ɓo�^����B
<form method="POST" action="./index.php" utn>
  <input type="submit" name="submit" value="�o�^" />
  <input type=hidden name="<?=session_name()?>" value="<?=clean(conv(session_id(),$keitai_char),$keitai_char)?>">
</form>
<?=$err?>
<?}?>
</body>
</html>
