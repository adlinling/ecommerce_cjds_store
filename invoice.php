<?php
/*
Revisions:  See revision notes in purchases_10.php
*/
?>

<style>

.invoice-container{
  display:block;
  #background-color:#293829;
  margin:20px;
}


.invoice-top{
  text-align:left;
  #border: 1px #298392 solid;
}


.invoice-title{
  text-align:center;
}



.invoice-shipdate, .payment-title{
  text-align:center;
  border: 2px #392839 solid;
  padding:10px;
}

.invoice-items{
  display:flex;
  #background-color:#583929;
  border-left: 2px #583929 solid;
  border-right: 2px #583929 solid;
  justify-content:space-between;
}


.invoice-items-left{
  text-align:left
  #background-color:#583929;
  padding:10px;
  flex:50;
}


.invoice-items-quantity{
  text-align: center;
  #background-color:#583929;
  padding:10px;
  flex:1;
}




.invoice-items-price{
  text-align:center;
  #background-color:#583929;
  padding:10px;
  flex:2;
}



.invoice-shipaddr{
  text-align:left;
  #background-color:#492839;
  border: 2px #583929 solid;
  margin-bottom:30px;
  padding:10px;
}


.invoice-payment{
  display:flex;
  border-left: 2px #392839 solid;
  border-right: 2px #392839 solid;
  border-bottom: 2px #392839 solid;
  justify-content: space-between;

}


.billing{
  padding:10px;
  flex:4;
}

.payment-totals-labels{
  text-align:right;
  flex:30;
  padding:10px;
}

.payment-totals{
  padding:10px;
  text-align:right;
  flex:1;
}

</style>


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
			$items = $orderinfo['resource']['purchase_units'][0]['items'];


			$recipient = $orderinfo['resource']['purchase_units'][0]['shipping']['name']['full_name'];
			$street = $orderinfo['resource']['purchase_units'][0]['shipping']['address']['address_line_1'];
			$city = $orderinfo['resource']['purchase_units'][0]['shipping']['address']['admin_area_2'];
			$state = $orderinfo['resource']['purchase_units'][0]['shipping']['address']['admin_area_2'];
			$zip = $orderinfo['resource']['purchase_units'][0]['shipping']['address']['postal_code'];
			$country = $orderinfo['resource']['purchase_units'][0]['shipping']['address']['country_code'];

			$custom_id = $orderinfo['resource']['purchase_units'][0]['custom_id'];
			$breakcustom = explode("-", $custom_id);
			$shippingmethod = $breakcustom[0];
		}


		if(isset($orderinfo['data'])){//Stripe

			$payprocessor = "stripe";
			$subtotal = $orderinfo['data']['object']['metadata']['subtotal'];
			$shippingcost = $orderinfo['data']['object']['metadata']['shipping'];
			$grandtotal = $subtotal + $shippingcost;
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


?>

<div class="invoice-container">



<div class="invoice-title">
Ecommerestore.com<br>
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
		//$itemquantity = $item['quantity'];
	}else{
		$breakitem = explode("#", $item);
		$itemprice = $breakitem[2];
	}
	echo "\$$itemprice<br><br>";
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

echo "<b>Shipping Method:</b><br> $shippingmethod<br>";
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
echo "<br>";
echo "<b>Grand Total:</b>";
?>
</div>


<div class="payment-totals">

<?php
echo "\$$subtotal<br>";
echo "\$$shippingcost<br>";
echo "---------<br>";
echo "<b>\$$grandtotal</b>";
?>
</div>

</div>







</div>

