<?php
//���߸��������ƥඦ�̥ե�����
//���å���󳫻�
session_start();
//����ե����륤�󥯥롼��
require_once('setting.php');

//DB�ѥ�᥿��
$param = array();


//Pear�饤�֥�ꥤ�󥯥롼��
////Pear MDB2�򥤥󥯥롼��
require_once('MDB2.php');

//	MDB2�Υ��顼ȯ�����ƤӽФ��ؿ��򥻥å�
//errorHandler��Ȥ��褦������
PEAR::setErrorHandling(PEAR_ERROR_CALLBACK, 'chk_exec');


//DB��Ͽ��
$touroku_date = date('Ymd');
$touroku_time = date('His');

//ǰ�Τ��᥻�å�����ĥ��ʤ���
//DoCoMo�����ϥ��å����ID�����ˤ���
If (chk_mobile() <> 1 ){
	session_regenerate_id(TRUE);
}
	//DB��³�����å�
	$conn =& MDB2::connect($dsn);
	if( MDB2::isError($conn) ) {
	    echo "�ǡ����١�������³�Ǥ��ޤ��󡣽�������ߤ��ޤ���";
	//    echo 'Standard Message: ' . $conn->getMessage() . "\n";
	//    echo 'Standard Code: ' . $conn->getCode() . "\n";
	//    echo 'DBMS/User Message: ' . $conn->getUserInfo() . "\n";
	//    echo 'DBMS/Debug Message: ' . $conn->getDebugInfo() . "\n";
	    exit;
	}
	//�ե��å��⡼�ɤ򥪥֥������ȷ���������
	$conn->setFetchMode(MDB2_FETCHMODE_OBJECT);



//�����Ĥ��ζ��̴ؿ����

//SQL�¹Է�̥����å�����
function chk_exec($ret){
	If (PEAR::isError($ret)){
		die($ret->getMessage());
		exit;
	}
}
//ʸ���������Ѵ�����
//�������Ѵ��о�ʸ�����Ѵ���ʸ��������
function conv($str,$par){
	return	mb_convert_encoding($str,$par,"AUTO");
}

//�ѿ�¸�ߥ����å�(�����Բġ�
//������¸�ߥ����å��о�
//���͡��������Ƥ��ʤ���NULL����("")��FALSE����ʳ���TRUE
function chk_value($in_value){
	If (isset($in_value) == TRUE){
		//�ͤ�¸��
		If(empty($in_value) == TRUE && is_numeric($in_value) == FALSE){
			//�ͤ����Ͱʳ��Ƕ��Ǥ���
			return FALSE;
		}
		return TRUE;
	}else{
		return FALSE;
	}
}


//ʸ����򤭤줤�ˤ���
//2008/10/20 XSS�к�
function clean($str,$charset){
//	$tmp = htmlspecialchars($str);
	If (chk_value($charset) == FALSE){
	//ʸ�������ɤ����äƤ��ʤ����ϥ��顼�Ȥ��롣
		//$charset = mb_internal_encoding();
		echo "�������顼";
		exit;
	}
	//ǰ�Τ���ʸ�������ɥ����å�
	If (mb_check_encoding($str,$charset) == FALSE){
		//ʸ�������ɤ���������
		echo "�������顼";
		exit;
	}
	//2���̤��ȡ���������Τǰ��ǥ����ɤ��ƥ��󥳡��ɤ���
	$str = html_entity_decode($str, ENT_QUOTES, $charset);
	
	$tmp = htmlentities($str, ENT_QUOTES, $charset);
	return rtrim($tmp);
}
//����Ƚ��ؿ�
//���͡�����DoCoMo 2:Au 3:SoftBank 0:����¾(PC)
function chk_mobile(){
//��ա��ɲä���ݡ�J-PHONE��Ϣ��AU������Ƚ�ꤹ�뤳�ȡ���
//      (VodaFone�ΰ��������UP.Browser���֤���Τ����뤿��

	//�桼��������������ȼ���
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
		//SoftBank ��ȥ���
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
		//����¾(PC)
		Return 0;
		Exit;
	}
}

function set_ime($par){
	//IME����
	//����ꥢȽ��
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
		//DoCoMo��AU
		switch ($par) {
		    case "IME_ON":
			return "istyle=\"1\"";
		        break;
		    case "IME_OFF":
		        return "istyle=\"3\"";//PC�Ǥˤ��碌�뤿�ᡢȾ�ѱѻ��ˤ��롣
		        break;
			case "NUMBER":
		        return "istyle=\"4\"";//���ӤΤߡ�
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
		        return "mode=\"alphabet\"";//PC�Ǥˤ��碌�뤿�ᡢȾ�ѱѻ��ˤ��롣
		        break;
			case "NUMBER":
		        return "mode=\"numeric\"";//���ӤΤߡ�
		        break;
		    default:
		        break;
		}

	}
}

//���ԥ����ɤ�<BR>�����ˤ���
function cr_to_br($IN_STR){
	$IN_STR = str_replace("\r\n", "<br>", $IN_STR);
	$IN_STR = str_replace("\r", "<br>", $IN_STR);
	$IN_STR = str_replace("\n", "<br>", $IN_STR);
	return $IN_STR;
}

//ˬ�����򸡺�������̤��֤�
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

//����ץ�������˥塼�������
//�������ץ�������̾�����Ǿ��͡������͡�ɽ��No
function value_list($name,$min_no,$max_no,$set_no){
$ret = "<SELECT name=\"" . $name . "\">" . "\r\n";
	for($i=$min_no;$i<=$max_no;$i++){
		If ($i == $set_no){
			//���פ�����
			$ret .= "<OPTION value=\"" . $i ."\" selected>" . $i . "</OPTION>" . "\r\n";
		}else{
			$ret .= "<OPTION value=\"" . $i ."\">" . $i . "</OPTION>" . "\r\n";
		}
	}
$ret .= "</SELECT>" . "\r\n";
return $ret;
}

//������Υ����å�
//�����󤷤Ƥ��ʤ��ȡ�TOP�ض������Ԥ�����
function chk_login(){
	If (chk_value($_SESSION["SHISETSU"]["ID"]) == TRUE){
		//�ʤˤ⤷�ʤ�
	}else{
		//���å����¸�ߤ��ʤ�
		header("Location: index.php");
		exit;
	}
}

//2���֤ε�Υ�򻻽Ф��롣ñ�̤�Km
//http://www2s.biglobe.ne.jp/~satosi/gmap/map_length.html��PHP�˾Ƥ��㤨
//�����㡧echo cal_length(135.73419570922852,34.97195515210841,135.7858657836914,34.96660970979051);
function cal_length($x1,$y1, $x2,$y2){
	$x1 = $x1 * M_PI / 180.0;
	$y1 = $y1 * M_PI / 180.0;
	$x2 = $x2 * M_PI / 180.0;
	$y2 = $y2 * M_PI / 180.0;

	$A = 6378137; // �ϵ����ƻȾ��(6378137m)
	
	$x = $A * ($x2-$x1) * cos( $y1 );
	$y = $A * ($y2-$y1);

	$L = sqrt($x*$x + $y*$y);	// �᡼�ȥ�ñ�̤ε�Υ
	$L = floor($L / 1000);	//Kmñ�̤ˤ���
	return  $L;
}
	

//SQL�¹Դ�Ϣ
function exec_sql($sql,$param,$db_char,$web_char,$conn){
	
	//����С���
	$sql = conv($sql,$db_char);
	mb_convert_variables($db_char,$web_char,$param);

	//�¹�
	$tmp=$conn->prepare($sql);
	$ret=$tmp->execute($param);

	//���
	return $ret;
}



//��ñ�������Ϣ
//������IP�Ǥ�����Ĵ�٤ʤ��Ȥ����ʤ������ʰ��ǤȤ��롣
//
//�ܥ��饹�Υ١�����http://turi2.net/blog/709.html����Ҽڤ��ޤ�����
//
//�ʲ��Τ褦��HTML��ϤäƤ���
//<!-- form���Ǥξ�� -->
//<form method="POST" action="./ktest.php" utn>
//  <input type="submit" value="������" />
//</form>
//
class MobileInformation{

	var $_UserAgent;	//�桼�������������

	function MobileInformation(){
	//���󥹥ȥ饯��
	//�桼������������Ȥ򥻥åȤ������
		$this->_UserAgent = $_SERVER["HTTP_USER_AGENT"];	
	}
	
	
	//���μ����ֹ�μ���
	function IndividualNum(){
		$line = "";
		$edline = 0;
		$agent = $this->_UserAgent;
		$len = strlen($agent);
		$rtn = 0;//�����
		$prob = chk_mobile();//����ꥢȽ��
		//
		switch($prob){
			case '2':
			//AU
				if(chk_value($_SERVER['HTTP_X_UP_SUBNO']) == TRUE){
					//���μ����ֹ椬���äƤ��������
					$rtn = $_SERVER['HTTP_X_UP_SUBNO'];
				}
				break;
			case '1':
			//DoCoMo
				if(strpos($agent, '/ser')){
					//��FOMAü����
					$line = strpos($agent, '/ser') + 4;
				}
				if(strpos($agent, ';icc')){
					//Fomaü����
					$line = strpos($agent, ';icc') + 4;
				}
				//�����������������桼������������Ⱦ��󤬼���ʤΤǾä�
				if(chk_value($line) == TRUE){
					$rtn = substr($agent, $line, $len-($line+1));
				}
				break;
			case '3':
			//SoftBank
				if(strpos($agent, '/SN')){
				//����
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
			//����¾
		}
		return $rtn;
	}
} 

?>