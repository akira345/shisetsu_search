<?php
require_once('./lib/common.php');
$pw = clean($_POST["password"],$web_char);
echo md5($pw);
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=euc-jp">
<title></title>
</head>
<body>
<form action=./chk_md5.php method=post>
<p><input type="password" name="password"></p>
<input type=submit>
</form>
</body>
</html>
