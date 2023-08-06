<?php
/* 
Revisions:  This version implements ajax to enable deleting a single item from the cart
*/


include_once "functions.php";
include_once "productslist.php";

?>


<script language="javascript" type="text/javascript">
<!--

//See javascript_post.php on how this works
function itemincart(item){
	this.item = item;
}


function sendFetchpostGR(event, phpreceiver, item){

const obj = new itemincart(item);

myJSON = JSON.stringify(obj);

//from https://www.freecodecamp.org/news/javascript-post-request-how-to-send-an-http-post-request-in-js/
  fetch('deleteitems.php', {
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



//-->
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
 	<div class="grid-item">Quantity</div>
 	<div class="grid-item">Price Ea.</div>

	<?php

	$cartcontents = array();

	foreach($cartitems as $vid => $quantity){


		$subtotal = $subtotal + $prices[$vid]*$quantity;

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



		//$description = $decodedData['data']['variantNameEn'];
		//$img = $decodedData['data']['variantImage'];

		?> 
		<div class='grid-item' onclick='sendFetchpostGR(event, "", "<?php echo "&".$vid."#".$prices[$vid]."#".$quantity;?>");'><a href="#">X</a></div>
		<div class='grid-item'><?php echo "<a href='?pg=store&sku=$prodsku'>$img</a>";?></div>
 		<div class='grid-item'><?php echo "<a href='?pg=store&sku=$prodsku'>$description</a>";?></div>
 		<div class='grid-item'><?php echo $quantity;?></div>
 		<div class='grid-item'><?php echo "\$".$prices[$vid];?></div>
		<?php


		$item = "$description#$vid#$quantity#".$prices[$vid];
		$cartcontents[] = $item;


	}

	?>


	<div class="grid-item"></div>
 	<div class="grid-item"></div>
 	<div class="grid-item"></div>
 	<div class="grid-item">Subtotal</div>
 	<div class="grid-item"><?php echo "\$".$subtotal;?></div>



	<div class="grid-item"></div>
 	<div class="grid-item"><a href='?pg=emptycart'>Empty Cart</a>&nbsp;&nbsp;&nbsp;<a href='?pg=store'>Continue Shopping</a></div>
 	<div class="grid-item"></div>
 	<div class="grid-item">

	<?php
		foreach($cartcontents as $cartcontent){

			echo "<input type='hidden' name='items[]' value='$cartcontent'>";

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
