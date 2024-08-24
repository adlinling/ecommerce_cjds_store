<?php

include "functions.php";
include "productslist.php";


$inputJSON = file_get_contents('php://input');
$obj = json_decode( $inputJSON ); //see json.php and products.php for more insight on how to handle the array you get form json_decode.
//$json = json_encode($obj, JSON_PRETTY_PRINT);

$itemtochange = $obj->item;


//echo "Item to change: $itemtochange<br><br>";

//The vid is assigned to $itm
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
		}else
		if($change == "less"){
			$quantity--;
		}else{
			$quantity = $change;
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



//echo "<br><br>Correcting for bundle pricing<br>";
$cartitems = $newcartcontent;

$vid_skus = array();


foreach($cartitems as $cartitem){
	//echo "$cartitem<br>";
	list($vid, $price, $qty) = explode("#", $cartitem);
	$fetchedsku = getSKU($vid, $storeproducts);
	$vid_skus[$vid] = $fetchedsku;
}

//echo "<br><br>Each item's SKU<br>";
//echo "<pre>";
//print_r($vid_skus);
//echo "</pre>";

//$sku_occurences = array_count_values($vid_skus);
$sku_occurences = array();

//echo "<br><br>Count the quantity for each SKU<br>";
foreach($cartitems as $cartitem){
	//echo "$cartitem<br>";
	list($vid, $price, $qty) = explode("#", $cartitem);

	$fetchedsku = getSKU($vid, $storeproducts);

	if(isset($sku_occurences[$fetchedsku])){
		$sku_occurences[$fetchedsku] = $sku_occurences[$fetchedsku] + $qty;
	}else{
		$sku_occurences[$fetchedsku] = $qty;
	}
	
}

//echo "<br><br>Quantity for each SKU<br>";
//echo "<pre>";
//print_r($sku_occurences);
//echo "</pre>";

$pricingarr = array();

//echo "Get pricing based on quantity:<br>";
foreach($sku_occurences as $sku => $qty){
	$pricing = ($products[$sku]['pricing'])?$products[$sku]['pricing']:"regular";

	//echo "Pricing for SKU $sku: $pricing<br>";
	if($pricing == "buymore"){
		$bundleprices = json_decode($products[$sku]['bundleprices'], true);

		//echo "<pre>";
		//print_r($bundleprices);
		//echo "</pre>";

		$maxqtyprice = end($bundleprices);
		
	
		//If the buyer adds more than the max choosable quantity in the bundle to 
		//cart, use the price for the highest quantity.
		//echo "Highest quantity price: $maxqtyprice<br>";
		$priceforsku = isset($bundleprices[$qty])?$bundleprices[$qty]:$maxqtyprice;

		//echo "Price for the $qty items with the sku $sku:$priceforsku<br><br>";
		$pricingarr[$sku] = $priceforsku;

	}
}


//echo "<br><br>Bundle prices by SKU based on quantity:<br>";
//echo "<pre>";
//print_r($pricingarr);
//echo "</pre>";


$bundlepriceupdated = array();

//echo "Updating cart with bundle prices<br>";

foreach($cartitems as $cartitem){
	//echo "$cartitem<br>";
	list($vid, $price, $qty) = explode("#", $cartitem);
	$itmsku = $vid_skus[$vid];
	//echo "SKU for this item is $itmsku<br>";
	$bndlprice = isset($pricingarr[$itmsku])?$pricingarr[$itmsku]:0;

	if($bndlprice){
		$bundlepriceupdated[] = $vid."#".$bndlprice."#".$qty;
	}else{
		$bundlepriceupdated[] = $cartitem;
	}
	
}

$cart_update = implode("&", $bundlepriceupdated);


//echo "New cart content: $cart_update<br>";

setcookie("cart", $cart_update, time()+3600*24*7, "/", $host);

header("Location: updatecart.php");


?>