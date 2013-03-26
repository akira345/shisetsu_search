<?php
//施設検索システム共通ファイル
//セッション開始
session_start();
//設定ファイルインクルード
require_once('setting.php');

//DBパラメタ用
$param = array();


//Pearライブラリインクルード
////Pear MDB2をインクルード
require_once('MDB2.php');

//	MDB2のエラー発生時呼び出す関数をセット
//errorHandlerを使うように設定
PEAR::setErrorHandling(PEAR_ERROR_CALLBACK, 'chk_exec');


//DB登録用
$touroku_date = date('Ymd');
$touroku_time = date('His');

//念のためセッションを張りなおす
//DoCoMoだけはセッションIDを固定にする
If (chk_mobile() <> 1 ){
	session_regenerate_id(TRUE);
}
	//DB接続チェック
	$conn =& MDB2::connect($dsn);
	if( MDB2::isError($conn) ) {
	    echo "データベースに接続できません。処理を中止します。";
	//    echo 'Standard Message: ' . $conn->getMessage() . "\n";
	//    echo 'Standard Code: ' . $conn->getCode() . "\n";
	//    echo 'DBMS/User Message: ' . $conn->getUserInfo() . "\n";
	//    echo 'DBMS/Debug Message: ' . $conn->getDebugInfo() . "\n";
	    exit;
	}
	//フェッチモードをオブジェクト形式に設定
	$conn->setFetchMode(MDB2_FETCHMODE_OBJECT);



//いくつかの共通関数定義

//SQL実行結果チェックサブ
function chk_exec($ret){
	If (PEAR::isError($ret)){
		die($ret->getMessage());
		exit;
	}
}
//文字コード変換サブ
//引数：変換対象文字列、変換後文字コード
function conv($str,$par){
	return	mb_convert_encoding($str,$par,"AUTO");
}

//変数存在チェック(配列不可）
//引数：存在チェック対象
//戻値：定義されていないかNULLか空("")：FALSEそれ以外：TRUE
function chk_value($in_value){
	If (isset($in_value) == TRUE){
		//値が存在
		If(empty($in_value) == TRUE && is_numeric($in_value) == FALSE){
			//値が数値以外で空である
			return FALSE;
		}
		return TRUE;
	}else{
		return FALSE;
	}
}


//文字列をきれいにする
//2008/10/20 XSS対策
function clean($str,$charset){
//	$tmp = htmlspecialchars($str);
	If (chk_value($charset) == FALSE){
	//文字コードが入っていない場合はエラーとする。
		//$charset = mb_internal_encoding();
		echo "内部エラー";
		exit;
	}
	//念のため文字コードチェック
	If (mb_check_encoding($str,$charset) == FALSE){
		//文字コードがおかしい
		echo "内部エラー";
		exit;
	}
	//2回通すと＆が増えるので一回デコードしてエンコードする
	$str = html_entity_decode($str, ENT_QUOTES, $charset);
	
	$tmp = htmlentities($str, ENT_QUOTES, $charset);
	return rtrim($tmp);
}
//機種判定関数
//戻値：１：DoCoMo 2:Au 3:SoftBank 0:その他(PC)
function chk_mobile(){
//注意！追加する際、J-PHONE関連はAUより先に判定すること！！
//      (VodaFoneの一部機種にUP.Browserを返すものがあるため

	//ユーザーエージェント取得
	$IN_AGENT = $_SERVER["HTTP_USER_AGENT"];

	If ( preg_match( "/DoCoMo/", $IN_AGENT) ) {
		//DoCoMo
		Return 1;
		exit;
	} Else If ( preg_match( "/SoftBank/", $IN_AGENT)) {
		//SoftBank
		Return 3;
		exit;
	} Else If ( preg_match( "/J-PHONE/", $IN_AGENT)) {
		//SoftBank
		Return 3;
		exit;
	} Else If ( preg_match ("/Vodafone/", $IN_AGENT)){
		//SoftBank
		Return 3;
		exit;
	} Else If ( preg_match( "/MOT-/",$IN_AGENT)) {
		//SoftBank モトローラ
		Return 3;
		exit; 
	} Else If ( preg_match( "/UP\.Browser/", $IN_AGENT)) {
		//Au
		Return 2;
		exit;
	} Else If ( preg_match( "/KDDI-/", $IN_AGENT)){
		//Au
		Return 2;
		exit;
	} Else {
		//その他(PC)
		Return 0;
		Exit;
	}
}

function set_ime($par){
	//IME制御
	//キャリア判別
	$kyaria = chk_mobile();
	If ($kyaria===0){
		//PC
		switch ($par) {
		    case "IME_ON":
			return "style=\"ime-mode:active\"";
		        break;
		    case "IME_OFF":
		    case "NUMBER":
		        return "style=\"ime-mode:disabled\"";
		        break;
		    default:
		        break;
		}
	}
	If ($kyaria === 1 || $kyaria === 2){
		//DoCoMoとAU
		switch ($par) {
		    case "IME_ON":
			return "istyle=\"1\"";
		        break;
		    case "IME_OFF":
		        return "istyle=\"3\"";//PC版にあわせるため、半角英字にする。
		        break;
			case "NUMBER":
		        return "istyle=\"4\"";//携帯のみ。
		        break;
		    default:
		        break;
		}

	}
	If ($kyaria === 3){
		//SoftBank
		switch ($par) {
		    case "IME_ON":
			return "mode=\"hiragana\"";
		        break;
		    case "IME_OFF":
		        return "mode=\"alphabet\"";//PC版にあわせるため、半角英字にする。
		        break;
			case "NUMBER":
		        return "mode=\"numeric\"";//携帯のみ。
		        break;
		    default:
		        break;
		}

	}
}

//改行コードを<BR>タグにする
function cr_to_br($IN_STR){
	$IN_STR = str_replace("\r\n", "<br>", $IN_STR);
	$IN_STR = str_replace("\r", "<br>", $IN_STR);
	$IN_STR = str_replace("\n", "<br>", $IN_STR);
	return $IN_STR;
}

//訪問回数を検索し、結果を返す
function cnt_houmon($no,$conn){
	$sql = "";
	$sql .= "select count(*) as cnt from houmon where no = '{$no}'";
	$sql = conv($sql,"UTF-8");
	$ret =& $conn->query($sql);
//	chk_exec($ret);
	while ( $row = $ret->fetchRow() ) {
		$cnt = conv($row->cnt,"EUC-JP");
	}
	If (chk_value($cnt) == TRUE){
		return $cnt;
	}else{
		return 0;
	}
}

//選択プルダウンメニューを作成。
//引数：プルダウンの名前、最小値、最大値、表示No
function value_list($name,$min_no,$max_no,$set_no){
$ret = "<SELECT name=\"" . $name . "\">" . "\r\n";
	for($i=$min_no;$i<=$max_no;$i++){
		If ($i == $set_no){
			//一致したら
			$ret .= "<OPTION value=\"" . $i ."\" selected>" . $i . "</OPTION>" . "\r\n";
		}else{
			$ret .= "<OPTION value=\"" . $i ."\">" . $i . "</OPTION>" . "\r\n";
		}
	}
$ret .= "</SELECT>" . "\r\n";
return $ret;
}

//ログインのチェック
//ログインしていないと、TOPへ強制送還させる
function chk_login(){
	If (chk_value($_SESSION["SHISETSU"]["ID"]) == TRUE){
		//なにもしない
	}else{
		//セッションが存在しない
		header("Location: index.php");
		exit;
	}
}

//2点間の距離を算出する。単位はKm
//http://www2s.biglobe.ne.jp/~satosi/gmap/map_length.htmlをPHPに焼き買え
//使用例：echo cal_length(135.73419570922852,34.97195515210841,135.7858657836914,34.96660970979051);
function cal_length($x1,$y1, $x2,$y2){
	$x1 = $x1 * M_PI / 180.0;
	$y1 = $y1 * M_PI / 180.0;
	$x2 = $x2 * M_PI / 180.0;
	$y2 = $y2 * M_PI / 180.0;

	$A = 6378137; // 地球の赤道半径(6378137m)
	
	$x = $A * ($x2-$x1) * cos( $y1 );
	$y = $A * ($y2-$y1);

	$L = sqrt($x*$x + $y*$y);	// メートル単位の距離
	$L = floor($L / 1000);	//Km単位にする
	return  $L;
}
	

//SQL実行関連
function exec_sql($sql,$param,$db_char,$web_char,$conn){
	
	//コンバート
	$sql = conv($sql,$db_char);
	mb_convert_variables($db_char,$web_char,$param);

	//実行
	$tmp=$conn->prepare($sql);
	$ret=$tmp->execute($param);

	//結果
	return $ret;
}



//簡単ログイン関連
//本当はIPでちゃんと調べないといけないが、簡易版とする。
//
//本クラスのベースはhttp://turi2.net/blog/709.htmlより拝借しました。
//
//以下のようなHTMLをはっておく
//<!-- form要素の場合 -->
//<form method="POST" action="./ktest.php" utn>
//  <input type="submit" value="ログイン" />
//</form>
//
class MobileInformation{

	var $_UserAgent;	//ユーザエージェント

	function MobileInformation(){
	//コンストラクタ
	//ユーザエージェントをセットするだけ
		$this->_UserAgent = $_SERVER["HTTP_USER_AGENT"];	
	}
	
	
	//固体識別番号の取得
	function IndividualNum(){
		$line = "";
		$edline = 0;
		$agent = $this->_UserAgent;
		$len = strlen($agent);
		$rtn = 0;//戻り値
		$prob = chk_mobile();//キャリア判定
		//
		switch($prob){
			case '2':
			//AU
				if(chk_value($_SERVER['HTTP_X_UP_SUBNO']) == TRUE){
					//固体識別番号が入っていたら取得
					$rtn = $_SERVER['HTTP_X_UP_SUBNO'];
				}
				break;
			case '1':
			//DoCoMo
				if(strpos($agent, '/ser')){
					//非FOMA端末用
					$line = strpos($agent, '/ser') + 4;
				}
				if(strpos($agent, ';icc')){
					//Foma端末用
					$line = strpos($agent, ';icc') + 4;
				}
				//取得した情報の中よりユーザエージェント情報が邪魔なので消す
				if(chk_value($line) == TRUE){
					$rtn = substr($agent, $line, $len-($line+1));
				}
				break;
			case '3':
			//SoftBank
				if(strpos($agent, '/SN')){
				//取得
					$line = strpos($agent, '/SN') + 3;
				}
				if(chk_value($line) == TRUE){
					$edline = strpos($agent, ' ', $line);
				}
				if(chk_value($edline) == TRUE){
					$rtn = substr($agent, $line, $edline-$line);
				}
				break;
			default:
			//その他
		}
		return $rtn;
	}
} 

?>