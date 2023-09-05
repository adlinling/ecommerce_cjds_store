<?php
$inputJSON = file_get_contents('php://input');
$obj = json_decode( $inputJSON ); //see json.php and products.php for more insight on how to handle the array you get form json_decode.
//$json = json_encode($obj, JSON_PRETTY_PRINT);

$itemtochange = $obj->item;


//echo "Item to change: $itemtochange<br><br>";

list($itm, $change) = explode("#", $itemtochange);


$host = $_SERVER['SERVER_NAME'];


$whatsincart = isset($_COOKIE['cart'])?$_COOKIE['cart']:"";

$whatsincart = explode("&", $whatsincart);


$newcartcontent = array();

foreach($whatsincart as $cartitem){
	//echo "$cartitem";//vid#price#quantity

	if(preg_match("/$itm/", $cartitem)){
		//echo "*<br>";
		list($vid, $price, $quantity) = explode("#", $cartitem);

		//echo "Old quantity: $quantity<br>";

		if($change == "more"){
			$quantity++;
		}


		if($change == "less"){
			$quantity--;
		}

		//echo "New quantity: $quantity<br>";

		if($quantity < 1){
			$changeditem = $vid."#".$price."#1";
		}else{
			$changeditem = $vid."#".$price."#".$quantity;
		}

		$newcartcontent[] = $changeditem;

	}else{
		//echo "<br>";
		$newcartcontent[] = $cartitem;
	}
}





$cart_update = implode("&", $newcartcontent);

//echo "New cart content: $cart_update<br>";

setcookie("cart", $cart_update, time()+3600*24*7, "/", $host);

header("Location: updatecart.php");


?>