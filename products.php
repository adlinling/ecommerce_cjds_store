<script src="checkout.js"></script>


<?php
/* 
Revisions:  $products[$sku]['imageset'] from productslist.php now loads into the image carousel
*/



include_once "functions.php";
include "productslist.php";

$thisfile = $_SERVER['PHP_SELF'];

$sku = isset($_GET['sku'])?$_GET['sku']:NULL;


?>

<div style="margin:0 auto;padding:20px;text-align:left;background-color:#ffffff;background-image: url();background-position: 0% 20%;background-repeat: no-repeat;">




<div id="homewrapper">




<div style="text-align:center;padding:10px;background-color:#ffffff;margin:2px;">
<font style="font-family:BebasNeue,San-serif,Verdana,Arial;font-size:1.9em;color:#000000;">

<?php
if($sku){
	echo $products[$sku]['title'];
}else{
	echo "Store";
}

?>

</font>
</div>



<?php




if($sku){



	$productData = json_decode($storeproducts[$sku]);

	//echo "<pre>";
	//print_r($productData);
	//echo "</pre>";

	$firstimg = NULL;
	$firstprice = NULL;
	$profit = $products[$sku]['profit'];
	$items = array();

	$prodimagesbadkeys = array();
	$productimages = array();
	$productimageset = $products[$sku]['imageset'];


	if(isset($productData)){
		foreach($productData as $key => $subarray){

			$vid = $subarray->vid;
			$varname = $subarray->variantKey;
			$price = $subarray->variantSellPrice + $profit;
			$img = $subarray->variantImage;

			$prodimagesbadkeys[] = $img;
			$variants[$varname] = $vid."#".$price."#".$img;

		}

		//If there are duplicate image urls, this will result in missing keys
		$prodimagesbadkeys = array_unique($prodimagesbadkeys);

		//echo "<pre>";
		//print_r($prodimagesbadkeys);
		//echo "</pre>";

		//Make a new images array that has the keys reset to 0, 1, 2 . . . 
		foreach($prodimagesbadkeys as $prodimage){
			$productimages[] = $prodimage;
		}

		//echo "<pre>";
		//print_r($productimages);
		//echo "</pre>";


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




	//echo "<pre>";
	//print_r($productData['data']);
	//echo "</pre>";


	//echo "<pre>";
	//print_r($inventoryData['data']);
	//echo "</pre>";


	$inventory = isset($inventoryData['data'][0]['storageNum'])?$inventoryData['data'][0]['storageNum']:0;






	echo "<div class='container'>";



	echo "<div class='one'>";

	
	echo "<div class='displaycontainer'>";
	echo "<div id='prevBtndisp'>&lt;</div>";
	echo "<div id='display' style='height:500px;background-color:rgba(40,40,40,1);'>";

	//https://stackoverflow.com/questions/3029422/how-to-auto-resize-an-image-while-maintaining-aspect-ratio
	echo "<img id='productImg' src='$firstimg' style='height: 100%; width: 100%; object-fit: contain;' onclick='modalpop(\"productImg\");'><br>";
	echo "</div>";
	echo "<div id='nextBtndisp'>&gt;</div>";
	echo "</div>";//class="displaycontainer"


?>


  <div class="carousel">
    <div id="leftBtn">&lt;</div>
    <div class="carousel-container">
<?php 

	foreach($productimageset as $imgkey => $productimg){
		echo '<img class="carousel-slide" src="'.$productimg.'" alt="Product" onclick="displayimg(this, '.$imgkey.');">';
	}


?>




    </div>
    <div id="rightBtn">&gt;</div>
  </div>


<?php


	echo "</div>";//class="one"

	echo "<div class='two'>";



	echo $products[$sku]['details']."<br>";


	echo "<div id='myDiv'>";

	echo "Price: \$$firstprice<br>";

	if($inventory > 0){
		echo "In stock<br>";
	}else{
		echo "Out of stock<br>";
	}

	echo "</div>";

	echo "<br><br><br>";

	echo "<form name='myForm' action='?pg=addtocart' method='post'>";


	//echo "Option: <select name='variant' onChange='updateProdctInfo();'>";
	echo "Option: <select name='variant' onChange='loadtodisplay(\"blah\");'>";

	foreach($variants as $sizeoption => $vidNpriceNimg){
		//$separate = explode("#", $vidNpriceNimg);
		//$vidNprice = $separate[0]."#".$separate[1];

		echo "<option value='$vidNpriceNimg'>$sizeoption</option>";
	}

	echo "</select><br>";



	?>
	<div style="padding: 4px 0px;">Quantity:</div>
	<div id="qtycontainer">
	<div class="more" onclick="changequantity('less');" >&nbsp;&nbsp;-&nbsp;&nbsp;</div>
	<input type="text" name="quantity" id="quantity" value="1" oninput="onlynumbers('quantity', 10000);" >
	<div class="less" onclick="changequantity('more');">&nbsp;&nbsp;+&nbsp;&nbsp;</div>
	&nbsp;&nbsp;&nbsp;<input type='submit' name='submit' value='Add to Cart'>
	</div>


	<?php
	//echo "<select name='quantity'>";
	//for($qant=1;$qant<=100;$qant++){
		//echo "<option value='$qant'>$qant</option>";
	//}
	//echo "</select>";

	//echo "<input type='submit' value='Add to Cart' onClick='updateProdctInfo();'>";
	echo "";
	echo "</form>";



	echo "</div>";

	echo "</div>";//class="container"

}else{

	echo "<div class='productgrid'>\n";
	//echo "<br><br><br>";

	foreach($storeproducts as $sku => $prodjson){
		$imgurl = $products[$sku]['image'];
		//$imgurl = "image0.png";
		$title = $products[$sku]['title'];
		echo "<div class='productsgriditem'><a class='prodlink' href='$thisfile?pg=store&sku=$sku'><div class='prodimgdiv'><img class='productgridimg' src='$imgurl'></div><div class='prodtitle'>$title</div></a></div>\n";
	}

	//echo "<div class='productsgriditem'><a href=''><div class='prodimgdiv'><img class='productgridimg' src='$imgurl'></div>Pillows</a></div>\n";
	//echo "<div class='productsgriditem'><a href=''><div class='prodimgdiv'><img class='productgridimg' src='$imgurl'></div>Eye Covers</a></div>\n";

	//echo "<div class='productsgriditem'><a href=''><div class='prodimgdiv'><img class='productgridimg' src='$imgurl'></div>Shirts</a></div>\n";
	//echo "<div class='productsgriditem'><a href=''><div class='prodimgdiv'><img class='productgridimg' src='$imgurl'></div>Mugs</a></div>\n";
	//echo "<div class='productsgriditem'><a href=''><div class='prodimgdiv'><img class='productgridimg' src='$imgurl'></div>Clocks</a></div>\n";

	//echo "<div class='productsgriditem'><a href=''><div class='prodimgdiv'><img class='productgridimg' src='$imgurl'></div>Lamps</a></div>\n";
	//echo "<div class='productsgriditem'><a href=''><div class='prodimgdiv'><img class='productgridimg' src='$imgurl'></div>Slippers</a></div>\n";
	//echo "<div class='productsgriditem'><a href=''><div class='prodimgdiv'><img class='productgridimg' src='$imgurl'></div>Toothbrushes</a></div>\n";

	echo "</div>";


}


?>





</div>


<div id="social">
<div>

Facebook Twitter Youtube
<br>
Sign up for our newsletter
</div>
</div>


</div>


</div>