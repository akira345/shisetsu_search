<?php
//���[�U�F��(�g��)


//�擾
	$id = clean($_POST["username"],$keitai_char);
	$pw = clean($_POST["password"],$keitai_char);
	$pw = md5($pw);
//������
	$cnt = 0;
	$err = "";
//�`�F�b�N
	If (chk_value($id) == TRUE && chk_value($pw) == TRUE){
	
	//SQL
		$sql = "";
		$sql .= "select * from login_user where user_name = ? and password = ?";

	//�p�����^
		$param = array($id,$pw);
	//���s
		$ret = exec_sql($sql,$param,$db_char,$keitai_char,$conn);

		while ( $row = $ret->fetchRow() ) {
			$cnt ++;
			//�F��OK
			session_regenerate_id(TRUE);//������DoCoMo���܂߂Ă�����
			//���[�UID���Z�b�V�����Ɋi�[�B
			If (@Is_NULL($_SESSION["SHISETSU"]["ID"])){//���������Z�b�V�����L�[���̂����݂��Ȃ��ꍇ�����邽��@������
				$_SESSION["SHISETSU"]["ID"] = clean(conv($row->id,$keitai_char),$keitai_char);//ID�i���o�[
				$_SESSION["SHISETSU"]["USER_NM"] = clean(conv($row->user_name,$keitai_char),$keitai_char);//ID�i���o�[
				$_SESSION["SHISETSU"]["LOGIN_IP"] = $ip=@getenv("REMOTE_ADDR");//IP�A�h���X
			//�Z�b�V�����f�[�^�m��̂��߁A�����[�h����
				//�Z�b�V�����L�[��ύX�����̂ŉ��߂ăZ�b�g���Ȃ���
				$sid = clean(conv(SID,$keitai_char),$keitai_char);

				header("Location:./index.php?" . $sid);
				exit;
			}
		}
		If ($cnt == 0){
			$err = "<P>ID��PW���ԈႦ�Ă��܂��B</P>";
		//�Z�b�V�����S�j��
			//index.php�ȊO���J���Ă����狭���I�ɖ߂�
			If (basename($_SERVER['PHP_SELF']) <> "index.php"){
			    header("location:./index.php");
			}
		//�Z�b�V���������j��
			// �Z�b�V�����ϐ���S�ĉ�������
			$_SESSION = array();

			// �Z�b�V������ؒf����ɂ̓Z�b�V�����N�b�L�[���폜����B
			// Note: �Z�b�V������񂾂��łȂ��Z�b�V������j�󂷂�B
			if (isset($_COOKIE[session_name()])) {
			    setcookie(session_name(), '', time()-42000, '/');
			}

			// �ŏI�I�ɁA�Z�b�V������j�󂷂�
			session_destroy();

		}
	}elseIf (chk_value($_POST["login"]) == TRUE){
 		//�ȒP���O�C���V�X�e���F��
	  $obj = new MobileInformation($_SERVER["REMOTE_ADDR"],$_SERVER["HTTP_USER_AGENT"]);
  	  $mobile_key = $obj->IndividualNum();
	  
	  If (chk_value($mobile_key) == TRUE){
	  	$sql = "";
		$sql .= "select * from login_user where mb_key = ?";
		//�p�����^
		$param = array($mobile_key);
			//���s
		$ret = exec_sql($sql,$param,$db_char,$keitai_char,$conn);

		while ( $row = $ret->fetchRow() ) {
			$cnt ++;
			//�F��OK
			session_regenerate_id(TRUE);//������DoCoMo���܂߂Ă�����
			//���[�UID���Z�b�V�����Ɋi�[�B
			If (@Is_NULL($_SESSION["SHISETSU"]["ID"])){//���������Z�b�V�����L�[���̂����݂��Ȃ��ꍇ�����邽��@������
				$_SESSION["SHISETSU"]["ID"] = clean(conv($row->id,$keitai_char),$keitai_char);//ID�i���o�[
				$_SESSION["SHISETSU"]["USER_NM"] = clean(conv($row->user_name,$keitai_char),$keitai_char);//ID�i���o�[
				$_SESSION["SHISETSU"]["LOGIN_IP"] = $ip=@getenv("REMOTE_ADDR");//IP�A�h���X
			//�Z�b�V�����f�[�^�m��̂��߁A�����[�h����
				//�Z�b�V�����L�[��ύX�����̂ŉ��߂ăZ�b�g���Ȃ���
				$sid = clean(conv(SID,$keitai_char),$keitai_char);

				header("Location:./index.php?" . $sid);
				exit;
			}
		}
		If ($cnt == 0){
			$err = "<P>�Y������g�т����݂��܂���B</P>";
		//�Z�b�V�����S�j��
			//index.php�ȊO���J���Ă����狭���I�ɖ߂�
			If (basename($_SERVER['PHP_SELF']) <> "index.php"){
			    header("location:./index.php");
			}
		//�Z�b�V���������j��
			// �Z�b�V�����ϐ���S�ĉ�������
			$_SESSION = array();

			// �Z�b�V������ؒf����ɂ̓Z�b�V�����N�b�L�[���폜����B
			// Note: �Z�b�V������񂾂��łȂ��Z�b�V������j�󂷂�B
			if (isset($_COOKIE[session_name()])) {
			    setcookie(session_name(), '', time()-42000, '/');
			}

			// �ŏI�I�ɁA�Z�b�V������j�󂷂�
			session_destroy();

		}

	  }else{
	  	$err = "�Y������g�т����݂��܂���";
	  }
  }
?>
<P>���O�C��</P>
<P>�o�^���s���ꍇ�̓��O�C�����s���Ă�������</P>
<HR>
<Form method="POST" action="./index.php">
<P>ID:<INPUT size="20" type="text" name="username" maxlength = "20" <?=set_ime("IME_OFF");?>>
<br>
PW:
<INPUT size="20" type="text" name="password" maxlength = "20" <?=set_ime("IME_OFF");?>></P><BR>
<INPUT type="submit" name="login" value="���O�C��"><BR>
<BR>
<font color = "red"><?php print($err); ?></font>
</FORM>
<form method="POST" action="./index.php" utn>
  <input type="submit" name="login" value="�ȒP���O�C��" />
</form>

