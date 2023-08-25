<div style="margin: 0 auto;padding:20px;text-align:left;background-color:#454545;background-image: url();">

<div style='text-align:center;background-color:#353535;padding:20px;'>
	<h1>Orders</h1>
</div>

<?php
/*
Revisions:  No longer storing order information in .htm files.  Storing them in cjorders table instead. $contents is now assigned with what's from the table.
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
 		<div class="order-item">Quantity</div>
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
				$quantity = $item['quantity'];
				$itemname = $item['name'];
			}else{

				$breakitem = explode("#", $item);				
				$price = $breakitem[2];
				$quantity = $breakitem[1];
				$itemname = getVariantName($breakitem[0], $storeproducts);//function stored in functions.php
			}

			$itemnameshort = substr($itemname, 0, 40);

			foreach($products as $prodkey => $product){
				$productname = $product['title'];

				if(preg_match("/$itemnameshort/i", $productname)){
					//echo "$prodkey $productname<br>";
					$sku = $prodkey;
				}
			}


			$itemtotal = $price*$quantity;

			$subtotal = $subtotal + $itemtotal;

			echo "<div class='order-item'><a href='?pg=store&sku=$sku'><img src='' width='100' height='100'></a></div>";
			echo "<div class='order-item'><a href='?pg=store&sku=$sku'>$itemname</a></div>";
			echo "<div class='order-item' id='purchquantity'>".$quantity."</div>";
			echo "<div class='order-item' id='purchprice'>$".$price."</div>";
			echo "<div class='order-item' id='purchtotal'>\$".$itemtotal."</div>";
			//echo $item['sku']."<br><br>";
		}

		//echo "<br><br>";
		//echo "<pre>";
		//print_r($orderinfo);
		//echo "</pre>";


			echo "<div class='order-item' id='trackorder'><a href='?pg=track&ordernum=$ordernumber'>Track Order</a></div>";
			echo "<div class='order-item'></div>";
			echo "<div class='order-item'></div>";
			echo "<div class='order-item'>Subtotal</div>";
			echo "<div class='order-item'>\$$subtotal</div>";

			echo "<div class='order-item' id='invoice'><a href='invoice.php?refnum=$refnum&ordernum=$ordernumber'>Invoice</a></div>";
			echo "<div class='order-item'></div>";
			echo "<div class='order-item'></div>";
			echo "<div class='order-item'>Shipping</div>";
			echo "<div class='order-item'>\$$shipping</div>";

			$grandtotal = $subtotal + $shipping;

			echo "<div class='order-item'></div>";
			echo "<div class='order-item'></div>";
			echo "<div class='order-item'></div>";
			echo "<div class='order-item'>Grand Total</div>";
			echo "<div class='order-item'>\$$grandtotal</div>";

		echo "</div>";

		echo "<br><br><br>";


		}
}else{
	echo "No orders found.";


	for($i=0;$i<30;$i++){
		echo "<br/>";
	}

}




?>

</div>

