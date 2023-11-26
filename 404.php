<?php
session_start();

if(isset($_GET['sessionid'])){
	$_SESSION['session_id'] = $_GET['sessionid'];
	$sessionid = $_GET['sessionid'];
}else{
	$sessionid = session_id();
}



//$thisfile = $_SERVER['PHP_SELF'];//moved to navigation.php
//echo "$thisfile<br/>";
//$currentdir = basename(dirname(__FILE__));

$page = isset($_GET['pg'])?$_GET['pg']:NULL;

?>

<!DOCTYPE HTML>

<html>
<head>
<title>Ecommerce Store</title>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<?php include "stylesheet.php";?>
<link href='searchbox.css?v=0.0.1' rel='stylesheet'>
<link rel="icon" href="favicon.ico" type="image/x-icon">
<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
<?php
if($page == "store"){
	echo "<link href='imgpopup.css' rel='stylesheet'>";
}
?>

</head>



<body>


<div id="page">

<div id="header">


<?php
include "logo.php";
?>

</div>



<?php
include "navigationerrorpg.php";
?>



<?php

//include "homepg.php";
include "notfound.php";

?>




<div id="footer">

<?php
include "subscribe.php";
?>


Copyright &copy; 2023 Ecommerce Store

<br><br>

<a href="?pg=tos">Terms of Service</a> | <a href="?pg=refund">Refund Policy</a> | <a href="?pg=privacy">Privacy Policy</a>


</div><!-- footer-->


</div><!-- page-->

</body>
</html>