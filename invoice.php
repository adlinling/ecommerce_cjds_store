<!DOCTYPE HTML>
<html>
<head>
<title>
Ecommerce Store - Invoice
</title>
<link href='invoice.css' rel='stylesheet'>
</head>

<body>

<?php
/*
Revisions:  Now shows taxes paid
*/
?>



<?php

include "productslist.php";
include "functions.php";


$refnum = isset($_GET['refnum'])?$_GET['refnum']:"";
$ordernum = isset($_GET['ordernum'])?$_GET['ordernum']:"";





include "dbconnect_prepstmt.php";


$sql = "SELECT * FROM cjorders WHERE refnum=?";
$stmt = $conn->prepare($sql); 
$stmt->bind_param("s", $refnum);
$stmt->execute();

$result = $stmt->get_result();


$data = $result->fetch_all(MYSQLI_ASSOC);



$stmt->close();
$conn->close();


//echo "<pre>";
//print_r($data);
//echo "</pre>";

$buyer = $data[0]['buyer'];
$paymethod = $data[0]['paymethod'];
$addr_bill_pieces = explode(",", $data[0]['bill_addr']);
$orderjson = $data[0]['json'];

$street_bill = $addr_bill_pieces[0];
$city_bill = $addr_bill_pieces[1];
$state_bill = $addr_bill_pieces[2];
$zip_bill = $addr_bill_pieces[3];
$country_bill = $addr_bill_pieces[4];




$orderdate = date("F j, Y", $refnum);


//echo "Ship To: $recipient $shipaddr<br>";

		$tax = 0;
		$subtotal = 0;
		$grandtotal = 0;
		

		//$dir = "orders/";
		//$file = $dir."order_".$refnum.".htm";
		//$contents = file_get_contents($file);
		//$contents = strip_tags($contents);

		$contents = $orderjson;

		//echo "$contents";

		$orderinfo = json_decode($contents, true);

		if(isset($orderinfo['resource'])){//Paypal

			$payprocessor = "paypal";
			$grandtotal = $orderinfo['resource']['purchase_units'][0]['amount']['value'];
			$subtotal = $orderinfo['resource']['purchase_units'][0]['amount']['breakdown']['item_total']['value'];
			$shippingcost = $orderinfo['resource']['purchase_units'][0]['amount']['breakdown']['shipping']['value'];
			$tax = $orderinfo['resource']['purchase_units'][0]['amount']['breakdown']['tax_total']['value'];
			$items = $orderinfo['resource']['purchase_units'][0]['items'];
			$currency = $orderinfo['resource']['purchase_units'][0]['amount']['currency_code'];

			$custom_id = $orderinfo['resource']['purchase_units'][0]['custom_id'];

			//echo "Custom ID: $custom_id<br>";

			$recipient = $orderinfo['resource']['purchase_units'][0]['shipping']['name']['full_name'];
			$street = $orderinfo['resource']['purchase_units'][0]['shipping']['address']['address_line_1'];
			$city = $orderinfo['resource']['purchase_units'][0]['shipping']['address']['admin_area_2'];
			$state = $orderinfo['resource']['purchase_units'][0]['shipping']['address']['admin_area_2'];
			$zip = $orderinfo['resource']['purchase_units'][0]['shipping']['address']['postal_code'];
			$country = $orderinfo['resource']['purchase_units'][0]['shipping']['address']['country_code'];

			list($shippingmethod, $phone, $email, $exchangerate) = explode("#", $custom_id);


		}


		if(isset($orderinfo['data'])){//Stripe

			$payprocessor = "stripe";
			$subtotal = $orderinfo['data']['object']['metadata']['subtotal'];
			$tax = $orderinfo['data']['object']['metadata']['tax'];
			$currency = $orderinfo['data']['object']['currency'];
			$exchangerate = $orderinfo['data']['object']['metadata']['exchangerate'];
			$shippingcost = $orderinfo['data']['object']['metadata']['shipping'];
			$grandtotal = $subtotal + $shippingcost + $tax;
			$itemsbought = $orderinfo['data']['object']['metadata']['purchases'];
			$items = explode("&", $itemsbought);

			$diffaddr = $orderinfo['data']['object']['metadata']['diffaddr'];

			if($diffaddr){

				$firstname = $orderinfo['data']['object']['metadata']['firstname'];
				$lastname = $orderinfo['data']['object']['metadata']['lastname'];

				$street = $orderinfo['data']['object']['metadata']['line1']." ".$orderinfo['data']['object']['metadata']['line2'];
				$city = $orderinfo['data']['object']['metadata']['city'];
				$state = $orderinfo['data']['object']['metadata']['state'];
				$zip = $orderinfo['data']['object']['metadata']['zip'];
				$country = $orderinfo['data']['object']['metadata']['country'];
			}else{
				$firstname = $orderinfo['data']['object']['metadata']['firstnameb'];
				$lastname = $orderinfo['data']['object']['metadata']['lastnameb'];

				$street = $orderinfo['data']['object']['metadata']['line1b']." ".$orderinfo['data']['object']['metadata']['line2b'];
				$city = $orderinfo['data']['object']['metadata']['cityb'];
				$state = $orderinfo['data']['object']['metadata']['stateb'];
				$zip = $orderinfo['data']['object']['metadata']['zipb'];
				$country = $orderinfo['data']['object']['metadata']['countryb'];

			}


			$recipient = $firstname." ".$lastname;
			$shippingmethod = $orderinfo['data']['object']['metadata']['shippingmethod'];

		}

		//echo "<pre>";
		//print_r($orderinfo);
		//echo "</pre>";

		if(preg_match("/jpy/i", $currency)){
			$currsymbol = "&yen;";
		}else
		if(preg_match("/inr/i", $currency)){
			$currsymbol = "&#8377;";
		}else
		if(preg_match("/eur/i", $currency)){
			$currsymbol = "&euro;";
		}else
		if(preg_match("/pkr/i", $currency)){
			$currsymbol = "Rs";
		}else
		if(preg_match("/gbp/i", $currency)){
			$currsymbol = "&pound;";
		}else{
			$currsymbol = "&dollar;";
		}



?>

<div class="invoice-container">



<div class="invoice-title">
Ecommerce Store<br>
ecommercestore.com<br><br>
123 Street<br>
Big City, State, 39283<br><br>


Invoice for Order #<?php echo $ordernum;?><br>
Print this page for your records<br><br>
</div>



<div class="invoice-top">
<?php
echo "Order Date:  $orderdate<br>";

//echo "Buyer: $buyer<br>";


?>
</div>


<div class="invoice-shipdate">
<b>Order Details</b>
</div>




<div class="invoice-items">

<div class="invoice-items-left">
<b>Items Ordered:</b><br>

<?php
foreach($items as $itkey => $item){

	if($payprocessor == "paypal"){
		$itemname = $item['name'];
		//$itemprice = $item['unit_amount']['value'];
		//$itemquantity = $item['quantity'];
	}else{

		$breakitem = explode("#", $item);	
		$itemname = getVariantName($breakitem[0], $storeproducts);
		//$itemquantity = $breakitem[2];
	}

	//echo "Item: $itemname<br>";
	echo "<i>$itemname</i><br><br>";

}
?>
</div>


<div class="invoice-items-quantity">
<b>Quantity</b><br>
<?php


foreach($items as $itkey => $item){

	if($payprocessor == "paypal"){
		//$itemname = $item['name'];
		//$itemprice = $item['unit_amount']['value'];
		$itemquantity = $item['quantity'];
	}else{
		$breakitem = explode("#", $item);
		$itemquantity = $breakitem[1];
	}

	echo "$itemquantity<br><br>";
}

?>

</div>




<div class="invoice-items-price">
<b>Price Ea.</b><br>
<?php


foreach($items as $itkey => $item){

	if($payprocessor == "paypal"){
		//$itemname = $item['name'];
		$itemprice = $item['unit_amount']['value'];
		$convertedprice = $itemprice;
		//$itemquantity = $item['quantity'];
	}else{
		$breakitem = explode("#", $item);
		$itemprice = $breakitem[2];
		$convertedprice = $itemprice*$exchangerate;
	}


	if(preg_match("/jpy/i", $currency)){
		$displayprice = round($convertedprice);
	}else{
		$displayprice = number_format($convertedprice, 2);
	}


	echo "$currsymbol".$displayprice."<br><br>";
}

?>

</div>



</div>


<div class="invoice-shipaddr">

<?php
echo "<b>Shipping Address:</b><br>";
echo "$recipient<br>";
echo "$street<br>";
echo "$city, $state $zip<br>";



echo $countries[$country]."<br><br>";

$shippingmethod = explode("&", $shippingmethod);

$shippingname = $shippingmethod[0];
$shippingtime = $shippingmethod[1];

echo "<b>Shipping Method:</b><br> $shippingname<br>$shippingtime days";
echo "<br><br>";

?>
</div>



<div class="payment-title">
<b>Payment Information</b>
</div>

<div class="invoice-payment">


<div class="billing">
<b>Payment Method:</b> <?php echo ucwords($paymethod);?><br><br>
<b>Billing Address:</b><br>
<?php

echo "$buyer<br>";
echo "$street_bill<br>";
echo "$city_bill, $state_bill $zip_bill<br>";
echo $countries[$country_bill]."<br>";

?>
</div>



<div class="payment-totals-labels">

<?php
echo "Subtotal:<br>";
echo "Shipping:<br>";
echo "Tax:<br>";
echo "<br>";
echo "<b>Grand Total (".strtoupper($currency)."):</b>";
?>
</div>


<div class="payment-totals">

<?php

	if(preg_match("/jpy/i", $currency)){
		$shippingcost = round($shippingcost);
		$tax = round($tax);
		$subtotal = round($subtotal);
		$grandtotal = round($grandtotal);
	}else{
		$shippingcost = number_format($shippingcost, 2);
		$tax = number_format($tax, 2);
		$subtotal = number_format($subtotal, 2);
		$grandtotal = number_format($grandtotal, 2);
	}



echo "$currsymbol".$subtotal."<br>";
echo "$currsymbol".$shippingcost."<br>";
echo "$currsymbol".$tax."<br>";
echo "<hr>";
echo "<b>$currsymbol".$grandtotal."</b>";
?>
</div>

</div>







</div>

</body>
</html>