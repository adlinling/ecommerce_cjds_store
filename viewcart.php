<?php
/* 
Revisions:  Added passing of type of currency to checkout.php
*/


include_once "functions.php";
include_once "productslist.php";


$host = $_SERVER['SERVER_NAME'];

//Create a unique string to be send along with the form data when the form is submitted to the checkout.php
$viewcart_id = random_str(10);

//Store the unique string in a form id cookie.  The Stripe checkout page will compare the value in the cookie with the 
//value from $_SERVER['HTTP_COOKIE'] that is sent along with the form data
setcookie("vcid", $viewcart_id, time()+3600*24*7, "/", $host, FALSE);


?>


<script src="checkout.js?v=0.0.1"></script>

<script>
function qtyinputchanged(vid){

	//You'll notice that each time a value is typed in, the focus is no longer in the text box.  
	//That is because of ajax in this function refreshing the contents of the "viewcart" div with output of updatecart.php
	onlynumbers(vid, 10);

	let quantity = document.getElementById(vid).value;
	//alert(vid + " " + quantity);
	let item = vid+"#"+quantity;

	const obj = new itemincart(item);

	myJSON = JSON.stringify(obj);

	//from https://www.freecodecamp.org/news/javascript-post-request-how-to-send-an-http-post-request-in-js/
	fetch('changequantity.php', {
		method: 'POST',
		headers: {
		'Content-Type': 'application/json'
		},
		body: myJSON
	})
	.then(response => response.text()) // Extract the response text
	.then(result => {
		// Update the HTML element with the response
		document.getElementById("viewcart").innerHTML = result;
	})
	.catch(error => {
		console.error('Error:', error);
	});


	event.preventDefault();


}

</script>


<link href='imgpopup.css' rel='stylesheet'>
<!-- The View Cart Modal -->
<div id="viewcartModal" class="modal">
  <div id="viewcartcaption" style="background-color:rgb(0,0,0,0);text-align:center;color:#ffffff;margin:0 auto;"></div>
</div>



<div style="text-align:center;padding:10px;background-color:#ffffff">
<font style="font-family:BebasNeue,San-serif,Verdana,Arial;color:#000000;font-size:1.9em;">Shopping Cart</font>
<br>
Shipping will be calculated at checkout
</div>

<br/>
<div style='background-color:#ffffff;padding:10px;'>

<?php



$items = isset($_COOKIE['cart'])?explode("&", $_COOKIE['cart']):array();


$currencystr = isset($_SESSION['currency'])?$_SESSION['currency']:NULL;


//Currencies list:  https://stripe.com/docs/currencies
//$currency = "gbp";//cad, gbp, usd, aud, nzd, hkd, jpy

if($currencystr){
	list($currency, $exchangerate) = explode(":", $currencystr);
}else{
	$currency = "USD";
	$exchangerate = "1";
}



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



//if(isset($_COOKIE['cart'])){
	//print_r($_COOKIE['cart']);
//}


$cartitems = array();
$prices = array();

$find = array("singquote", "dubquote");
$replace = array("&#039;", "&quot;");



if(count($items)){



	//echo "<pre>";
	//echo "Cookie<br>";
	//print_r($items);
	//echo "</pre>";



		//Consolidate the cart in case an item is added to cart more than onces
		foreach($items as $itemkey => $item){

			//echo "$item<br/>";

			$breakitem = explode("#", $item);
			//$breakitem[0] is vid
			//$breakitem[1] is price
			//$breakitem[2] is quantity

			if(isset($cartitems[$breakitem[0]])){  //each element in the $cartitems array is a quantity
				$cartitems[$breakitem[0]] = $cartitems[$breakitem[0]] + $breakitem[2];
			}else{
				$cartitems[$breakitem[0]] = $breakitem[2];
			}


			if(!isset($prices[$breakitem[0]])){  //each element in the $cartitems array is a quantity
				$prices[$breakitem[0]] =  $breakitem[1];
			}else{

				if(!($prices[$breakitem[0]] == $breakitem[1])){
					//Price values in the cookies for this vid is not the same
				}
			}


		}


	/*
	echo "<pre>";
	echo "Consolidated items:<br>";
	print_r($cartitems);
	echo "</pre>";

	echo "<pre>";
	echo "Prices:<br>";
	print_r($prices);
	echo "</pre>";
	*/

	$subtotal = 0;

	echo "<form name='myForm' method='POST' action='?pg=checkout' onsubmit='viewcartmodalalert(\"Processing. . . Please wait\");'>";
	//echo "<form name='myForm' method='POST' action='post_origin_check.php'>";
	//echo "<form name='myForm' method='POST' action='http://localhost:4242/checkout.php'>";

	/*
	Use ajax to delete cart items.  A php file will update the cookie and return with a new table of items in cart when X is clicked 
	Just use javascript to remove the divs and then send the chosen item to be removed to updatecart.php to update just the cookie. 
	*/
	?>


	<div class='grid-container' id='viewcart'>

	<div class="grid-item"></div>
 	<div class="grid-item"></div>
 	<div class="grid-item"></div>
 	<div class="grid-item">Qty</div>
 	<div class="grid-item">Price Ea.</div>
 	<div class="grid-item">Total</div>

	<?php

	$cartcontents = array();

	foreach($cartitems as $vid => $quantity){

		$itemtotal = $prices[$vid]*$quantity;

		$subtotal = $subtotal + $itemtotal;

		$variantsarray = array();

		foreach($storeproducts as $sku => $json){
			//echo "$sku<br>$json<br><br>";
			if(preg_match("/".$vid."/", $json)){
				//echo "<b>$sku</b> has this vid!<br>";
				$prodsku = $sku;
				$variantsarray = json_decode($storeproducts[$sku]);
			}
		}

		//echo "<pre>";
		//print_r($variantsarray);
		//echo "</pre>";

		//echo "<pre>";
		//print_r($products);//in productslist.php
		//echo "</pre>";


		foreach($variantsarray as $varkey => $variant){
			//echo $variant->vid."<br>";
			if($vid == $variant->vid){
				$img = $variant->variantImage;
				//echo "variantNameEng ".$variant->variantNameEn."<br>";
				$title = str_replace($find, $replace, $variant->variantNameEn);
				//echo "Title: $title<br>";
				$description = ucwords($title);
				//$descripshort = substr($description, 0, 20);
			}
		}



		?> 
		<div class='grid-item' onclick='deleteitem(event, "", "<?="&".$vid."#".$prices[$vid]."#".$quantity;?>");'><div><a class='removeitem' href="#">X</a></div></div>
		<div class='grid-item'><?="<a href='?pg=store&sku=$prodsku'><img src='$img' alt='$img' style='height: 100%; width: 100%; object-fit: contain'></a>";?></div>
 		<div class='grid-item'><div style='padding:20px;'><?="<a class='prodlink' href='?pg=store&sku=$prodsku'>$description</a>";?></div></div>
 		<div class='grid-item'><div class='more' onclick='changeqty(event, "", "<?=$vid;?>#less");'>&nbsp;-&nbsp;</div><input type='text' id='<?=$vid;?>' class='qty' value='<?=$quantity;?>' oninput='qtyinputchanged("<?=$vid;?>");'><div class='less' onclick='changeqty(event, "", "<?=$vid;?>#more");'>&nbsp;+&nbsp;</div></div>
 		<div class='grid-item'><div><?="$currsymbol".number_format($prices[$vid]*$exchangerate, 2);?></div></div>
 		<div class='grid-item'><?="$currsymbol".number_format($itemtotal*$exchangerate, 2);?></div>
		<?php


		$item = "$description#$vid#$quantity#".$prices[$vid];
		$cartcontents[] = $item;


	}

	?>


	<div class="grid-item"></div>
 	<div class="grid-item"></div>
 	<div class="grid-item"></div>
 	<div class="grid-item"></div>
 	<div class="grid-item">Subtotal (<?=strtoupper($currency);?>)</div>
 	<div class="grid-item" title="Shipping will be calculated at checkout"><?="$currsymbol".number_format($subtotal*$exchangerate, 2);?></div>



	<div class="grid-item"></div>
 	<div class="grid-item"></div>
 	<div class="grid-item"></div>
 	<div class="grid-item"></div>
 	<div class="grid-item">


	
	<?php
		foreach($cartcontents as $cartcontent){

			echo "<input type='hidden' name='items[]' value='$cartcontent'><br>";

		}

	?>

	</div>


 	<div class="grid-item">

	<?php


		echo "<input type='hidden' name='subtotal' value='$subtotal'>";
		echo "<input type='hidden' name='switchtocurrency' value='$currency'>";



 		echo "<br><br><br><br><br><br><input type='submit' name='checkout' value='Checkout'>";
	?>
	</div>


	</form>	
	</div>

<?php
//<a class='viewcart' href='?pg=emptycart'>Empty Cart</a>&nbsp;&nbsp;&nbsp;
?>

<a class='viewcart' href='?pg=store'>Continue Shopping</a>



	<?php

}else{ //if(count($items)){
	echo "Your cart is empty.";
	//echo $_COOKIE['cart'];
}



//$host = $_SERVER['SERVER_NAME'];
//setcookie("cart", "", time()-3600*24*7, "/", $host);

//echo "After emptying: ";
//print_r($_COOKIE['cart']);

for($i=0;$i<10;$i++){
	echo "<br/>";
}

?>


</div>




</div>




</div>
</div>
