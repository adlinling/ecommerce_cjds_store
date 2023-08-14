<script src="checkout.js"></script>

<?php
/* 
Revisions:  Renamed ajaxfunction() to updateProdctInfo() and moved it to checkout.js
*/



include_once "functions.php";
include_once "productslist.php";

$thisfile = $_SERVER['PHP_SELF'];

$sku = isset($_GET['sku'])?$_GET['sku']:NULL;


?>

<div style="margin:0 auto;padding:20px;text-align:left;background-color:#333333;background-image: url();background-position: 0% 20%;background-repeat: no-repeat;">




<div id="homewrapper">




<div style="text-align:center;padding:10px;background-color:#282828;margin:2px;">
<font style="font-family:San-serif,Verdana,Arial;color:#ffffff;"><h3>

<?php
if($sku){
	echo $products[$sku]['title'];
}else{
	echo "Store";
}

?>

</h3></font>
</div>



<?php




if($sku){


	include "productslist.php";


	$productData = json_decode($storeproducts[$sku]);

	//echo "<pre>";
	//print_r($productData);
	//echo "</pre>";

	$firstimg = NULL;
	$firstprice = NULL;
	$profit = 12;
	$items = array();


	if(isset($productData)){
		foreach($productData as $key => $subarray){

			$vid = $subarray->vid;
			$varname = $subarray->variantKey;
			$price = $subarray->variantSellPrice + $profit;
			$img = $subarray->variantImage;
			$variants[$varname] = $vid."#".$price."#".$img;

		}



		ksort($variants);//Short buy array index name.  krsort() - Sorts an array by key in descending order

		foreach($variants as $sizeoption => $vidNpriceNimg){
			if(!isset($firstvid)){
				$breakvidnprice = explode("#", $vidNpriceNimg);
				$firstvid = $breakvidnprice[0];
				//echo "First vid: $firstvid<br>";
			}


			if(!$firstprice){
				$breakvidnprice = explode("#", $vidNpriceNimg);
				$firstprice = $breakvidnprice[1];
				//echo "First price: $firstprice<br>";
			}

			if(!$firstimg){
				$breakvidnprice = explode("#", $vidNpriceNimg);
				$firstimg = $breakvidnprice[2];
				//echo "First img: $firstimg<br>";
			}
		}






		//Code below from https://www.geeksforgeeks.org/how-to-use-curl-to-get-json-data-and-decode-json-data-in-php  with modifications

		// Initializing curl
		$curl = curl_init();


		curl_setopt($curl, CURLOPT_PASSWORD,	"$apikey");

		// Telling curl to store JSON
		// data in a variable instead
		// of dumping on screen
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);


		$headers = [
			'Content-Type: application/json',
			//Get this using cj_gettoken.php
			'CJ-Access-Token: '.$token
		];

		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);


		curl_setopt($curl, CURLOPT_URL,	"https://developers.cjdropshipping.com/api2.0/v1/product/stock/queryByVid?vid=$firstvid");

		// Executing curl
		$response = curl_exec($curl);

		// Checking if any error occurs
		// during request or not
		if($e = curl_error($curl)) {
			echo "Inventory query error<br>";
			echo $e;
		} else {

			// Decoding JSON data
			$decodedData = json_decode($response, true);

			$inventoryData = $decodedData;

		}

	}

	// Closing curl
	curl_close($curl);


	//echo "<pre>";
	//print_r($productData['data']);
	//echo "</pre>";


	//echo "<pre>";
	//print_r($inventoryData['data']);
	//echo "</pre>";


	$inventory = isset($inventoryData['data'][0]['storageNum'])?$inventoryData['data'][0]['storageNum']:0;






	echo "<div class='container'>";



	echo "<div class='one' id='myDiv'>";

	echo "<div style='height:500px;background-color:rgba(40,40,40,1);'>";
	echo "$firstimg<br>";
	echo "</div>";

	echo "Price: \$$firstprice<br>";

	if($inventory > 0){
		echo "In stock<br>";
	}else{
		echo "Out of stock<br>";
	}


	echo "</div>";//class="one"

	echo "<div class='two'>";

	echo $products[$sku]['details']."<br>";



	echo "<form name='myForm' action='?pg=addtocart' method='post'>";


	echo "Size: <select name='variant' onChange='updateProdctInfo();'>";

	foreach($variants as $sizeoption => $vidNpriceNimg){
		$separate = explode("#", $vidNpriceNimg);
		$vidNprice = $separate[0]."#".$separate[1];

		echo "<option value='$vidNprice'>$sizeoption</option>";
	}

	echo "</select><br/>";

	echo "Quantity: <select name='quantity'>";
	for($qant=1;$qant<=100;$qant++){
		echo "<option value='$qant'>$qant</option>";
	}
	echo "</select>";

	//echo "<input type='submit' value='Add to Cart' onClick='updateProdctInfo();'>";
	echo "<input type='submit' name='submit' value='Add to Cart'>";
	echo "</form>";



	echo "</div>";

	echo "</div>";//class="container"

}else{

	echo "Pajamas: <a href='$thisfile?pg=store&sku=CJLY1428781'>CJLY1428781</a><br>Wirelss charger: <a href='$thisfile?pg=store&sku=CJSJ1089759'>CJSJ1089759</a><br>SSD: <a href='$thisfile?pg=store&sku=CJJSCCGT00017'>CJJSCCGT00017</a><br>";

}


?>




<div id="social">
<h5 style="color:#ffffff;text-align:center;">Tell Your Friends About Us</h5>
Facebook Twitter Youtube Google Plus  Sign up for our newsletter
</div>

</div>



</div>


</div>