<?php
//GoogleMaps 座標取得さんぷる
//何気にPearを使用してみる。
//以下のサンプルを参考にしました。THX!
//http://www.geekpage.jp/
//http://japonyol.net/editor/googlemaps.html
//http://f3.aaa.livedoor.jp/~matukazu/pear/db_result.php

//動作環境
//CentOS 5.2
//PHP 5.2.6
//MySQL 5.0.45
//Pear MDB2 2.4.1
//使用しているAPIとか
//
//GoogleMaps
//ReGiocoding


//共通ライブラリ読み込み
require_once('./lib/common.php');

//機種判定追加
If (chk_mobile() <> 0){
	//携帯版へ
	header("Location: ./mb/index.php");
	exit;
}

//ログインチェック
If(chk_value($_SESSION["SHISETSU"]["ID"]) == FALSE){
	require_once('./login.php');
}

//POSTデータ取得
	$x = clean($_POST["x"],$web_char);
	$y = clean($_POST["y"],$web_char);
	$zoom = clean($_POST["zoom"],$web_char);

	$text = clean($_POST["text"],$web_char);
	
	IF (chk_value($_POST["mode"]) == TRUE){
		$mode = clean($_POST["mode"],$web_char);
	}else{
		$mode = clean($_GET["mode"],$web_char);
	}

	//小文字に変換
	$mode = strtolower($mode);

	$comment = clean($_POST["comment"],$web_char);
	$no = clean($_GET["no"],$web_char);
	
//参照モードの場合はGetで座標を取得
	If ($mode == "view"){
		$x = clean($_GET["x"],$web_char);
		$y = clean($_GET["y"],$web_char);
		$zoom = clean($_GET["zoom"],$web_char);
	}

//数字チェック
//経度、緯度は少数を含むのでis_numericでチェックする

	If (is_numeric($x) == FALSE){
		$x = NULL;
	}
	
	If (is_numeric($y) == FALSE){
		$y = NULL;
	}
	If (ctype_digit($zoom) == FALSE){
		$zoom = NULL;
	}
	
	If (chk_value($x) == FALSE || chk_value($y) == FALSE){
		//初期値セット
		$x = 132.5115966796875;
		$y = 34.8047829195724;
	}

	//ズームレベル設定
	If (chk_value($zoom) == TRUE){
		//取得できるズームのレベルと表示設定のレベルが逆なので変換する
		$zoom = 17 - $zoom;
		//レベルは０から１９までだが、オーバーしても特に害が無いのでチェックしない
	}else{
		//デフォールト
		$zoom = 5;
	}
	//セッションより登録者ID取得
	$s_touroku_id = $_SESSION["SHISETSU"]["USER_NM"];
	//セッションより登録者IP取得
	$s_ip = $_SESSION["SHISETSU"]["LOGIN_IP"];

//外部からの変数処理ーここまでー
//データの削除
//ログインしていることが条件
If ($mode == "del" && chk_value($_SESSION["SHISETSU"]["ID"]) == TRUE){

	//数字のチェックはis_numberよりctype_digitがより厳密らしい(ただし少数は受け付けない。）
	If (ctype_digit($no)){
	//削除前に座標を確保しておく
		//SQL
		$sql = "select x,y,zoom from googlemap where no in (?) ";
		//パラメタ
		$param = array($no);

	//実行
		$ret = exec_sql($sql,$param,$db_char,$web_char,$conn);

		while ( $row = $ret->fetchRow() ) {
			//座標
			$x = clean(conv($row->x,$web_char),$web_char);
			$y = clean(conv($row->y,$web_char),$web_char);
			$zoom = clean(conv($row->zoom,$web_char),$web_char);
		}

		//SQL
		$sql = "";
		$sql = "delete from googlemap where NO = ? and touroku_id = ?";
		//パラメタ
		$param = array($no,$s_touroku_id);

	//実行
		$ret = exec_sql($sql,$param,$db_char,$web_char,$conn);

		//Get文字列構築
		$params = array(
			'mode' => 'view',
			'y' => $y,
			'x' => $x,
			'zoom' => $zoom
		);
		header("Location:./index.php?" . http_build_query($params));
		exit;

	}
}

//データの登録
//登録ボタンが押され、追加モーど、かつログインされている
If (chk_value($_POST["submit"]) == TRUE && $mode== "ins" && chk_value($_SESSION["SHISETSU"]["ID"]) == TRUE){
	//Googleの座標より住所を逆変換する
	
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

	//Get XML
	$get_item = simplexml_load_file($service_url);
	
	//データ初期化
	$todofuken = "";
	$sikugun = "";
	$jyusyo = "";
	If (chk_value($get_item) == TRUE){
		//XML受信
		If (($get_item->status) == TRUE){
			//正常
			//都道府県情報取得
			$todofuken = clean(conv(($get_item->prefecture->pname),$web_char),$web_char);
			//市区郡
			$sikugun = clean(conv(($get_item->municipality->mname),$web_char),$web_char);
			If (chk_value($get_item->local->section) == TRUE){
				//住所（現在番地までは持たない）
				$jyusyo = clean(conv(($get_item->local->section),$web_char),$web_char);
			}else{
				$jyusyo = "";
			}
		}
	}
	
	//ズームの値は表示用に１７を引いているので、逆算する
	$zoom = 17 - $zoom ;

	//SQL
	$sql = "";
	$sql .= " insert into googlemap (x,y,zoom,text,comment,touroku_id,ip,todofuken,sikugun,jyusyo,touroku_date,touroku_time) ";
	$sql .= " values ";
	$sql .= " (?,?,?,?,?,?,?,?,?,?,?,?) ";

	//パラメタ
	$param = array($x,$y,$zoom,$text,$comment,$s_touroku_id,$s_ip,$todofuken,$sikugun,$jyusyo,$touroku_date,$touroku_time);

	//実行
	$ret = exec_sql($sql,$param,$db_char,$web_char,$conn);

	//登録完了したのでリロードしておく

	//Get文字列付加
	$params = array(
		'mode' => 'view',
		'y' => $y,
		'x' => $x,
		'zoom' => $zoom
	);
	//URL構築
	header("Location:./index.php?" . http_build_query($params));
	exit;
	
}

//噴出し作成
//ここは全処理共通

	//SQL
	$sql = "";
	$sql = " select * from googlemap order by NO ";

	//パラメタ
	$param = array();

	//実行
	$ret = exec_sql($sql,$param,$db_char,$web_char,$conn);

	$fukidasi = "";
	$view_list = "";
	$view_list_java = "";
	while ( $row = $ret->fetchRow() ) {
		//タイトル取得
		$title = clean(conv($row->text,$web_char),$web_char);
		//コメント取得
		$comment = clean(conv($row->comment,$web_char),$web_char);
		//データNO
		$row_no = clean(conv($row->no,$web_char),$web_char);
		//座標
		$fx = clean(conv($row->x,$web_char),$web_char);
		$fy = clean(conv($row->y,$web_char),$web_char);
		//登録者
		$touroku_id = clean(conv($row->touroku_id,$web_char),$web_char);

		//登録日
		$touroku_date = clean(conv($row->touroku_date,$web_char),$web_char);
		$touroku_date = substr($touroku_date,0,4) . "/" . substr($touroku_date,4,2) . "/" . substr($touroku_date,6,2);

		//タイトルに訪問回数を追加
		$title = $title . "(" . cnt_houmon($row_no,$conn) . ")";
		
		//マーカーへのジャンプリンク作成
		$view_list .= "<a href=# onclick=\"go_to_maker(" . $row_no . ")\">";
		$view_list .= $title;
		$view_list .= "</a><BR>\n";

		//JavaScript関数 go_to_maker作成
		$view_list_java .= "        case " . $row_no . ":\n";
		$view_list_java .= "          point = new GLatLng(" . $fy . "," . $fx . ");\n";
		$view_list_java .= "          break;\n";
		
		//マーカーの文字を作成する
		$view_text  = "<B>" . addslashes($title) . "</B>";//JavaScriptなのでaddslashesを通す
		$view_text .= "<HR width=200><BR>";
		$view_text .= cr_to_br(addslashes($comment));//改行をBRに置換する
		$view_text .= "<BR><BR>";
		
		//ログインしている場合は訪問記録の追加を許可する
		If (chk_value($s_touroku_id) == TRUE){
			//ログイン中
			
			//Get文字列構築
			$params = array(
				'mode' => 'ins',
				'no' => $row_no
			);
			
			$view_text .= "<a href=\"rec_comment.php?" . http_build_query($params) . "\">";
			$view_text .= "訪問記録をつける";
			$view_text .= "</a><br>";
		}
		//訪問履歴が存在する場合
		If (cnt_houmon($row_no,$conn)>0){
		//訪問記録を見る
		
			//Get文字列構築
			$params = array(
				'no' => $row_no
			);
		
			$view_text .= "<a href=\"view_houmon.php?" . http_build_query($params) . "\">";
			$view_text .= "訪問記録を見る";
			$view_text .= "</a><br>";
		}
		
		//マーカーの削除はログインしていないと禁止
		//かつ、登録者でなければならない
		If ($s_touroku_id == $touroku_id){
		
			//Get文字列構築
			$params = array(
				'mode' => 'del',
				'no' => $row_no
			);
		
			$view_text .= "<a href=\"index.php?" . http_build_query($params) . "\" ";
			$view_text .= "onclick=\"return confirm(\\'本当に削除しますか？\\')\"";
			$view_text .= ">";
			$view_text .= "このマーカーを削除する";
			$view_text .= "</a><BR><BR>";
		}
		$view_text .= "(".$touroku_id.")<BR>(" . $touroku_date .")";
		
		//マーカーの座標が逆なので注意
		$fukidasi .="      //" . $title . "\n";
		$fukidasi .="      var point" . $row_no . " = new GLatLng(" . $fy . "," . $fx .");\n";
		$fukidasi .="      var marker" . $row_no . " = createMarker(point" . $row_no . ", '" . $view_text . "');\n";
		$fukidasi .="      map.addOverlay(marker" . $row_no . ");\n";
		
	}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
<meta http-equiv="Content-Type" content="text/html; charset=euc-jp">

    <title>施設マップシステム</title>
    <script src="http://maps.google.com/maps?file=api&v=1&key=<?=$gmap_key?>"
        type="text/javascript" charset="utf-8"></script>

<script type="text/javascript"> 
<!-- 

function check(){
//必須入力チェック
	var flag = 0;
	//施設名
	if(document.form1.text.value == ""){
		flag = 1;
	}

	//えらー
	if(flag){
		window.alert('必須項目に未入力がありました');
		return false; // 送信を中止
	}
	else{
		return true; // 送信を実行
	}
}

// -->
</script>

  </head>
  <body >

<p>
施設マップシステム
<table border="1" height=480>
<tr><td>
<script type="text/javascript" charset="utf-8">
if (GBrowserIsCompatible()) {
	document.write('<div id="map" style="width: 600px; height: 400px"><\/div>');//表示サイズ指定
} else {
	document.write('お使いのブラウザではJavascriptに対応していないか、Javascript がオフになっています。Javascript の動くブラウザでないと地図は表示できません');
}
</script>
<noscript>お使いのブラウザではJavascriptに対応していないか、Javascript がオフになっています。Javascript の動くブラウザでないと地図は表示できません</noscript>
</td><td align=center width=20%>
<?=$view_list?>
</td></tr>
</table>
</p>
    <script type="text/javascript">
    if (GBrowserIsCompatible()) {
      var map = new GMap(document.getElementById("map"),{draggableCursor: 'crosshair',draggingCursor: 'move'});
      map.addControl(new GLargeMapControl());
      map.addControl(new GMapTypeControl());
	//初期表示座標設定	  
	map.centerAndZoom(new GPoint(<?=$x?>, <?=$y?>), <?=$zoom?>);

	//噴出し処理
	//クリックしたときに噴出しを出す関数
      function createMarker(point,html) {
        var marker = new GMarker(point);
        GEvent.addListener(marker, "click", function() {
          marker.openInfoWindowHtml(html);
        });
        return marker;
      }

		<?=$fukidasi?>
    } else {
		alert("お使いのブラウザでは地図を表示することができません。Internet Explorer 6.0以降か、Firefox 1.0以降をおすすめします。");
	}
//ズームイベントを取得する
    GEvent.addListener(map, 'zoomend',
                       function(oldZoomLevel, newZoomLevel) {
    // document.getElementById("zoom_old").innerHTML = oldZoomLevel;
    // document.getElementById("zoom_new").innerHTML = newZoomLevel;
	//form1のhidden要素にセット
	document.form1.zoom.value=newZoomLevel;
    });
	<?php
		//ログインが有効なら表示
		If (isset($_SESSION["SHISETSU"]["ID"])){
	?>

//クリックイベント
    GEvent.addListener(map, 'click', function(overlay, point) {
      if (point) {
        document.getElementById("show_x").innerHTML = point.x;
        document.getElementById("show_y").innerHTML = point.y;
		//form1のhidden各要素に値をセット
		document.form1.x.value=point.x;
		document.form1.y.value=point.y;
      }
    });
	<?}?>
//中心座標取得
	function getCenterLatLng(){
	  var latlng = map.getCenter();
	  var lat = latlng.lat();
	  var lng = latlng.lng();
	  var str = "http://<?=$_SERVER["HTTP_HOST"].$_SERVER["PHP_SELF"]?>?mode=view&y=" + lat + "&x=" + lng + "&zoom=" + document.form1.zoom.value;
	  document.form2.text.value = str;
	}

//ポインタ移動関数
	function go_to_maker(n){
        switch (n) {
<?=$view_list_java?>
        default:
          break;
        }

        map.panTo(point);
	
	}
    </script>
<HR>
<?php
	//ログインが有効なら表示
	If (isset($_SESSION["SHISETSU"]["ID"])){
?>
<p> 施設情報追加の場合は、地図上の地点をダブルクリックしてください。</p>

<h3>現在選択した座標</h3>
    <P id="show_x"></P>
    <P id="show_y"></P>
	<?}?>
<Form action=./index.php method=POST name=form1 onSubmit="return check()">
	<input type=hidden name=zoom>
<?php
	//ログインが有効なら表示
	If (isset($_SESSION["SHISETSU"]["ID"])){
?>
	<input type=hidden name=x>
	<input type=hidden name=y>
	<input type=hidden name=mode value="ins">
	<p>施設名を入力してください(必須)</p>
	<input type=text name=text <?=set_ime("IME_ON");?> >
	<p>コメントを入力してください</P>
	<textarea name="comment" cols=40 rows=4 <?=set_ime("IME_ON");?> ></textarea>
	<input type=submit value="送信" name=submit>
<?php
	}
?>
</form>
<a href="#" onclick="getCenterLatLng();return false;">この地図のアドレスを表示する</a>
<Form name=form2>
	<input type=text name=text size=150>
</Form>
  </body>

</html>
