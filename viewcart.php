<?php

include_once "functions.php";
include_once "productslist.php";


$host = $_SERVER['SERVER_NAME'];


$viewcart_id = random_str(10);
setcookie("vcid", $viewcart_id, time()+3600*24*7, "/", $host, FALSE);


?>



<script>
function qtyinputchanged(vid){


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

<div style="text-align:center;padding:10px;background-color:#282828">
<font style="font-family:San-serif,Verdana,Arial;color:#ffffff;"><h3>Shopping Cart</h3></font>
</div>

<br/>
<div style='background-color:#252525;padding:10px;'>

<?php


/*
if(isset($_COOKIE['cart'])){
	print_r($_COOKIE['cart']);
	echo "<br><br>";
	echo "<a href='?pg=emptycart'>Empty Cart</a> ";
}else{
	echo "Cart is empty";
}
*/




$items = isset($_COOKIE['cart'])?explode("&", $_COOKIE['cart']):array();



//if(isset($_COOKIE['cart'])){
	//print_r($_COOKIE['cart']);
//}


$cartitems = array();
$prices = array();

if(count($items) > 1){



	//echo "<pre>";
	//echo "Cookie<br>";
	//print_r($items);
	//echo "</pre>";



		//Consolidate the cart in case an item is added to cart more than onces
		foreach($items as $itemkey => $item){
			if($itemkey > 0){

				//echo "$item<br/>";

				$breakitem = explode("#", $item);
				//$breakitem[0] is vid
				//$breakitem[1] is price
				//$breakitem[2] is quantity

				if(isset($cartitems[$breakitem[0]])){  //each element in the $cartitems array is a quanitiy
					$cartitems[$breakitem[0]] = $cartitems[$breakitem[0]] + $breakitem[2];
				}else{
					$cartitems[$breakitem[0]] = $breakitem[2];
				}


				if(!isset($prices[$breakitem[0]])){  //each element in the $cartitems array is a quanitiy
					$prices[$breakitem[0]] =  $breakitem[1];
				}else{

					if(!($prices[$breakitem[0]] == $breakitem[1])){
						//Price values in the cookies for this vid is not the same
					}
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

	echo "<form name='myForm' method='POST' action='?pg=checkout'>";
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
				$description = ucwords($variant->variantNameEn);
				$descripshort = substr($description, 0, 30);
			}
		}


		foreach($products as $prodkey => $product){
			$productname = $product['title'];
			//echo "$productname <br>";
			if(preg_match("/$descripshort/i", $productname)){
				//echo "$prodkey $productname<br>";
				$prodsku = $prodkey;
			}
		}


		?> 
		<div class='grid-item' onclick='deleteitem(event, "", "<?="&".$vid."#".$prices[$vid]."#".$quantity;?>");'><div><a href="#">X</a></div></div>
		<div class='grid-item'><?="<a href='?pg=store&sku=$prodsku'><img src='$img' alt='$img' style='height: 100%; width: 100%; object-fit: contain'></a>";?></div>
 		<div class='grid-item'><div style='padding:20px;'><?="<a href='?pg=store&sku=$prodsku'>$description</a>";?></div></div>
 		<div class='grid-item'><div class='more' onclick='changeqty(event, "", "<?=$vid;?>#less");'>&nbsp;-&nbsp;</div><input type='text' id='<?=$vid;?>' class='qty' value='<?=$quantity;?>' oninput='qtyinputchanged("<?=$vid;?>");'><div class='less' onclick='changeqty(event, "", "<?=$vid;?>#more");'>&nbsp;+&nbsp;</div></div>
 		<div class='grid-item'><div><?="\$".$prices[$vid];?></div></div>
 		<div class='grid-item'><?="\$".$itemtotal;?></div>
		<?php


		$item = "$description#$vid#$quantity#".$prices[$vid];
		$cartcontents[] = $item;


	}

	?>


	<div class="grid-item"></div>
 	<div class="grid-item"></div>
 	<div class="grid-item"></div>
 	<div class="grid-item"></div>
 	<div class="grid-item">Subtotal</div>
 	<div class="grid-item"><?="\$".$subtotal;?></div>



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


 		echo "<br><br><input type='submit' name='checkout' value='Checkout'>";
	?>
	</div>


	</form>	
	</div>

<a href='?pg=emptycart'>Empty Cart</a>&nbsp;&nbsp;&nbsp;<a href='?pg=store'>Continue Shopping</a>



	<?php

}else{ //if(count($items) > 1){
	echo "Your cart is empty.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br><br>  <a href='?pg=store'>Go to Store</a>";
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
