<?php
/*
Revisions:  Change homepage layout.  Moved 'main' div into if($page){echo "<div class='main'>";}
*/

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
<title>
Ecommerce Store
<?php

if(!$page){
  $pagename = "";
}else
if($page == "viewcart"){
 $pagename = " - Cart";
}else{
  $pagename = " - ".ucfirst($page);
}

echo $pagename;

?>
</title>


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
include "navigation.php";
?>



<?php


if($page){
	echo "<div class='main'>";
}


if(isset($_COOKIE['session_id']) || isset($_SESSION['session_id'])){

	if($page == "account" && (isset($_SESSION['username']) || isset($_COOKIE['username']) || isset($_SESSION['session_id']))){
		include "account.php";
	}else
	if($page == "track"){
		include "track.php";
	}else
	if($page == "maillist"){
		include "maillist.php";
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
	if($page == "review"){
		include "review.php";
	}else
	if($page == "search"){
		include "search.php";
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
	if($page == "storemanager" && (isset($_SESSION['username']) || isset($_COOKIE['username']))){
		//echo $_COOKIE['username']."<br>";
		include "storemanager.php";
	}else
	if($page == "addproduct" && (isset($_SESSION['username']) || isset($_COOKIE['username']))){
		include "addproduct.php";
	}else
	if($page == "editproduct" && (isset($_SESSION['username']) || isset($_COOKIE['username']))){
		include "editproduct.php";
	}else
	if($page == "deleteprod" && (isset($_SESSION['username']) || isset($_COOKIE['username']))){
		include "deleteproduct.php";
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
	if($page == "maillist"){
		include "maillist.php";
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
	if($page == "search"){
		include "search.php";
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
	}else{
		include "loggedout.php";
	}
}


if($page){
	echo "</div><!-- main-->";
}

?>




<div id="footer">

<?php

if($page != "maillist" && $page != "checkout"){
	include "subscribe.php";
}
?>

Copyright &copy; 2023 Superlative Consumer Goods

<br><br>

<a href="?pg=tos">Terms of Service</a> | <a href="?pg=refund">Refund Policy</a> | <a href="?pg=privacy">Privacy Policy</a>


</div><!-- footer-->


</div><!-- page-->

</body>
</html>