<?php
//�桼��ǧ��


//����
	$id = clean($_POST["username"],$web_char);
	$pw = clean($_POST["password"],$web_char);
	$pw = md5($pw);
//�����
	$cnt = 0;
	$err = "";
//�����å�
//����ʸ����̵�뤹��
	If (chk_value($id) == TRUE && chk_value($pw) == TRUE){
	
	//SQL
		$sql = "";
		$sql .= "select * from login_user where user_name = ? and password = ?";

	//�ѥ�᥿
		$param = array($id,$pw);

	//�¹�
		$ret = exec_sql($sql,$param,$db_char,$web_char,$conn);

		while ( $row = $ret->fetchRow() ) {
			$cnt ++;
			//ǧ��OK
			session_regenerate_id(TRUE);	//���å����ID�դ��ؤ�
			
			//�桼��ID�򥻥å����˳�Ǽ��
			If (@Is_NULL($_SESSION["SHISETSU"]["ID"])){//���⤽�⥻�å���󥭡����Τ�¸�ߤ��ʤ���礬���뤿��@��Ĥ���
				$_SESSION["SHISETSU"]["ID"] = clean(conv($row->id,$web_char),$web_char);//ID�ʥ�С�
				$_SESSION["SHISETSU"]["USER_NM"] = clean(conv($row->user_name,$web_char),$web_char);//ID�ʥ�С�
				$_SESSION["SHISETSU"]["LOGIN_IP"] = @getenv("REMOTE_ADDR");//IP���ɥ쥹
			//���å����ǡ�������Τ��ᡢ����ɤ���
				header("Location:./index.php");
				exit;
			}
		}
		If ($cnt == 0){
			$err = "<P>ID��PW���ְ㤨�Ƥ��ޤ���</P>";
		//���å�������˲�
			//index.php�ʳ��򳫤��Ƥ����鶯��Ū���᤹
			If (basename($_SERVER['PHP_SELF']) <> "index.php"){
			    header("location:./index.php");
			}
		//���å�������˴�
			// ���å�����ѿ������Ʋ������
			$_SESSION = array();

			// ���å��������Ǥ���ˤϥ��å���󥯥å����������롣
			// Note: ���å�����������Ǥʤ����å������˲����롣
			if (isset($_COOKIE[session_name()])) {
			    setcookie(session_name(), '', time()-42000, '/');
			}

			// �ǽ�Ū�ˡ����å������˲�����
			session_destroy();

		}
	}
?>

<P>������</P>
<P>��Ͽ��Ԥ����ϥ������ԤäƤ�������</P>
<HR>
<Form method="POST" action="./index.php">
<P>ID:<INPUT size="20" type="text" name="username" maxlength = "20" <?=set_ime("IME_OFF");?>>
��
PW:
<INPUT size="20" type="password" name="password" maxlength = "20"></P><BR>
<INPUT type="submit" name="login" value="������"><BR>
<BR>
<font color = "red"><?php print($err); ?></font>
</FORM>
