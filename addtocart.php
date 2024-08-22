<?php
/*
Revisions:  Removed gtag Google Analytics add_to_cart event.  It will be executed in products.php instead
*/

include "functions.php";
include "productslist.php";

//session_start();


$host = $_SERVER['SERVER_NAME'];


////echo "<pre>";
print_r($_POST);
//echo "</pre>";

//echo "Host:  $host<br>";
//foreach($_POST as $key => $value){
//	echo "$key $value<br>";
//	$$key = $value;
//}


$sku = $_POST['sku'];
$quantity = $_POST['quantity'];
$variant = $_POST['variant'];

if(isset($_POST['buymoresave'])){
	//echo "Buy more and save<br>";
	$variantsarray = $_POST['buymoresave'];

}else{
	//echo "Add one item<br>";
	$variantsarray = array($variant);
}


$whatsincart = isset($_COOKIE['cart'])?$_COOKIE['cart']:"";

//echo "What is already in cart:<br> $whatsincart<br>";


foreach($variantsarray as $variantstr){

	//echo "$variantstr<br>";

	//Remove the product image URL before inserting into $cart_update.
	$breakvariantstr = explode("#", $variantstr);
	$vid = $breakvariantstr[0];
	$price = $breakvariantstr[1];
	//$prodimg = $breakvariantstr[2];

	$vidNprice = $vid."#".$price;

	if($quantity == "0" || $quantity == ""){
		$quantity = "1";
	}


	if($whatsincart){

		$cartitems = explode("&", $whatsincart);

		//echo "<pre>";
		//print_r($cartitems);
		//echo "</pre>";

		$itemexists = 0;

		$newcontents = array();

		foreach($cartitems as $cartitem){

			//echo "Already in cart: $cartitem<br>";

			if(preg_match("/$vid/", $cartitem)){
				//echo "VID match! Change its quantity<br>";
				$itemexists = 1;
				list($itemvid, $itemprice, $itemquant) = explode("#", $cartitem);
				$newquant = $itemquant + $quantity;
				$newcontents[] = $itemvid."#".$itemprice."#".$newquant;
			}else{
				$newcontents[] = $cartitem;
			}
		}


		if($itemexists){
			$cart_update = implode("&", $newcontents);
		}else{
			$cart_update = $whatsincart."&".$vidNprice."#".$quantity;
		}

	}else{
		//echo "Cart is empty.  Add first item<br>";
		$cart_update = $vidNprice."#".$quantity;
	}


	$whatsincart = $cart_update;
}




//echo "Updated cart: $whatsincart<br>";


//echo "<br><br>Correcting for bundle pricing<br>";
$cartitems = explode("&", $whatsincart);

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

$whatsincart = implode("&", $bundlepriceupdated);


setcookie("cart", $whatsincart, time()+3600*24*7, "/", $host);


//Google Analytics
//The add to cart event is sent in products.php



header("Location: index.php?pg=viewcart");
?>


