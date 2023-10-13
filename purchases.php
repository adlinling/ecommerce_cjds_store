<div style="margin: 0 auto;padding:20px;text-align:left;background-color:#ffffff;background-image: url();">

<div style="text-align:center;padding:10px;background-color:#ffffff">
<font style="font-family:BebasNeue,San-serif,Verdana,Arial;color:#000000;font-size:1.9em;">Orders</font>
</div>

<?php
/*
Revisions:  Changed to a new way to find the value for $prodsku
*/


	if(isset($_COOKIE['session_id'])){
		//echo "Cookie:  ".$_COOKIE['session_id']."<br/>";
		$sessionid = $_COOKIE['session_id'];

	}else
	if(isset($_SESSION['session_id'])){
		//echo "Session:  $session_id";
		$sessionid = $_SESSION['session_id'];
	}



	include "productslist.php";
	include "functions.php";


	include "dbconnect_prepstmt.php";

	//regdate is the account number
	$query = "SELECT regdate FROM cjusers WHERE sessionid=?";

	$stmt = $conn->prepare($query); 
	$stmt->bind_param("s", $sessionid);
	$stmt->execute();

	$result = $stmt->get_result();

	$data = $result->fetch_all(MYSQLI_ASSOC);


	//$affectedRows = $stmt->affected_rows;

	//echo "Affected rows: $affectedRows<br>";

	$stmt->close();
	//$conn->close();

	$account= $data[0]['regdate'];





//echo "Account number: $account<br>";


//$query = "SELECT refnum, ordernum, recipient, ship_addr FROM cjorders WHERE account='$account'"; //This will get incomplete orders where the webhook from payment processor has not been received

	$query = "SELECT refnum, ordernum, recipient, ship_addr, json FROM cjorders WHERE account=? AND ordernum<>''";//<>'' means NOT emtpy

	$stmt = $conn->prepare($query); 
	$stmt->bind_param("s", $account);
	$stmt->execute();

	$result = $stmt->get_result();

	$data = $result->fetch_all(MYSQLI_ASSOC);


	//$affectedRows = $stmt->affected_rows;

	//echo "Affected rows: $affectedRows<br>";

	$stmt->close();
	$conn->close();


	//echo "<pre>";
	//print_r($data);
	//echo "</pre>";


	$refnums = array();
	$ordernums = array();
	$recipients = array();
	$shipaddresses = array();
	$jsons = array();

	foreach($data as $key => $dataarray){
		$refnums[] = $dataarray['refnum'];
		$ordernums[] = $dataarray['ordernum'];
		$recipients[] = $dataarray['recipient'];
		$shipaddresses[] = $dataarray['ship_addr'];
		$jsons[] = $dataarray['json'];
	}


echo "<br><br>";



if(count($refnums)){

	foreach($refnums as $refkey => $refnum){


		$subtotal = 0;
		$grandtotal = 0;




		//echo "$refkey<br>";


		//$reference_id = time() from checkout.php
		//this value is passed to the paypal webhook as $orderinfo['resource']['purchase_units'][0]['reference_id']

		//No longer storing order information in .htm files.  Storing them in cjorders table instead
		//$dir = "orders/";
		//$file = $dir."order_".$refnum.".htm";
		//$contents = file_get_contents($file);
		//$contents = strip_tags($contents);

		$contents = $jsons[$refkey];

		//echo "$contents";

		$orderinfo = json_decode($contents, true);//true flag will give you arrays instead of std Objects


		//echo "<pre>";
		//print_r($orderinfo);
		//echo "</pre>";

		//https://www.php.net/manual/en/datetime.format.php
		$orderdate = date("F j, Y", $refnum);


		$recipient = $recipients[$refkey];
		$shippingaddr = $shipaddresses[$refkey];

		$addresspieces = explode(",", $shippingaddr);

		$street = $addresspieces[0];
		$city = $addresspieces[1];
		$state = $addresspieces[2];
		$zip = $addresspieces[3];
		$country = $addresspieces[4];


		echo "<div class='orders-top'>";


		echo "<div class='orders-top-elements'>";
		echo "Order Date:<br><br>";
		echo "Ref#: <br><br>";
		echo "Ship To:";
		echo "</div>";

		echo "<div class='orders-top-elements'>";
		echo "$orderdate<br><br>";

		echo "$refnum<br><br>";
		echo "$recipient<br>";
		echo "$street<br>";
		echo "$city, $state $zip<br>";
		echo $countries[$country];
		echo "</div>";

		echo "<div class='orders-top-elements'>";
		echo "</div>";

		echo "<div class='orders-top-elements'>";
		echo "</div>";


		
		echo "</div>";





		echo "<div class='orders-container' >";

		$ordernumber = $ordernums[$refkey];

		//echo "Order number: ".$ordernumber."<br><br>";



		?>

		<div class="order-item"></div>
 		<div class="order-item"></div>
 		<div class="order-item">Price Ea.</div>
		<div class="order-item">Total</div>
		</div>



		<?php

		$paymethod = "";
		$shipping = "";

		echo "<div class='orders-container' >";

		//stripe vs paypal
		if(isset($orderinfo['resource'])){//Paypal

			$paymethod = "paypal";
			$items = $orderinfo['resource']['purchase_units'][0]['items'];
			$shipping = $orderinfo['resource']['purchase_units'][0]['amount']['breakdown']['shipping']['value'];
		}


		if(isset($orderinfo['data'])){//Stripe
			$paymethod = "stripe";
			//$stripepurchases = $orderinfo->data->object->metadata->purchases;
			$itemsbought = $orderinfo['data']['object']['metadata']['purchases'];
			$shipping = $orderinfo['data']['object']['metadata']['shipping'];
			$items = explode("&", $itemsbought);

		}


		foreach($items as $itemkey => $item){

			if($paymethod == "paypal"){
				$price = $item['unit_amount']['value'];
				$vid = $item['sku'];
				$quantity = $item['quantity'];
				$itemname = $item['name'];
			}else{

				$breakitem = explode("#", $item);				
				$vid = $breakitem[0];
				$quantity = $breakitem[1];
				$price = $breakitem[2];
				$itemname = getVariantName($vid, $storeproducts);//function stored in functions.php
			}



			$variantsarray = array();

			foreach($storeproducts as $sku => $json){
				//echo "$sku<br>$json<br><br>";
				if(preg_match("/".$vid."/", $json)){
					//echo "<b>$sku</b> has this vid!<br>";
					$prodsku = $sku;
					$variantsarray = json_decode($storeproducts[$sku]);
				}
			}


			foreach($variantsarray as $varkey => $variant){
				//echo $variant->vid."<br>";
				if($vid == $variant->vid){
					$img = $variant->variantImage;
				}
			}


			$itemtotal = $price*$quantity;

			$subtotal = $subtotal + $itemtotal;

			echo "<div class='order-item'><a href='?pg=store&sku=$prodsku'><img src='$img' style='height: 100%; width: 100%; object-fit: contain;'></a></div>";
			echo "<div class='order-item'><div class='itemcount'>$quantity</div><div class='checkoutdescr'><a class='prodlink' href='?pg=store&sku=$prodsku'>$itemname</a></div></div>";
			echo "<div class='order-item' id='purchprice'>$".number_format($price, 2)."</div>";
			echo "<div class='order-item' id='purchtotal'>\$".number_format($itemtotal, 2)."</div>";
			//echo $item['sku']."<br><br>";
		}

		//echo "<br><br>";
		//echo "<pre>";
		//print_r($orderinfo);
		//echo "</pre>";


			echo "<div class='order-item' id='trackorder'><a class='trackorder' href='?pg=track&ordernum=$ordernumber'>Track Order</a></div>";
			echo "<div class='order-item'></div>";
			echo "<div class='order-item'>Subtotal</div>";
			echo "<div class='order-item'>\$$subtotal</div>";

			echo "<div class='order-item' id='invoice'><a class='invoice' href='invoice.php?refnum=$refnum&ordernum=$ordernumber'>Invoice</a></div>";
			echo "<div class='order-item'></div>";
			echo "<div class='order-item'>Shipping</div>";
			echo "<div class='order-item'>\$".number_format($shipping, 2)."</div>";

			$grandtotal = $subtotal + $shipping;

			echo "<div class='order-item'></div>";
			echo "<div class='order-item'></div>";
			echo "<div class='order-item'>Grand Total</div>";
			echo "<div class='order-item'>\$".number_format($grandtotal, 2)."</div>";

		echo "</div>";

		echo "<br><br><br><hr>";


		}
}else{
	echo "No orders found.";


	for($i=0;$i<30;$i++){
		echo "<br/>";
	}

}




?>

</div>

