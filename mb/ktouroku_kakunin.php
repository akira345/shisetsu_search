<?php
//�{�ݓo�^�i�g�сj

require_once('../lib/common.php');


If (chk_mobile() == 0){
	//PC�ł�(������PC��������֎~�I�j
	header("Location: ../index.php");
	exit;
}

//���O�C���`�F�b�N
chk_login();

//�ȉ��F��OK��̏���


//�Z�b�V�����`�F�b�N
If ($_SESSION["SHISETSU_POSTDATA"]["status"] <> "kakunin"){
	//�s���A�N�Z�X
	header("Location: index.php");
	exit;
}

//�Z�b�V�����ϐ��W�J
	$jyusyo = $_SESSION["SHISETSU_POSTDATA"]["jyusyo"];
	$title = $_SESSION["SHISETSU_POSTDATA"]["title"];
	$comment = $_SESSION["SHISETSU_POSTDATA"]["comment"];
	$x = $_SESSION["SHISETSU_POSTDATA"]["x"];
	$y = $_SESSION["SHISETSU_POSTDATA"]["y"];
//�߂鏈��
If (chk_value($_GET["back"]) == TRUE){
	$_SESSION["SHISETSU_POSTDATA"]["status"] = "back";
	header("Location: ktouroku.php?".$sid);
	exit;
}
//�X�V����
If(chk_value($_GET["submit"]) == TRUE){

	If (chk_value($x) == FALSE || chk_value($y) == FALSE){
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
		
		//�f�[�^������
		$x = "";
		$y = "";
		If (chk_value($get_item) == TRUE){
			//XML��M
			If (($get_item->error) == TRUE){
				echo "�����G���[�����I";
				exit;
			}else{
				//����
				$x = clean(conv((double)($get_item->coordinate->lng),$keitai_char),$keitai_char);
				$y = clean(conv((double)($get_item->coordinate->lat),$keitai_char),$keitai_char); 
				If(chk_value($x) == FALSE || chk_value($y) == FALSE){
				//�������n�����݂����ꍇ�̓G���[
					echo "�����G���[�����I";
					exit;
				}else{
				//OK
				}
			}
		}else{
			echo "�����G���[�����I";
			exit;
		}
	}
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
			echo "A";
			//var_dump($get_item);
			echo conv($get_item->prefecture->pname,$keitai_char);
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
		}else{
			echo "�����G���[";
			exit;
		}
	}else{
		echo "�����G���[";
		exit;
	}
	//�Z�b�V�������o�^��ID�擾
	$s_touroku_id = $_SESSION["SHISETSU"]["USER_NM"];
	//�Z�b�V�������o�^��IP�擾
	$s_ip = $_SESSION["SHISETSU"]["LOGIN_IP"];
	

	//zoom�͓K���ɃZ�b�g
	$zoom = 10;

	//SQL
	$sql = "";
	$sql .= " insert into googlemap (x,y,zoom,text,comment,touroku_id,ip,todofuken,sikugun,jyusyo,touroku_date,touroku_time) ";
	$sql .= " values ";
	$sql .= " (?,?,?,?,?,?,?,?,?,?,?,?)";

	//�p�����^
	$param = array($x,$y,$zoom,$title,$comment,$s_touroku_id,$s_ip,$todofuken,$sikugun,$jyusyo,$touroku_date,$touroku_time);
	
	//���s
	$ret = exec_sql($sql,$param,$db_char,$keitai_char,$conn);

	$_SESSION["SHISETSU_POSTDATA"] = NULL;

print<<<EOD
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=shift_jis">
<title></title>
</head>
<body>
<p>�o�^����</P>
<BR>
<a href=./index.php?{$sid}>TOP�֖߂�</a>
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
<p>�{�ݓo�^(�m�F�j
<hr>
<P>�Z��:<?=$jyusyo?></P>
<P>�{�ݖ��F<?=$title?></p>
<P>�R�����g�F<?=$comment?></P>
<form action=./ktouroku_kakunin.php method=get>
<input type="submit" value="�߂�" name="back">�@<input type="submit" value="�X�V" name="submit">
<input type="hidden" name="<?=session_name()?>" value="<?=clean(conv(session_id(),$keitai_char),$keitai_char)?>">

</form>
</body>
</html>
