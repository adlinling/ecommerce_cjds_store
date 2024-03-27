<?php
//Revisions:  Products can now be added just by entering the SKU and the markup amount.
?>

<style>

.formcontainer{
  display:flex;
  margin:0 auto;
  max-width:800px;
  flex-direction:column;
  justify-content:center;
  align-items:center;
  /*border:1px black solid;*/
}


.info{
  margin:0 auto;
  max-width:500px;
  text-align:center;
}

.fields{
  flex:1;
  display:flex;
  justify-content: space-between;
  width:56%;
  padding:10px 30px 10px;
}

.jsonbox, .detailsbox{
  display:flex;
  flex:1;
  height:100px;
  max-width:364px;
}


input, textarea {
  flex:1;
  max-width:370px;
}


.addprodfieldnames{
   display:flex;
   justify-content:left;
   /*border: 1px black solid;*/
   max-width:100px;
}

</style>


<script src="checkout.js"></script>


<div style="text-align:center;padding:10px;background-color:#ffffff">
<font style="font-family:BebasNeue,San-serif,Verdana,Arial;color:#000000;font-size:1.9em;">Add CJ Product</font>
</div>


<?php
include_once "functions.php";
include "credentials.php";


$isowner = FALSE;
if($_COOKIE['username'] == $owneremail){
	$isowner = TRUE;
}


if($isowner){
?>


<form action="" method="POST" class="formcontainer">

<div class="fields"><div class="addprodfieldnames" title="The SPU code shown on the product page.">SKU:</div><input type="text" name="sku"></div>
<div class="fields"><div class="addprodfieldnames" title="The profit you want to make">Markup:</div><input id="markup" type="text" name="markup" value="10" oninput="onlyfloat('markup', 2);"></div>
<div class="fields">&nbsp;<input type="submit" name="submit" value="Submit"></div>
</form>

<?php
}//if($isowner){
?>


<div class="info">



<?php

if(isset($_POST['submit'])){


	if(!empty($_POST['sku']) && !empty($_POST['markup'])){

		$sku = trim($_POST['sku']);
		$markup = $_POST['markup'];




		//echo "<hr>";
		$descripimages =  $decodedData['data']['description'];



		$descriptext = strip_tags($decodedData['data']['description'], '<p><br>');

		$productImageSet = $decodedData['data']['productImageSet'];
		$title = $decodedData['data']['productNameEn'];


		$variants = $decodedData['data']['variants'];

		$variantsjson = json_encode($variants);


		preg_match_all("/http[:a-zA-Z0-9-\.\/]+\.com\/[a-zA-Z0-9-]+\.(jpg|png)/", $descripimages, $imagematches);



		//echo "<pre>";
		//print_r($productImageSet);
		//print_r($imagematches[0]);
		//echo "</pre>";

		$mergedimages = array_merge($productImageSet, $imagematches[0]);

		//echo "Count before unique function: ".count($mergedimages)."<br>";

		$mergedimages = array_unique($mergedimages);

		//echo "Count after unique function: ".count($mergedimages)."<br>";

		//Insert into DB table
		$imagestring = implode(",", $mergedimages);

		$storefrontimage = $mergedimages[0];

		//echo "Image set string:$imagestring<br> ";

		//echo "Title: $title<br>";


		//echo "<br><br>Desciption with images striped:<br>";
		//echo "$descriptext<br>";




		include "dbconnect_prepstmt.php";



		$listorder = "";
		$json = $variantsjson; 

		$json = htmlspecialchars($json);//convert back using htmlspecialchars_decode()

		$image = $storefrontimage; 
		$imageset = $imagestring;
		$slug = str_replace(" ", "_", strtolower($title));



		$details = htmlspecialchars($descriptext);

		$vieworder = "";
		$whatsincluded = "";

		$query = "INSERT INTO cjproducts (title, listorder, slug, sku, json, image, firstimg, imageset, included, markup, details) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

		//$title, $sku, $json, $image, $imageset, $markup, $details

		$stmt = $conn->prepare($query);
		$stmt->bind_param("sssssssssss", $title, $listorder, $slug, $sku, $json, $image, $storefrontimage, $imageset, $whatsincluded, $markup, $details);


		$result = $stmt->execute();

		if($result){
			echo "Data successfully inserted<br><br>";
			echo "<a class='prodlink' href='?pg=storemanager'>OK</a>";
		}else{
			echo "Failed to insert data<br>";
		}
	}else{
		if(empty($_POST['sku'])){
			echo "You must enter an SKU.<br>";
		}

		if(empty($_POST['markup'])){
			echo "You must enter a markup amount.<br>";
		}
	}
}

?>

</div><!--class="info"-->