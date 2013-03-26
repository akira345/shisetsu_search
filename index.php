<?php
//GoogleMaps ��ɸ��������פ�
//������Pear����Ѥ��Ƥߤ롣
//�ʲ��Υ���ץ�򻲹ͤˤ��ޤ�����THX!
//http://www.geekpage.jp/
//http://japonyol.net/editor/googlemaps.html
//http://f3.aaa.livedoor.jp/~matukazu/pear/db_result.php

//ư��Ķ�
//CentOS 5.2
//PHP 5.2.6
//MySQL 5.0.45
//Pear MDB2 2.4.1
//���Ѥ��Ƥ���API�Ȥ�
//
//GoogleMaps
//ReGiocoding


//���̥饤�֥���ɤ߹���
require_once('./lib/common.php');

//����Ƚ���ɲ�
If (chk_mobile() <> 0){
	//�����Ǥ�
	header("Location: ./mb/index.php");
	exit;
}

//����������å�
If(chk_value($_SESSION["SHISETSU"]["ID"]) == FALSE){
	require_once('./login.php');
}

//POST�ǡ�������
	$x = clean($_POST["x"],$web_char);
	$y = clean($_POST["y"],$web_char);
	$zoom = clean($_POST["zoom"],$web_char);

	$text = clean($_POST["text"],$web_char);
	
	IF (chk_value($_POST["mode"]) == TRUE){
		$mode = clean($_POST["mode"],$web_char);
	}else{
		$mode = clean($_GET["mode"],$web_char);
	}

	//��ʸ�����Ѵ�
	$mode = strtolower($mode);

	$comment = clean($_POST["comment"],$web_char);
	$no = clean($_GET["no"],$web_char);
	
//���ȥ⡼�ɤξ���Get�Ǻ�ɸ�����
	If ($mode == "view"){
		$x = clean($_GET["x"],$web_char);
		$y = clean($_GET["y"],$web_char);
		$zoom = clean($_GET["zoom"],$web_char);
	}

//���������å�
//���١����٤Ͼ�����ޤ�Τ�is_numeric�ǥ����å�����

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
		//����ͥ��å�
		$x = 132.5115966796875;
		$y = 34.8047829195724;
	}

	//�������٥�����
	If (chk_value($zoom) == TRUE){
		//�����Ǥ��륺����Υ�٥��ɽ������Υ�٥뤬�դʤΤ��Ѵ�����
		$zoom = 17 - $zoom;
		//��٥�ϣ����飱���ޤǤ����������С����Ƥ��ä˳���̵���Τǥ����å����ʤ�
	}else{
		//�ǥե������
		$zoom = 5;
	}
	//���å��������Ͽ��ID����
	$s_touroku_id = $_SESSION["SHISETSU"]["USER_NM"];
	//���å��������Ͽ��IP����
	$s_ip = $_SESSION["SHISETSU"]["LOGIN_IP"];

//����������ѿ������������ޤǡ�
//�ǡ����κ��
//�����󤷤Ƥ��뤳�Ȥ����
If ($mode == "del" && chk_value($_SESSION["SHISETSU"]["ID"]) == TRUE){

	//�����Υ����å���is_number���ctype_digit����긷̩�餷��(�����������ϼ����դ��ʤ�����
	If (ctype_digit($no)){
	//������˺�ɸ����ݤ��Ƥ���
		//SQL
		$sql = "select x,y,zoom from googlemap where no in (?) ";
		//�ѥ�᥿
		$param = array($no);

	//�¹�
		$ret = exec_sql($sql,$param,$db_char,$web_char,$conn);

		while ( $row = $ret->fetchRow() ) {
			//��ɸ
			$x = clean(conv($row->x,$web_char),$web_char);
			$y = clean(conv($row->y,$web_char),$web_char);
			$zoom = clean(conv($row->zoom,$web_char),$web_char);
		}

		//SQL
		$sql = "";
		$sql = "delete from googlemap where NO = ? and touroku_id = ?";
		//�ѥ�᥿
		$param = array($no,$s_touroku_id);

	//�¹�
		$ret = exec_sql($sql,$param,$db_char,$web_char,$conn);

		//Getʸ������
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

//�ǡ�������Ͽ
//��Ͽ�ܥ��󤬲����졢�ɲå⡼�ɡ����ĥ����󤵤�Ƥ���
If (chk_value($_POST["submit"]) == TRUE && $mode== "ins" && chk_value($_SESSION["SHISETSU"]["ID"]) == TRUE){
	//Google�κ�ɸ��꽻�����Ѵ�����
	
	//ReGiocoding�����Ѥ��ޤ������ա�
	$service_url = 'http://refits.cgk.affrc.go.jp/tsrv/jp/rgeocode.php';
	//Getʸ�����ղ�
	$params = array(
		'v' => 1,
		'lon' => $x,
		'lat' => $y
	);
	//URL����
	$service_url .= '?' . http_build_query($params);

	//Get XML
	$get_item = simplexml_load_file($service_url);
	
	//�ǡ��������
	$todofuken = "";
	$sikugun = "";
	$jyusyo = "";
	If (chk_value($get_item) == TRUE){
		//XML����
		If (($get_item->status) == TRUE){
			//����
			//��ƻ�ܸ��������
			$todofuken = clean(conv(($get_item->prefecture->pname),$web_char),$web_char);
			//�Զ跴
			$sikugun = clean(conv(($get_item->municipality->mname),$web_char),$web_char);
			If (chk_value($get_item->local->section) == TRUE){
				//����ʸ������ϤޤǤϻ����ʤ���
				$jyusyo = clean(conv(($get_item->local->section),$web_char),$web_char);
			}else{
				$jyusyo = "";
			}
		}
	}
	
	//��������ͤ�ɽ���Ѥˣ���������Ƥ���Τǡ��ջ�����
	$zoom = 17 - $zoom ;

	//SQL
	$sql = "";
	$sql .= " insert into googlemap (x,y,zoom,text,comment,touroku_id,ip,todofuken,sikugun,jyusyo,touroku_date,touroku_time) ";
	$sql .= " values ";
	$sql .= " (?,?,?,?,?,?,?,?,?,?,?,?) ";

	//�ѥ�᥿
	$param = array($x,$y,$zoom,$text,$comment,$s_touroku_id,$s_ip,$todofuken,$sikugun,$jyusyo,$touroku_date,$touroku_time);

	//�¹�
	$ret = exec_sql($sql,$param,$db_char,$web_char,$conn);

	//��Ͽ��λ�����Τǥ���ɤ��Ƥ���

	//Getʸ�����ղ�
	$params = array(
		'mode' => 'view',
		'y' => $y,
		'x' => $x,
		'zoom' => $zoom
	);
	//URL����
	header("Location:./index.php?" . http_build_query($params));
	exit;
	
}

//ʮ�Ф�����
//����������������

	//SQL
	$sql = "";
	$sql = " select * from googlemap order by NO ";

	//�ѥ�᥿
	$param = array();

	//�¹�
	$ret = exec_sql($sql,$param,$db_char,$web_char,$conn);

	$fukidasi = "";
	$view_list = "";
	$view_list_java = "";
	while ( $row = $ret->fetchRow() ) {
		//�����ȥ����
		$title = clean(conv($row->text,$web_char),$web_char);
		//�����ȼ���
		$comment = clean(conv($row->comment,$web_char),$web_char);
		//�ǡ���NO
		$row_no = clean(conv($row->no,$web_char),$web_char);
		//��ɸ
		$fx = clean(conv($row->x,$web_char),$web_char);
		$fy = clean(conv($row->y,$web_char),$web_char);
		//��Ͽ��
		$touroku_id = clean(conv($row->touroku_id,$web_char),$web_char);

		//��Ͽ��
		$touroku_date = clean(conv($row->touroku_date,$web_char),$web_char);
		$touroku_date = substr($touroku_date,0,4) . "/" . substr($touroku_date,4,2) . "/" . substr($touroku_date,6,2);

		//�����ȥ��ˬ�������ɲ�
		$title = $title . "(" . cnt_houmon($row_no,$conn) . ")";
		
		//�ޡ������ؤΥ����ץ�󥯺���
		$view_list .= "<a href=# onclick=\"go_to_maker(" . $row_no . ")\">";
		$view_list .= $title;
		$view_list .= "</a><BR>\n";

		//JavaScript�ؿ� go_to_maker����
		$view_list_java .= "        case " . $row_no . ":\n";
		$view_list_java .= "          point = new GLatLng(" . $fy . "," . $fx . ");\n";
		$view_list_java .= "          break;\n";
		
		//�ޡ�������ʸ�����������
		$view_text  = "<B>" . addslashes($title) . "</B>";//JavaScript�ʤΤ�addslashes���̤�
		$view_text .= "<HR width=200><BR>";
		$view_text .= cr_to_br(addslashes($comment));//���Ԥ�BR���ִ�����
		$view_text .= "<BR><BR>";
		
		//�����󤷤Ƥ������ˬ�䵭Ͽ���ɲä���Ĥ���
		If (chk_value($s_touroku_id) == TRUE){
			//��������
			
			//Getʸ������
			$params = array(
				'mode' => 'ins',
				'no' => $row_no
			);
			
			$view_text .= "<a href=\"rec_comment.php?" . http_build_query($params) . "\">";
			$view_text .= "ˬ�䵭Ͽ��Ĥ���";
			$view_text .= "</a><br>";
		}
		//ˬ������¸�ߤ�����
		If (cnt_houmon($row_no,$conn)>0){
		//ˬ�䵭Ͽ�򸫤�
		
			//Getʸ������
			$params = array(
				'no' => $row_no
			);
		
			$view_text .= "<a href=\"view_houmon.php?" . http_build_query($params) . "\">";
			$view_text .= "ˬ�䵭Ͽ�򸫤�";
			$view_text .= "</a><br>";
		}
		
		//�ޡ������κ���ϥ����󤷤Ƥ��ʤ��ȶػ�
		//���ġ���Ͽ�ԤǤʤ���Фʤ�ʤ�
		If ($s_touroku_id == $touroku_id){
		
			//Getʸ������
			$params = array(
				'mode' => 'del',
				'no' => $row_no
			);
		
			$view_text .= "<a href=\"index.php?" . http_build_query($params) . "\" ";
			$view_text .= "onclick=\"return confirm(\\'�����˺�����ޤ�����\\')\"";
			$view_text .= ">";
			$view_text .= "���Υޡ�������������";
			$view_text .= "</a><BR><BR>";
		}
		$view_text .= "(".$touroku_id.")<BR>(" . $touroku_date .")";
		
		//�ޡ������κ�ɸ���դʤΤ����
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

    <title>���ߥޥåץ����ƥ�</title>
    <script src="http://maps.google.com/maps?file=api&v=1&key=<?=$gmap_key?>"
        type="text/javascript" charset="utf-8"></script>

<script type="text/javascript"> 
<!-- 

function check(){
//ɬ�����ϥ����å�
	var flag = 0;
	//����̾
	if(document.form1.text.value == ""){
		flag = 1;
	}

	//���顼
	if(flag){
		window.alert('ɬ�ܹ��ܤ�̤���Ϥ�����ޤ���');
		return false; // ���������
	}
	else{
		return true; // ������¹�
	}
}

// -->
</script>

  </head>
  <body >

<p>
���ߥޥåץ����ƥ�
<table border="1" height=480>
<tr><td>
<script type="text/javascript" charset="utf-8">
if (GBrowserIsCompatible()) {
	document.write('<div id="map" style="width: 600px; height: 400px"><\/div>');//ɽ������������
} else {
	document.write('���Ȥ��Υ֥饦���Ǥ�Javascript���б����Ƥ��ʤ�����Javascript �����դˤʤäƤ��ޤ���Javascript ��ư���֥饦���Ǥʤ����Ͽޤ�ɽ���Ǥ��ޤ���');
}
</script>
<noscript>���Ȥ��Υ֥饦���Ǥ�Javascript���б����Ƥ��ʤ�����Javascript �����դˤʤäƤ��ޤ���Javascript ��ư���֥饦���Ǥʤ����Ͽޤ�ɽ���Ǥ��ޤ���</noscript>
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
	//���ɽ����ɸ����	  
	map.centerAndZoom(new GPoint(<?=$x?>, <?=$y?>), <?=$zoom?>);

	//ʮ�Ф�����
	//����å������Ȥ���ʮ�Ф���Ф��ؿ�
      function createMarker(point,html) {
        var marker = new GMarker(point);
        GEvent.addListener(marker, "click", function() {
          marker.openInfoWindowHtml(html);
        });
        return marker;
      }

		<?=$fukidasi?>
    } else {
		alert("���Ȥ��Υ֥饦���Ǥ��Ͽޤ�ɽ�����뤳�Ȥ��Ǥ��ޤ���Internet Explorer 6.0�ʹߤ���Firefox 1.0�ʹߤ򤪤����ᤷ�ޤ���");
	}
//�����।�٥�Ȥ��������
    GEvent.addListener(map, 'zoomend',
                       function(oldZoomLevel, newZoomLevel) {
    // document.getElementById("zoom_old").innerHTML = oldZoomLevel;
    // document.getElementById("zoom_new").innerHTML = newZoomLevel;
	//form1��hidden���Ǥ˥��å�
	document.form1.zoom.value=newZoomLevel;
    });
	<?php
		//������ͭ���ʤ�ɽ��
		If (isset($_SESSION["SHISETSU"]["ID"])){
	?>

//����å����٥��
    GEvent.addListener(map, 'click', function(overlay, point) {
      if (point) {
        document.getElementById("show_x").innerHTML = point.x;
        document.getElementById("show_y").innerHTML = point.y;
		//form1��hidden�����Ǥ��ͤ򥻥å�
		document.form1.x.value=point.x;
		document.form1.y.value=point.y;
      }
    });
	<?}?>
//�濴��ɸ����
	function getCenterLatLng(){
	  var latlng = map.getCenter();
	  var lat = latlng.lat();
	  var lng = latlng.lng();
	  var str = "http://<?=$_SERVER["HTTP_HOST"].$_SERVER["PHP_SELF"]?>?mode=view&y=" + lat + "&x=" + lng + "&zoom=" + document.form1.zoom.value;
	  document.form2.text.value = str;
	}

//�ݥ��󥿰�ư�ؿ�
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
	//������ͭ���ʤ�ɽ��
	If (isset($_SESSION["SHISETSU"]["ID"])){
?>
<p> ���߾����ɲäξ��ϡ��Ͽ޾����������֥륯��å����Ƥ���������</p>

<h3>�������򤷤���ɸ</h3>
    <P id="show_x"></P>
    <P id="show_y"></P>
	<?}?>
<Form action=./index.php method=POST name=form1 onSubmit="return check()">
	<input type=hidden name=zoom>
<?php
	//������ͭ���ʤ�ɽ��
	If (isset($_SESSION["SHISETSU"]["ID"])){
?>
	<input type=hidden name=x>
	<input type=hidden name=y>
	<input type=hidden name=mode value="ins">
	<p>����̾�����Ϥ��Ƥ�������(ɬ��)</p>
	<input type=text name=text <?=set_ime("IME_ON");?> >
	<p>�����Ȥ����Ϥ��Ƥ�������</P>
	<textarea name="comment" cols=40 rows=4 <?=set_ime("IME_ON");?> ></textarea>
	<input type=submit value="����" name=submit>
<?php
	}
?>
</form>
<a href="#" onclick="getCenterLatLng();return false;">�����ϿޤΥ��ɥ쥹��ɽ������</a>
<Form name=form2>
	<input type=text name=text size=150>
</Form>
  </body>

</html>
