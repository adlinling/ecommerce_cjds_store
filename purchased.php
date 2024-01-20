<div class="purchasedinfo">

<?php
//Revisions:  Added gtag purchase event that sends the sales amount and items purchased to Google Analytics.

$host = $_SERVER['SERVER_NAME'];
setcookie("cart", "", time()-3600*24*7, "/", $host);
setcookie("prices", "", time()-3600*24*7, "/", $host);



//After the purchase, create an account using the email and shipping address from the webhook and 
//send message to the email saying an account has been created with a randomly 
//generated password to log in to track order
//The user could also create an account before purchase by clicking on Register link.

//$email = isset($_GET['email'])?$_GET['email']:"";
//echo "Email:  $email<br>";



//from http://www.hackingwithphp.com/4/11/0/pausing-script-execution, sleep(5); doesn't seem to work on byethost, so have to use this solution
$now = time();
while ($now + 5 > time()) {
    // do nothing
}




$referencecode = isset($_GET['refid'])?$_GET['refid']:"";

echo "Thank you for your order.  The order reference number is $referencecode. A confirmation has been sent to the email address you provided.<br><br>";

	include "dbconnect_prepstmt.php";


	$query = "SELECT account, json FROM cjorders WHERE refnum=?";

	$stmt = $conn->prepare($query); 
	$stmt->bind_param("s", $referencecode);
	$stmt->execute();

	$result = $stmt->get_result();

	$data = $result->fetch_all(MYSQLI_ASSOC);

	$affectedRows = $stmt->affected_rows;

	//echo "Number of rows where refnum=$referencecode: $affectedRows<br>";

	$stmt->close();



	if($affectedRows == 0){

		//echo "No order with given reference code found.<br>";

	}else{
		//echo "Reference code found in orders table.  Getting the account number.<br>";
		$account = $data[0]['account'];
		$jsonofpurchase = $data[0]['json'];
	}


	//echo "$jsonofpurchase<br>";

	$query = "SELECT activated FROM cjusers	WHERE regdate=?";

	$stmt = $conn->prepare($query); 
	$stmt->bind_param("s", $account);
	$stmt->execute();

	$result = $stmt->get_result();

	$data = $result->fetch_all(MYSQLI_ASSOC);

	$affectedRows = $stmt->affected_rows;

	//echo "Affected rows: $affectedRows<br>";

	$stmt->close();
	$conn->close();

	if($affectedRows == 0){
		//Since a row with the account number has already been added to the table by paid.php in checkout.php, this line will never run unless it is new buyer
		//echo "No account with given account number found.<br>";

	}else{
		
		$activated = $data[0]['activated'];

		if($activated == "1"){
				echo "You can check the status of your order by going to Account&#8594;Purchases.<br>";
		}else{
				echo "A new account has been created.  An activation link and password has been emailed to you.<br>";
		}

		echo "<br>If you have any issues with your order, you can get in touch with us by using the <a style='color:#000000;' href='?pg=contact'><b>contact form</b></a>.";

	}


//for($i=0;$i<32;$i++){
//	echo "<br/>";
//}

?>

</div><!--class="purchasedinfo"-->


<div class="alsolike">


<font style="font-family: BebasNeue,San-serif,Verdana,Arial;font-size:1.9em;">YOU MAY ALSO LIKE:</font>


<?php

include "productslist.php";
include "functions.php";


$thisfile = $_SERVER['PHP_SELF'];

$find = array("singquote", "dubquote");
$replace = array("&#039;", "&quot;");


$orderinfo = json_decode($jsonofpurchase, true);

//echo "Order Info:<pre>";
//print_r($orderinfo);
//echo "</pre>";

$paymentprocessor = "paypal";

if(isset($orderinfo['data'])){//Stripe

	$paymentprocessor = "stripe";
	$itemsbought = $orderinfo['data']['object']['metadata']['purchases'];
	$items = explode("&", $itemsbought);

}


//echo "<pre>";
//print_r($items);
//echo "</pre>";

$vids = array();

if($paymentprocessor == "stripe"){

	foreach($items as $item){
		list($vid, $quantity, $price) = explode("#", $item);
		$vids[] = $vid;
	}

	//echo "Vids of products purchased<br>";
	//echo "<pre>";
	//print_r($vids);
	//echo "</pre>";

	$skus_purchased = array();

	foreach($vids as $vid){
		$skus_purchased[] = getSKU($vid, $storeproducts);
	}

	$skus_purchased = array_unique($skus_purchased);

	//echo "SKUs of purchased products:";
	//echo "<pre>";
	//print_r($skus_purchased);
	//echo "</pre>";

	$skupurchasedstring = implode(",", $skus_purchased);

}else{
	//Paypal used as payment processor.  Due to its webhook being slow, this script can't get the SKUs of the purchased items from the database.
	//The SKUs are passed from checkout.php redirect after payment completion
	$skupurchasedstring = isset($_GET['skus'])?$_GET['skus']:"";
}

//echo $skupurchasedstring."<br>";

echo "<div class='productgridalsolike'>";

$displayitemscount = 0;


foreach($storeproducts as $sku => $prodjson){
	if(!preg_match("/".$sku."/i", $skupurchasedstring) && $displayitemscount < 4){
		$displayitemscount++;
		$imgurl = $products[$sku]['image'];
		$slug = $productslugs[$sku];
		//$imgurl = "image0.png";
		$title = str_replace($find, $replace, $products[$sku]['title']);
		//echo "<div class='productsgriditemalsolike'><a class='prodlink' href='$thisfile?pg=store&sku=$sku'><div class='prodimgdivalsolike'><img class='productgridimg' src='$imgurl'></div><div class='prodtitlealsolike'>$title</div></a></div>\n";
		echo "<div class='productsgriditemalsolike'><a class='prodlink' href='$thisfile?pg=store&prod=$slug'><div class='prodimgdivalsolike'><img class='productgridimg' src='$imgurl'></div><div class='prodtitlealsolike'>$title</div></a></div>\n";
	}


}

?>
</div><!--class productgridalsolike-->


</div>


<?php

//Start of Google Analytics purchase event
if(preg_match("/GAdata/", $jsonofpurchase)){
	//echo "JSON is self generated data<br>";
	$GAdata = $jsonofpurchase;
}else{
	$purchasearray = json_decode($jsonofpurchase, true);

	//echo "<pre>";
	//print_r($purchasearray);
	//echo "</pre>";
}


$items = "";
$ordervalue = 0;
$tax = 0;
$shipping = 0;
$currency = "";
$referenceid = "";

$GAitemsarray = array();

//Stripe, item prices in the JSON are in USD.  Shipping, tax and $ordervalue are in the buyer's chosen currency.  You have to convert them to USD by dividing by exchange rate
if(isset($purchasearray['data']['object']['metadata']['purchases'])){

	$items = explode("&", $purchasearray['data']['object']['metadata']['purchases']);
	$exchangerate = $purchasearray['data']['object']['metadata']['exchangerate'];


	$currency = $purchasearray['data']['object']['currency'];

	//echo "Currency: $currency<br>";

	$ordervalue = $purchasearray['data']['object']['amount']/100;

	$shipping = $purchasearray['data']['object']['metadata']['shipping'];
	$tax = $purchasearray['data']['object']['metadata']['tax'];

	if(!preg_match("/USD/i", $currency)){
		$ordervalue = $ordervalue/$exchangerate;
		$shipping = $shipping/$exchangerate;
		$tax = $tax/$exchangerate;
	}

	$ordervalue = round($ordervalue, 2);
	$shipping = round($shipping, 2);
	$tax = round($tax, 2);


	$referenceid = $purchasearray['data']['object']['metadata']['reference_id'];


	foreach($items as $itemkey => $item){

		$itempieces = explode("#", $item);

		$vid = $itempieces[0];
		$productname = getVariantName($vid, $storeproducts);
		$quantity = $itempieces[1];

		$price = $itempieces[2];

		$price = round($price, 2);		
		$price = (string) $price;

		$GAitemsarray[] = array(

			"item_id" => $vid,
			"item_name" => substr($productname, 0, 50),
			"affiliation" => "Google Merchandise Store",
			"coupon" => "none",
			"discount" => 0,
			"index" => $itemkey,
			"item_brand" => "Google",
			"item_category" => "Apparel",
			"item_category2" => "Adult",
			"item_category3" => "Shirts",
			"item_category4" => "Crew",
			"item_category5" => "Short sleeve",
			"item_list_id" => "related_products",
			"item_list_name" => "Related Products",
			"item_variant" => $vid,
			"location_id" => "ChIJIQBpAG2ahYAR_6128GcTUEo",
			"price" => $price,
			"quantity" => $quantity	

			);

	}


}

//Paypal
if(isset($purchasearray['resource']['purchase_units'][0]['items'])){
	

	$paypalitemsarr = $purchasearray['resource']['purchase_units'][0]['items'];

	//echo "<pre>";
	//print_r($paypalitemsarr);
	//echo "</pre>";

	$ordervalue = $purchasearray['resource']['purchase_units'][0]['amount']['value'];
	$shipping = $purchasearray['resource']['purchase_units'][0]['amount']['breakdown']['shipping']['value'];
	$tax = $purchasearray['resource']['purchase_units'][0]['amount']['breakdown']['tax_total']['value'];
	$currency = $purchasearray['resource']['purchase_units'][0]['amount']['currency_code'];
	$referenceid = $purchasearray['resource']['purchase_units'][0]['reference_id'];

	foreach($paypalitemsarr as $paypalkey => $paypalitem){

		$vid = $paypalitem['sku'];
		$productname = $paypalitem['name'];
		$quantity = $paypalitem['quantity'];
		$price = $paypalitem['unit_amount']['value'];

		$GAitemsarray[] = array(

			"item_id" => $vid,
			"item_name" => substr($productname, 0, 50),
			"affiliation" => "Google Merchandise Store",
			"coupon" => "none",
			"discount" => 0,
			"index" => $paypalkey,
			"item_brand" => "Google",
			"item_category" => "Apparel",
			"item_category2" => "Adult",
			"item_category3" => "Shirts",
			"item_category4" => "Crew",
			"item_category5" => "Short sleeve",
			"item_list_id" => "related_products",
			"item_list_name" => "Related Products",
			"item_variant" => "green",
			"location_id" => "ChIJIQBpAG2ahYAR_6128GcTUEo",
			"price" => $price,
			"quantity" => $quantity	

			);
	}


}




//Self generated Google Analytics data
if(isset($GAdata)){



	list($label, $items, $subtotal, $currency, $exchangerate, $tax, $shipping) = explode("#", $GAdata);

	$itemsjson = urldecode($items);
	$itemsarray = json_decode($itemsjson);


	//echo "Items $itemsjson<br>";
	//echo "Subtotal $subtotal<br>";
	//echo "Currency $currency<br>";
	//echo "Exchange rate $exchangerate<br>";
	//echo "Tax $tax<br>";
	//echo "shipping $shipping<br>";
	//echo "Length of data string: ".strlen($GAdata)."<br>";

	if(!preg_match("/USD/i", $currency)){
		//echo "Currency not in USD<br>";
		$subtotal = $subtotal/$exchangerate;
		$tax = round($tax/$exchangerate, 2);
		$shipping = round($shipping/$exchangerate, 2);
	}
	
	$ordervalue = $subtotal + $tax + $shipping;

	$referenceid = $referencecode;

	//echo "<pre>";
	//print_r($itemsarray);
	//echo "</pre>";

	foreach($itemsarray as $itemkey => $item){
		
		//$price is already in USD.  No need to divide by $exchangerate
		list($productname, $vid, $quantity, $price) = explode("#", $item);

		$GAitemsarray[] = array(

			"item_id" => $vid,
			"item_name" => substr($productname, 0, 50),
			"affiliation" => "Stripe",
			"coupon" => "none",
			"discount" => 0,
			"index" => $itemkey,
			"item_brand" => "Google",
			"item_category" => "Apparel",
			"item_category2" => "Adult",
			"item_category3" => "Shirts",
			"item_category4" => "Crew",
			"item_category5" => "Short sleeve",
			"item_list_id" => "related_products",
			"item_list_name" => "Related Products",
			"item_variant" => "green",
			"location_id" => "ChIJIQBpAG2ahYAR_6128GcTUEo",
			"price" => $price,
			"quantity" => $quantity	

			);
	}
}



	$ordervalue = round($ordervalue, 2);
	$ordervalue = (string) $ordervalue;//Not converting to a string will result in really long decimal places from $output .= json_encode($GAarray, JSON_PRETTY_PRINT)
	$shipping = (string) $shipping;
	$tax = (string) $tax;

	$currency = "USD";//Want to send all values in USD to Google Analytics

	$GAarray = array(
		"transaction_id" => $referenceid,
		"value" => $ordervalue,
		"tax" => $tax,
		"shipping" => $shipping,
		"currency" => $currency,
		"coupon" => "none",
		"items" => $GAitemsarray
	);



	//echo "<pre>";
	//print_r($GAarray);
	//echo "</pre>";


	$output = "";
	$output = "<script>\n";
	$output .= 'gtag("event", "purchase", ';

	$output .= json_encode($GAarray, JSON_PRETTY_PRINT) . ");\n";
	$output .= "</script>\n";
	echo $output;

//End of Google Analytics purchase event


?>
