<script src="checkout.js"></script>



<?php
/* 
Revisions:  Shopper can now switch to different currencies
*/



include_once "functions.php";
include "productslist.php";

$thisfile = $_SERVER['PHP_SELF'];

$sku = isset($_GET['sku'])?$_GET['sku']:NULL;



if(isset($_SESSION['currency'])){

	list($currency, $exchangerate) = explode(":", $_SESSION['currency']);
}else{
	$exchangerate = 1;
	$currency = "USD";
	$_SESSION['currency'] = "USD:1";
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



?>

<div style="margin:0 auto;padding:20px;text-align:left;background-color:#ffffff;background-image: url();background-position: 0% 20%;background-repeat: no-repeat;">




<div id="homewrapper">




<div style="text-align:center;padding:10px;background-color:#ffffff;margin:2px;">
<font style="font-family:BebasNeue,San-serif,Verdana,Arial;font-size:1.9em;color:#000000;">

<?php


$find = array("singquote", "dubquote");
$replace = array("&#039;", "&quot;");




if($sku){
	echo str_replace($find, $replace, $products[$sku]['title']);
}else{
	echo "Store";
}

?>

</font>
</div>



<?php


if($sku){


	//echo "The JSON: ".$storeproducts[$sku]."<br>";
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


		//Make a new images array that has the keys reset to 0, 1, 2 . . . 
		foreach($prodimagesbadkeys as $prodimage){
			$productimages[] = $prodimage;
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



	echo "<div class='container'>";



	echo "<div class='one'>";

	
	echo "<div class='displaycontainer'>";
	echo "<div id='prevBtndisp'>&lt;</div>";
	echo "<div id='display' style='height:500px;background-color:rgba(255,255,255,1);'>";

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

	//foreach($productimages as $imgkey => $productimg){
	foreach($productimageset as $imgkey => $productimg){
		echo '<img class="carousel-slide" src="'.$productimg.'" alt="Product" onclick="displayimg(this, '.$imgkey.', \'display\');">';
	}


      //<img class="carousel-slide" src="image0.png" alt="Product Image 1" onclick="displayimg(this, 0, 'display');">
       //<img class="carousel-slide" src="image1.png" alt="Product Image 2" onclick="displayimg(this, 1, 'display');">
       //<img class="carousel-slide" src="image2.png" alt="Product Image 3" onclick="displayimg(this, 2, 'display');">
       //<img class="carousel-slide" src="image3.png" alt="Product Image 4" onclick="displayimg(this, 3, 'display');">
       //<!-- Add more images as needed -->

?>




    </div>
    <div id="rightBtn">&gt;</div>
  </div>

<?php


	echo "</div>";//class="one"

	echo "<div class='two'>";


	echo "<div id='myDiv'>";



	$displayprice = $firstprice*$exchangerate;

	//Styling: searchbox.css
	echo "<form name='changecurrencyform' class='currencyform' action='' method=''>";

	echo "<b>Price</b>: $currsymbol".number_format($displayprice, 2)." ";



	echo "<select name='currencyselect' id='currencyselect' class='currencyselect' onChange='chgcurrency();'>";

	$currencystr = isset($_SESSION['currency'])?$_SESSION['currency']:"USD:1";

	list($selectedcurr, $exchrate) = explode(":", $currencystr);

	//$exchangerates is in functions.php
	foreach($exchangerates as $ekey => $currNrate){
		list($curr, $rate) = explode(":", $currNrate);

		if($curr == $selectedcurr){
			echo "<option value='$currNrate' selected>$currNrate</option>";
		}else{
			echo "<option value='$currNrate'>$currNrate</option>";
		}
	}

	echo "</select></form>";


	echo "USD Price: $firstprice<br>";//Hide this when not debugging. The USD price is already stored in the Options: drop down.
	echo "<input type='text'  id='selectedcurrency' value='$currency:$exchangerate'>";

	if($inventory > 0){
		echo "In stock<br>";
	}else{
		echo "Out of stock<br>";
	}

	echo "</div>";

	echo "<br><br><br>";

	echo "<form name='myForm' action='?pg=addtocart' method='post'>";



	//echo "Option: <select name='variant' onChange='updateProdctInfo();'>";
	echo "Option: <select name='variant' onChange='loadtodisplay();'>";

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
	echo "</form><br><br><br>";



	echo $products[$sku]['details']."<br>";



	echo "</div>";//class='two'

	echo "</div>";//class="container"

}else{

	echo "<div class='productgrid'>\n";
	//echo "<br><br><br>";



	foreach($storeproducts as $sku => $prodjson){
		$imgurl = $products[$sku]['image'];
		//$imgurl = "image0.png";
		$title = str_replace($find, $replace, $products[$sku]['title']);
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
<!--
Facebook Twitter Youtube
<br>
Sign up for our newsletter
-->
</div>


</div>


</div>