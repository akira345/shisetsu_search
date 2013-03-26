<?php
//ユーザ認証(携帯)


//取得
	$id = clean($_POST["username"],$keitai_char);
	$pw = clean($_POST["password"],$keitai_char);
	$pw = md5($pw);
//初期化
	$cnt = 0;
	$err = "";
//チェック
	If (chk_value($id) == TRUE && chk_value($pw) == TRUE){
	
	//SQL
		$sql = "";
		$sql .= "select * from login_user where user_name = ? and password = ?";

	//パラメタ
		$param = array($id,$pw);
	//実行
		$ret = exec_sql($sql,$param,$db_char,$keitai_char,$conn);

		while ( $row = $ret->fetchRow() ) {
			$cnt ++;
			//認証OK
			session_regenerate_id(TRUE);//ここはDoCoMoも含めてかえる
			//ユーザIDをセッションに格納。
			If (@Is_NULL($_SESSION["SHISETSU"]["ID"])){//そもそもセッションキー自体が存在しない場合があるため@をつける
				$_SESSION["SHISETSU"]["ID"] = clean(conv($row->id,$keitai_char),$keitai_char);//IDナンバー
				$_SESSION["SHISETSU"]["USER_NM"] = clean(conv($row->user_name,$keitai_char),$keitai_char);//IDナンバー
				$_SESSION["SHISETSU"]["LOGIN_IP"] = $ip=@getenv("REMOTE_ADDR");//IPアドレス
			//セッションデータ確定のため、リロードする
				//セッションキーを変更したので改めてセットしなおす
				$sid = clean(conv(SID,$keitai_char),$keitai_char);

				header("Location:./index.php?" . $sid);
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
	}elseIf (chk_value($_POST["login"]) == TRUE){
 		//簡単ログインシステム認証
	  $obj = new MobileInformation($_SERVER["REMOTE_ADDR"],$_SERVER["HTTP_USER_AGENT"]);
  	  $mobile_key = $obj->IndividualNum();
	  
	  If (chk_value($mobile_key) == TRUE){
	  	$sql = "";
		$sql .= "select * from login_user where mb_key = ?";
		//パラメタ
		$param = array($mobile_key);
			//実行
		$ret = exec_sql($sql,$param,$db_char,$keitai_char,$conn);

		while ( $row = $ret->fetchRow() ) {
			$cnt ++;
			//認証OK
			session_regenerate_id(TRUE);//ここはDoCoMoも含めてかえる
			//ユーザIDをセッションに格納。
			If (@Is_NULL($_SESSION["SHISETSU"]["ID"])){//そもそもセッションキー自体が存在しない場合があるため@をつける
				$_SESSION["SHISETSU"]["ID"] = clean(conv($row->id,$keitai_char),$keitai_char);//IDナンバー
				$_SESSION["SHISETSU"]["USER_NM"] = clean(conv($row->user_name,$keitai_char),$keitai_char);//IDナンバー
				$_SESSION["SHISETSU"]["LOGIN_IP"] = $ip=@getenv("REMOTE_ADDR");//IPアドレス
			//セッションデータ確定のため、リロードする
				//セッションキーを変更したので改めてセットしなおす
				$sid = clean(conv(SID,$keitai_char),$keitai_char);

				header("Location:./index.php?" . $sid);
				exit;
			}
		}
		If ($cnt == 0){
			$err = "<P>該当する携帯が存在しません。</P>";
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

	  }else{
	  	$err = "該当する携帯が存在しません";
	  }
  }
?>
<P>ログイン</P>
<P>登録を行う場合はログインを行ってください</P>
<HR>
<Form method="POST" action="./index.php">
<P>ID:<INPUT size="20" type="text" name="username" maxlength = "20" <?=set_ime("IME_OFF");?>>
<br>
PW:
<INPUT size="20" type="text" name="password" maxlength = "20" <?=set_ime("IME_OFF");?>></P><BR>
<INPUT type="submit" name="login" value="ログイン"><BR>
<BR>
<font color = "red"><?php print($err); ?></font>
</FORM>
<form method="POST" action="./index.php" utn>
  <input type="submit" name="login" value="簡単ログイン" />
</form>

