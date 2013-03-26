<?php
//ユーザ認証


//取得
	$id = clean($_POST["username"],$web_char);
	$pw = clean($_POST["password"],$web_char);
	$pw = md5($pw);
//初期化
	$cnt = 0;
	$err = "";
//チェック
//空白文字は無視する
	If (chk_value($id) == TRUE && chk_value($pw) == TRUE){
	
	//SQL
		$sql = "";
		$sql .= "select * from login_user where user_name = ? and password = ?";

	//パラメタ
		$param = array($id,$pw);

	//実行
		$ret = exec_sql($sql,$param,$db_char,$web_char,$conn);

		while ( $row = $ret->fetchRow() ) {
			$cnt ++;
			//認証OK
			session_regenerate_id(TRUE);	//セッションID付け替え
			
			//ユーザIDをセッションに格納。
			If (@Is_NULL($_SESSION["SHISETSU"]["ID"])){//そもそもセッションキー自体が存在しない場合があるため@をつける
				$_SESSION["SHISETSU"]["ID"] = clean(conv($row->id,$web_char),$web_char);//IDナンバー
				$_SESSION["SHISETSU"]["USER_NM"] = clean(conv($row->user_name,$web_char),$web_char);//IDナンバー
				$_SESSION["SHISETSU"]["LOGIN_IP"] = @getenv("REMOTE_ADDR");//IPアドレス
			//セッションデータ確定のため、リロードする
				header("Location:./index.php");
				exit;
			}
		}
		If ($cnt == 0){
			$err = "<P>IDかPWが間違えています。</P>";
		//セッション全破壊
			//index.php以外を開いていたら強制的に戻す
			If (basename($_SERVER['PHP_SELF']) <> "index.php"){
			    header("location:./index.php");
			}
		//セッション強制破棄
			// セッション変数を全て解除する
			$_SESSION = array();

			// セッションを切断するにはセッションクッキーも削除する。
			// Note: セッション情報だけでなくセッションを破壊する。
			if (isset($_COOKIE[session_name()])) {
			    setcookie(session_name(), '', time()-42000, '/');
			}

			// 最終的に、セッションを破壊する
			session_destroy();

		}
	}
?>

<P>ログイン</P>
<P>登録を行う場合はログインを行ってください</P>
<HR>
<Form method="POST" action="./index.php">
<P>ID:<INPUT size="20" type="text" name="username" maxlength = "20" <?=set_ime("IME_OFF");?>>
　
PW:
<INPUT size="20" type="password" name="password" maxlength = "20"></P><BR>
<INPUT type="submit" name="login" value="ログイン"><BR>
<BR>
<font color = "red"><?php print($err); ?></font>
</FORM>
