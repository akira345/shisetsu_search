<?php
//携帯版マーカー登録

require_once('../lib/common.php');


If (chk_mobile() == 0){
	//PC版へ(ここはPC立ち入り禁止！）
	header("Location: ../index.php");
	exit;
}

//ログインチェック
chk_login();

//以下認証OK後の処理

//初期値設定
$jyusyo = "";
$err_jyusyo = "";
$title = "";
$err_title = "";
$comment = "";

//携帯GPS座標取得準備
// ユーザエージェント
$agent = $_SERVER['HTTP_USER_AGENT'];

//表示名
$lavel = "現在地取得";

// URLを作成
$url = 'http://api.cirius.co.jp/v1/geoform/xhtml?';
$url .= 'ua=' . urlencode($agent);
$url .= '&return_uri=' . urlencode($keitai_ret_url);
$url .= '&api_key=' . $keitai_gps_key;
$url .= '&display=' . urlencode($lavel);

//携帯3社別のアドレス取得ページへのリンク生成
$contents = conv(file_get_contents($url),$keitai_char);



//更新処理
If (chk_value($_GET["submit"]) == TRUE){
	//データGet
	$jyusyo = clean($_GET["jyusyo"],$keitai_char);
	$title = clean($_GET["title"],$keitai_char);
	$comment = clean($_GET["comment"],$keitai_char);
	$x = clean($_GET["x"],$keitai_char);
	$y = clean($_GET["y"],$keitai_char);
		
	//データチェック
	$flg = 0;
	$err_jyusyo = "";
	$err_title = "";
	$err_msg = "";

	//タイトル
	If (chk_value($title) == FALSE){
		//タイトルが入っていない
		$flg = 1;
		$err_title = "<font color=red>施設名を入れてください</font><BR>";
	}
	//住所チェック
	If(chk_value($jyusyo) == FALSE){
		//住所が入っていない
		$flg = 1;
		$err_jyusyo = "<font color=red>住所を入れてください</font><BR>";
	}elseIf (chk_value($x) == FALSE || chk_value($y) == FALSE){
		//座標データが取得できなかった場合
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
		//exit;
		//データ初期化
		$x = "";
		$y = "";
		If (chk_value($get_item) == TRUE){
			//XML受信
			If (chk_value($get_item->error) == TRUE){
				$flg = 1;
				$err_jyusyo = "<font color=red>住所データ変換エラーです。</font><BR>";
			}else{
				//正常
				$x = clean(conv((double)$get_item->coordinate->lng,$keitai_char),$keitai_char);
				$y = clean(conv((double)$get_item->coordinate->lat,$keitai_char),$keitai_char); 
				If(chk_value($x) == FALSE || chk_value($y) == FALSE){
					//複数候補あり
					$flg = 1;
					$err_jyusyo = "<font color=red>住所に複数の候補が存在しました。詳細な住所を入力してください。</font><BR>";
				}else{
					//OK
				}
			}
		}else{
			$flg = 1;
			$err_msg = "住所データ変換エラーです。しばらくお待ちください<BR>";
		}
	}
	If ($flg==0){
		//OK
		//セッションへ退避
		$_SESSION["SHISETSU_POSTDATA"]["title"] = $title;
		$_SESSION["SHISETSU_POSTDATA"]["jyusyo"] = $jyusyo;
		$_SESSION["SHISETSU_POSTDATA"]["comment"] = $comment;
		$_SESSION["SHISETSU_POSTDATA"]["x"] = $x;
		$_SESSION["SHISETSU_POSTDATA"]["y"] = $y;
		
		$_SESSION["SHISETSU_POSTDATA"]["status"] = "kakunin";
		//確認ページへGo
		header("Location: ktouroku_kakunin.php?".$sid);
		exit;

	}


}
//戻ってきた処理
If ($_SESSION["SHISETSU_POSTDATA"]["status"] =="back"){
	//フォームデータ復元
	$jyusyo = $_SESSION["SHISETSU_POSTDATA"]["jyusyo"];
	$title = $_SESSION["SHISETSU_POSTDATA"]["title"];
	$comment = $_SESSION["SHISETSU_POSTDATA"]["comment"];
	$x = $_SESSION["SHISETSU_POSTDATA"]["x"];
	$y = $_SESSION["SHISETSU_POSTDATA"]["y"];
	//ステータスはクリア
	$_SESSION["SHISETSU_POSTDATA"]["status"]= "";
}

//API座標取得時処理
If (chk_value($_GET['lat'] == TRUE) && chk_value($_GET['lon']) == TRUE){
	//座標データが存在した
	//データ取得
	//APIの座標文字コードが不明なので一応変換しておく
	$y = (double)clean(conv($_GET["lat"],$keitai_char),$keitai_char);
	$x = (double)clean(conv($_GET["lon"],$keitai_char),$keitai_char);
	$lv = clean(conv($_GET['accuracy'],$keitai_char),$keitai_char);
	$seido = "かなりアバウト";
	If ($lv ==3){
		$seido = "誤差50mより小";
	}elseIf ($lv ==2){
		$seido = "誤差300m以下";
	}elseIf ($lv ==1){
		$seido = "誤差300mより大";
	}elseIf ($lv ==0){
		$seido = "かなりアバウト";
	}
//取得した座標より住所を割り出す。（同一住所でも座標が異なる場合があるため）
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
			//初期値構築
			$jyusyo = ltrim($todofuken) . ltrim($sikugun) . ltrim($jyusyo);
		}
	}


} 


?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=shift_jis">
<title></title>
</head>
<body>
施設登録
<HR>
<form action=ktouroku.php method=get>
<input type="hidden" name="<?=session_name()?>" value="<?=clean(conv(session_id(),$keitai_char),$keitai_char)?>">
<P><font color=red><?=$err_msg?></font></P>
<p>登録する住所を入力してください<br>
<input type=text name="jyusyo" value="<?=$jyusyo?>" <?=set_ime("IME_ON")?> size=20><br>
<?If (isset($seido)){echo"（" . $seido . "）";}?>
<BR>
<?=$err_jyusyo?>
<br>
<?=$contents?><BR>(!注意！施設名、コメントが消えます。
<BR>又、必ずしも取得できるわけではありません。）
<BR>
<br>
施設名を入力<br>
<input type=text size=20 name="title" value="<?=$title?>" <?=set_ime("IME_ON")?> ><br>
<?=$err_title?>
<br>
コメントを入力<br>
<textarea name="comment" <?=set_ime("IME_ON")?> ><?=$comment?></textarea><br>
<br>
<input type="submit" value="送信" name="submit"></p>
<input type="hidden" value="<?=$x?>" name="x">
<input type="hidden" value="<?=$y?>" name="y">
</form>
</body>
</html>
