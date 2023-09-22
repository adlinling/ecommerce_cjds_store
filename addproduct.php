<style>

.formcontainer{
  display:flex;
  margin:0 auto;
  max-width:1000px;
  flex-direction:column;
  justify-content:center;
  align-items:center;
  border:1px black solid;
}


.info{
  margin:0 auto;
  max-width:500px;
  display:flex;
  justify-content:center;
  align-items:center;
}

.fields{
  display:flex;
  justify-content: space-between;
  width:450px;
  padding:10px 0px 10px;
}

.jsonbox, .detailsbox{
  height:300px;
  min-width:177px;
}


input, textarea {
  width:370px;
}

</style>


<form action="" method="POST" class="formcontainer">

<div class="fields">Title:<input type="text" name="title"></div>

<div class="fields">SKU:<input type="text" name="sku"></div>
<div class="fields">JSON:<textarea class="jsonbox" name="json">Get json string from allvariants.php</textarea></div>
<div class="fields">Image:<input type="text" name="image"></div>
<div class="fields">Image Set:<input type="text" name="imageset" value="Get from curl_productdetails.php [productImage] string"></div>
<div class="fields">Markup:<input type="text" name="markup" value="10"></div>
<div class="fields">Details:<textarea class="detailsbox" name="details"></textarea></div>
<div class="fields">&nbsp;<input type="submit" name="submit" value="Submit"></div>
</form>


<div class="info">

<?php

if(isset($_POST['submit'])){

	include "dbconnect_prepstmt.php";


	$title = $_POST['title'];

	$sku = $_POST['sku'];
	$json = $_POST['json']; 

	$json = htmlspecialchars($json);//convert back using htmlspecialchars_decode()

	$image = $_POST['image']; 
	$imageset = $_POST['imageset'];

	$markup = $_POST['markup'];
	$details = $_POST['details'];
	$details = htmlspecialchars($details);

	$vieworder = "";

	$query = "INSERT INTO cjproducts (title, sku, json, image, imageset, markup, details) VALUES (?, ?, ?, ?, ?, ?, ?)";

	//$title, $sku, $json, $image, $imageset, $markup, $details

	$stmt = $conn->prepare($query);
	$stmt->bind_param("sssssss", $title, $sku, $json, $image, $imageset, $markup, $details);


	$result = $stmt->execute();

	if($result){
		echo "Data successfully inserted<br>";
	}else{
		echo "Failed to insert data<br>";
	}

}

?>
</div>


