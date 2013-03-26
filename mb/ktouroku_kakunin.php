<?php
//施設登録（携帯）

require_once('../lib/common.php');


If (chk_mobile() == 0){
	//PC版へ(ここはPC立ち入り禁止！）
	header("Location: ../index.php");
	exit;
}

//ログインチェック
chk_login();

//以下認証OK後の処理


//セッションチェック
If ($_SESSION["SHISETSU_POSTDATA"]["status"] <> "kakunin"){
	//不正アクセス
	header("Location: index.php");
	exit;
}

//セッション変数展開
	$jyusyo = $_SESSION["SHISETSU_POSTDATA"]["jyusyo"];
	$title = $_SESSION["SHISETSU_POSTDATA"]["title"];
	$comment = $_SESSION["SHISETSU_POSTDATA"]["comment"];
	$x = $_SESSION["SHISETSU_POSTDATA"]["x"];
	$y = $_SESSION["SHISETSU_POSTDATA"]["y"];
//戻る処理
If (chk_value($_GET["back"]) == TRUE){
	$_SESSION["SHISETSU_POSTDATA"]["status"] = "back";
	header("Location: ktouroku.php?".$sid);
	exit;
}
//更新処理
If(chk_value($_GET["submit"]) == TRUE){

	If (chk_value($x) == FALSE || chk_value($y) == FALSE){
		//住所ー＞座標に変換する
	
		//Giocodingを利用します。感謝！
		$service_url = 'http://www.geocoding.jp/api/';
		//Get文字列付加
		$params = array(
			'v' => 1.1,
			'q' =>  $jyusyo
		);
		//URL構築
		$service_url .= '?' . http_build_query($params);
		//echo $service_url;
		//Get XML
		$get_item = simplexml_load_file($service_url);
		//var_dump($get_item);
		
		//データ初期化
		$x = "";
		$y = "";
		If (chk_value($get_item) == TRUE){
			//XML受信
			If (($get_item->error) == TRUE){
				echo "内部エラー発生！";
				exit;
			}else{
				//正常
				$x = clean(conv((double)($get_item->coordinate->lng),$keitai_char),$keitai_char);
				$y = clean(conv((double)($get_item->coordinate->lat),$keitai_char),$keitai_char); 
				If(chk_value($x) == FALSE || chk_value($y) == FALSE){
				//複数候補地が存在した場合はエラー
					echo "内部エラー発生！";
					exit;
				}else{
				//OK
				}
			}
		}else{
			echo "内部エラー発生！";
			exit;
		}
	}
	//取得した座標をregiocodingして戻す
	
	//ReGiocodingを利用します。感謝！
	$service_url = 'http://refits.cgk.affrc.go.jp/tsrv/jp/rgeocode.php';
	//Get文字列付加
	$params = array(
		'v' => 1,
		'lon' => $x,
		'lat' => $y
	);
	//URL構築
	$service_url .= '?' . http_build_query($params);
	//echo $service_url;
	//Get XML
	$get_item = simplexml_load_file($service_url);
	//var_dump($get_item);
	
	//データ初期化
	$todofuken = "";
	$sikugun = "";
	$jyusyo = "";
	If (chk_value($get_item) == TRUE){
		//XML受信
		If (($get_item->status) == TRUE){
			//正常
			echo "A";
			//var_dump($get_item);
			echo conv($get_item->prefecture->pname,$keitai_char);
			//都道府県情報取得
			$todofuken = clean(conv(rtrim($get_item->prefecture->pname),$keitai_char),$keitai_char);
			//市区郡
			$sikugun = clean(conv(rtrim($get_item->municipality->mname),$keitai_char),$keitai_char);
			If (chk_value($get_item->local->section) == TRUE){
				//住所（現在番地までは持たない）
				$jyusyo = clean(conv(rtrim($get_item->local->section),$keitai_char),$keitai_char);
			}else{
				$jyusyo = "";
			}
		}else{
			echo "内部エラー";
			exit;
		}
	}else{
		echo "内部エラー";
		exit;
	}
	//セッションより登録者ID取得
	$s_touroku_id = $_SESSION["SHISETSU"]["USER_NM"];
	//セッションより登録者IP取得
	$s_ip = $_SESSION["SHISETSU"]["LOGIN_IP"];
	

	//zoomは適当にセット
	$zoom = 10;

	//SQL
	$sql = "";
	$sql .= " insert into googlemap (x,y,zoom,text,comment,touroku_id,ip,todofuken,sikugun,jyusyo,touroku_date,touroku_time) ";
	$sql .= " values ";
	$sql .= " (?,?,?,?,?,?,?,?,?,?,?,?)";

	//パラメタ
	$param = array($x,$y,$zoom,$title,$comment,$s_touroku_id,$s_ip,$todofuken,$sikugun,$jyusyo,$touroku_date,$touroku_time);
	
	//実行
	$ret = exec_sql($sql,$param,$db_char,$keitai_char,$conn);

	$_SESSION["SHISETSU_POSTDATA"] = NULL;

print<<<EOD
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=shift_jis">
<title></title>
</head>
<body>
<p>登録完了</P>
<BR>
<a href=./index.php?{$sid}>TOPへ戻る</a>
</body>
</html>
EOD;
exit;

}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=shift_jis">
<title></title>
</head>
<body>
<p>施設登録(確認）
<hr>
<P>住所:<?=$jyusyo?></P>
<P>施設名：<?=$title?></p>
<P>コメント：<?=$comment?></P>
<form action=./ktouroku_kakunin.php method=get>
<input type="submit" value="戻る" name="back">　<input type="submit" value="更新" name="submit">
<input type="hidden" name="<?=session_name()?>" value="<?=clean(conv(session_id(),$keitai_char),$keitai_char)?>">

</form>
</body>
</html>
