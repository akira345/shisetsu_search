<?php
//�g�єŃ}�[�J�[�o�^

require_once('../lib/common.php');


If (chk_mobile() == 0){
	//PC�ł�(������PC��������֎~�I�j
	header("Location: ../index.php");
	exit;
}

//���O�C���`�F�b�N
chk_login();

//�ȉ��F��OK��̏���

//�����l�ݒ�
$jyusyo = "";
$err_jyusyo = "";
$title = "";
$err_title = "";
$comment = "";

//�g��GPS���W�擾����
// ���[�U�G�[�W�F���g
$agent = $_SERVER['HTTP_USER_AGENT'];

//�\����
$lavel = "���ݒn�擾";

// URL���쐬
$url = 'http://api.cirius.co.jp/v1/geoform/xhtml?';
$url .= 'ua=' . urlencode($agent);
$url .= '&return_uri=' . urlencode($keitai_ret_url);
$url .= '&api_key=' . $keitai_gps_key;
$url .= '&display=' . urlencode($lavel);

//�g��3�Еʂ̃A�h���X�擾�y�[�W�ւ̃����N����
$contents = conv(file_get_contents($url),$keitai_char);



//�X�V����
If (chk_value($_GET["submit"]) == TRUE){
	//�f�[�^Get
	$jyusyo = clean($_GET["jyusyo"],$keitai_char);
	$title = clean($_GET["title"],$keitai_char);
	$comment = clean($_GET["comment"],$keitai_char);
	$x = clean($_GET["x"],$keitai_char);
	$y = clean($_GET["y"],$keitai_char);
		
	//�f�[�^�`�F�b�N
	$flg = 0;
	$err_jyusyo = "";
	$err_title = "";
	$err_msg = "";

	//�^�C�g��
	If (chk_value($title) == FALSE){
		//�^�C�g���������Ă��Ȃ�
		$flg = 1;
		$err_title = "<font color=red>�{�ݖ������Ă�������</font><BR>";
	}
	//�Z���`�F�b�N
	If(chk_value($jyusyo) == FALSE){
		//�Z���������Ă��Ȃ�
		$flg = 1;
		$err_jyusyo = "<font color=red>�Z�������Ă�������</font><BR>";
	}elseIf (chk_value($x) == FALSE || chk_value($y) == FALSE){
		//���W�f�[�^���擾�ł��Ȃ������ꍇ
		//�Z���[�����W�ɕϊ�����
	
		//Giocoding�𗘗p���܂��B���ӁI
		$service_url = 'http://www.geocoding.jp/api/';
		//Get������t��
		$params = array(
			'v' => 1.1,
			'q' =>  $jyusyo
		);
		//URL�\�z
		$service_url .= '?' . http_build_query($params);
		//echo $service_url;
		//Get XML
		$get_item = simplexml_load_file($service_url);
		//var_dump($get_item);
		//exit;
		//�f�[�^������
		$x = "";
		$y = "";
		If (chk_value($get_item) == TRUE){
			//XML��M
			If (chk_value($get_item->error) == TRUE){
				$flg = 1;
				$err_jyusyo = "<font color=red>�Z���f�[�^�ϊ��G���[�ł��B</font><BR>";
			}else{
				//����
				$x = clean(conv((double)$get_item->coordinate->lng,$keitai_char),$keitai_char);
				$y = clean(conv((double)$get_item->coordinate->lat,$keitai_char),$keitai_char); 
				If(chk_value($x) == FALSE || chk_value($y) == FALSE){
					//������₠��
					$flg = 1;
					$err_jyusyo = "<font color=red>�Z���ɕ����̌�₪���݂��܂����B�ڍׂȏZ������͂��Ă��������B</font><BR>";
				}else{
					//OK
				}
			}
		}else{
			$flg = 1;
			$err_msg = "�Z���f�[�^�ϊ��G���[�ł��B���΂炭���҂���������<BR>";
		}
	}
	If ($flg==0){
		//OK
		//�Z�b�V�����֑ޔ�
		$_SESSION["SHISETSU_POSTDATA"]["title"] = $title;
		$_SESSION["SHISETSU_POSTDATA"]["jyusyo"] = $jyusyo;
		$_SESSION["SHISETSU_POSTDATA"]["comment"] = $comment;
		$_SESSION["SHISETSU_POSTDATA"]["x"] = $x;
		$_SESSION["SHISETSU_POSTDATA"]["y"] = $y;
		
		$_SESSION["SHISETSU_POSTDATA"]["status"] = "kakunin";
		//�m�F�y�[�W��Go
		header("Location: ktouroku_kakunin.php?".$sid);
		exit;

	}


}
//�߂��Ă�������
If ($_SESSION["SHISETSU_POSTDATA"]["status"] =="back"){
	//�t�H�[���f�[�^����
	$jyusyo = $_SESSION["SHISETSU_POSTDATA"]["jyusyo"];
	$title = $_SESSION["SHISETSU_POSTDATA"]["title"];
	$comment = $_SESSION["SHISETSU_POSTDATA"]["comment"];
	$x = $_SESSION["SHISETSU_POSTDATA"]["x"];
	$y = $_SESSION["SHISETSU_POSTDATA"]["y"];
	//�X�e�[�^�X�̓N���A
	$_SESSION["SHISETSU_POSTDATA"]["status"]= "";
}

//API���W�擾������
If (chk_value($_GET['lat'] == TRUE) && chk_value($_GET['lon']) == TRUE){
	//���W�f�[�^�����݂���
	//�f�[�^�擾
	//API�̍��W�����R�[�h���s���Ȃ̂ňꉞ�ϊ����Ă���
	$y = (double)clean(conv($_GET["lat"],$keitai_char),$keitai_char);
	$x = (double)clean(conv($_GET["lon"],$keitai_char),$keitai_char);
	$lv = clean(conv($_GET['accuracy'],$keitai_char),$keitai_char);
	$seido = "���Ȃ�A�o�E�g";
	If ($lv ==3){
		$seido = "�덷50m��菬";
	}elseIf ($lv ==2){
		$seido = "�덷300m�ȉ�";
	}elseIf ($lv ==1){
		$seido = "�덷300m����";
	}elseIf ($lv ==0){
		$seido = "���Ȃ�A�o�E�g";
	}
//�擾�������W���Z��������o���B�i����Z���ł����W���قȂ�ꍇ�����邽�߁j
	//�擾�������W��regiocoding���Ė߂�
	
	//ReGiocoding�𗘗p���܂��B���ӁI
	$service_url = 'http://refits.cgk.affrc.go.jp/tsrv/jp/rgeocode.php';
	//Get������t��
	$params = array(
		'v' => 1,
		'lon' => $x,
		'lat' => $y
	);
	//URL�\�z
	$service_url .= '?' . http_build_query($params);
	//echo $service_url;
	//Get XML
	$get_item = simplexml_load_file($service_url);
	//var_dump($get_item);
	
	//�f�[�^������
	$todofuken = "";
	$sikugun = "";
	$jyusyo = "";
	If (chk_value($get_item) == TRUE){
		//XML��M
		If (($get_item->status) == TRUE){
			//����
			//�s���{�����擾
			$todofuken = clean(conv(rtrim($get_item->prefecture->pname),$keitai_char),$keitai_char);
			//�s��S
			$sikugun = clean(conv(rtrim($get_item->municipality->mname),$keitai_char),$keitai_char);
			If (chk_value($get_item->local->section) == TRUE){
				//�Z���i���ݔԒn�܂ł͎����Ȃ��j
				$jyusyo = clean(conv(rtrim($get_item->local->section),$keitai_char),$keitai_char);
			}else{
				$jyusyo = "";
			}
			//�����l�\�z
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
�{�ݓo�^
<HR>
<form action=ktouroku.php method=get>
<input type="hidden" name="<?=session_name()?>" value="<?=clean(conv(session_id(),$keitai_char),$keitai_char)?>">
<P><font color=red><?=$err_msg?></font></P>
<p>�o�^����Z������͂��Ă�������<br>
<input type=text name="jyusyo" value="<?=$jyusyo?>" <?=set_ime("IME_ON")?> size=20><br>
<?If (isset($seido)){echo"�i" . $seido . "�j";}?>
<BR>
<?=$err_jyusyo?>
<br>
<?=$contents?><BR>(!���ӁI�{�ݖ��A�R�����g�������܂��B
<BR>���A�K�������擾�ł���킯�ł͂���܂���B�j
<BR>
<br>
�{�ݖ������<br>
<input type=text size=20 name="title" value="<?=$title?>" <?=set_ime("IME_ON")?> ><br>
<?=$err_title?>
<br>
�R�����g�����<br>
<textarea name="comment" <?=set_ime("IME_ON")?> ><?=$comment?></textarea><br>
<br>
<input type="submit" value="���M" name="submit"></p>
<input type="hidden" value="<?=$x?>" name="x">
<input type="hidden" value="<?=$y?>" name="y">
</form>
</body>
</html>
