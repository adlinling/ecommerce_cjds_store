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

?>

<!DOCTYPE HTML>
<head>
<title>
CJ Dev
</title>
<link href='style.css' rel='stylesheet'>
</head>

<body>
<div id="header" style="margin: 0 auto;max-width:1200px;height:100px;border 1px white solid;background-color:#474747;">


<div id="logo" style="float:left;width:250px;height:60px;background-color:#565656;text-align:center;padding-top:40px;color:#ffffff;font-family:Arial;font-size:1.8em;">
CJ Dev
</div>




</div>

<?php
include "navigation.php";
?>

<div id="body" style="margin: 0 auto;max-width:1160px;border 1px white solid;background-color:#454545;padding:20px;">

<?php

$page = isset($_GET['pg'])?$_GET['pg']:NULL;


if(isset($_COOKIE['session_id']) || isset($_SESSION['session_id'])){

	if($page == "account" && (isset($_SESSION['username']) || isset($_COOKIE['username']) || isset($_SESSION['session_id']))){
		include "account.php";
	}else
	if($page == "track"){
		include "track.php";
	}else
	if($page == "invoice"){
		include "invoice.php";
	}else
	if($page == "purchases"){
		include "purchases.php";
	}else
	if($page == "contact"){
		include "contact.php";
	}else
	if($page == "store"){
		include "store.php";
	}else
	if($page == "addtocart"){
		include "addtocart.php";
	}else
	if($page == "viewcart"){
		include "viewcart.php";
	}else
	if($page == "emptycart"){
		include "emptycart.php";
	}else
	if($page == "deleteitems"){
		include "deleteitems.php";
	}else
	if($page == "checkout"){
		include "checkout.php";
	}else
	if($page == "purchased"){
		include "purchased.php";
	}else
	if($page == "storetest"){
		include "storetest.php";
	}else
	if($page == "tos"){
		include "tos.php";
	}else
	if($page == "privacy"){
		include "privacy.php";
	}else
	if($page == "refund"){
		include "refund.php";
	}else
	if($page == "updateemail"){
		include "updateemail.php";
	}else
	if($page == "settings" && (isset($_SESSION['username']) || isset($_COOKIE['username']))){
	//if($page == "settings"){
		include "settings.php";
	}else
	if($page == "logout"){
		include "logout.php";
	}else
	if($page == "ordercompleted"){
		include "ordercompleted.php";
	}else{
		include "loggedin.php";
	}

}else{
	if($page == "login"){
		include "login.php";
	}else
	if($page == "activation"){
		include "activation.php";
	}else
	if($page == "links"){
		include "links.php";
	}else
	if($page == "resendactivation"){
		include "resendactivation.php";
	}else
	if($page == "updateemail"){
		include "updateemail.php";
	}else
	if($page == "contact"){
		include "contact.php";
	}else
	if($page == "store"){
		include "store.php";
	}else
	if($page == "addtocart"){
		include "addtocart.php";
	}else
	if($page == "viewcart"){
		include "viewcart.php";
	}else
	if($page == "emptycart"){
		include "emptycart.php";
	}else
	if($page == "deleteitems"){
		include "deleteitems.php";
	}else
	if($page == "checkout"){
		include "checkout.php";
	}else
	if($page == "purchased"){
		include "purchased.php";
	}else
	if($page == "storetest"){
		include "storetest.php";
	}else
	if($page == "lstdw"){
		include "lastdrawscript.php";
	}else
	if($page == "tos"){
		include "tos.php";
	}else
	if($page == "privacy"){
		include "privacy.php";
	}else
	if($page == "refund"){
		include "refund.php";
	}else
	if($page == "tutorials"){
		include "tutorials.php";
	}else
	if($page == "register"){
		include "register.php";
	}else
	if($page == "activation"){
		include "activation.php";
	}else
	if($page == "newpw"){
		include "newpw.php";
	}else
	if($page == "resetpw"){
		include "resetpw.php";
	}else
	if(preg_match("/vfs[0-9]+/", $page, $pgmatch)){
		include "vidsforsale.php";
	}else{
		include "loggedout.php";
	}
}

?>


</div>

<div id="footer" style="margin: 0 auto;max-width:1200px;height:24px;border 1px white solid;background-color:#787878;text-align:center;padding-top:10px;font-family:Arial;font-size:0.9em;">
Copyright &copy; 2023 CJ All Rights Reserved

<br><br>

<a href="?pg=tos">Terms of Service</a> | <a href="?pg=refund">Refund Policy</a> | <a href="?pg=privacy">Privacy Policy</a>

<br><br>

</div>



</body>
</HTML>