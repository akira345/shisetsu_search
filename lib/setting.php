<?php
//���߸��������ƥࡡ����ե�����


//DB��³���
$dsn = "mysql://test_user:test@localhost/test_db";//�ޥå��ѡ�ʸ�������ɤ�UTF-8����

//mb_convert_encoding�����ǻ��ꤹ��
//DB�����Ф���ʸ��������
$db_char = "UTF-8";

//PC�ڡ���ʸ��������
$web_char = "EUC-JP";

//���ӥڡ���ʸ��������
$keitai_char = "SJIS";

/////////�ѹ��Բ�///////////////////////////////////
////���ӥ��å����ID�Ѷ����ѿ�
$sid = clean(conv(SID,$keitai_char),$keitai_char);
////////////////////////////////////////////////////

//GoogleMapsAPIKey���å�
$gmap_key = "�ȤäƤ�������";

//GeoForm APIKey���å�
//http://lab.cirius.co.jp/���API�Υ�����������뤳��
$keitai_gps_key = '�ȤäƤ�������';

//GPS���������Τ�ɤ���URL
$keitai_ret_url = 'http://exsample.com/shisetsu/mb/ktouroku.php?' . $sid ;


?>