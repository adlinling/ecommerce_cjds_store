<?php 	
//session_start();


$host = $_SERVER['SERVER_NAME'];


//echo "Host:  $host<br>";
foreach($_POST as $key => $value){
	$$key = $value;
}


$whatsincart = isset($_COOKIE['cart'])?$_COOKIE['cart']:"";


$cart_update = $whatsincart."&".$variant."#".$_POST['quantity'];

setcookie("cart", $cart_update, time()+3600*24*7, "/", $host);

header("Location: index.php?pg=viewcart");
?>