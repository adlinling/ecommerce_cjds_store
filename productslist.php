<?php
/*
Revisions:  Products data are now stored in a database table (instead of in this file) and added using addproduct.php
*/

include "dbconnect_prepstmt.php";


$query = "SELECT * FROM cjproducts";
$stmt = $conn->prepare($query); 

$stmt->execute();

$result = $stmt->get_result();

$data = $result->fetch_all(MYSQLI_ASSOC);


$stmt->close();
$conn->close();


//echo "<pre>";
//print_r($data);
//echo "</pre>";


$storeproducts = array();
$products = array();

foreach($data as $datakey => $datavalue){
	$sku = $datavalue['sku'];
	$title = $datavalue['title'];
	$image = $datavalue['image'];
	$imageset = $datavalue['imageset'];
	$markup = $datavalue['markup'];
	$json = htmlspecialchars_decode($datavalue['json']);
	$details = htmlspecialchars_decode($datavalue['details']);

	//echo $sku." ".$title."<br>";
	$storeproducts[$sku] = $json;
	$products[$sku]['title'] = $title;
	$products[$sku]['image'] = $image;
	$products[$sku]['profit'] = $markup;
	$products[$sku]['details'] = $details;
	$products[$sku]['imageset'] = explode(",", $imageset);
}


//echo "<pre>";
//print_r($storeproducts);
//print_r($products);
//echo "</pre>";

?>