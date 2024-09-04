<?php

include "dbconnect_prepstmt.php";

$prodlistslug = isset($_GET['prod'])?$_GET['prod']:NULL;

if($prodlistslug){
	$query = "SELECT * FROM ds_products WHERE slug = ?";//ASC|DESC
	$stmt = $conn->prepare($query); 
	$stmt->bind_param("s", $prodlistslug);
}else{
	$query = "SELECT * FROM ds_products ORDER BY listorder ASC";//ASC|DESC
	$stmt = $conn->prepare($query); 
}




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
$productslugs = array();

foreach($data as $datakey => $datavalue){
	$sku = $datavalue['sku'];
	$title = $datavalue['title'];
	$slug = $datavalue['slug'];
	$image = $datavalue['image'];
	$firstimg = $datavalue['firstimg'];
	$imageset = $datavalue['imageset'];
	$markup = $datavalue['markup'];
	$selectby = $datavalue['selectby'];
	$visible = $datavalue['visible'];
	$attributes = $datavalue['attributes'];
	$pricing = $datavalue['pricing'];
	$strikeprice = $datavalue['strikeprice'];
	$hiddenvids = explode(",", $datavalue['hiddenvids']);
	$showstrikeprice = $datavalue['showstrikeprice'];
	$fixedprice = ($datavalue['fixedprice'])?$datavalue['fixedprice']:1;
	$bundlejson = htmlspecialchars_decode($datavalue['bundleprices']);
	$customimages = htmlspecialchars_decode($datavalue['customimages']);
	$json = htmlspecialchars_decode($datavalue['json']);
	$whatincluded = htmlspecialchars_decode($datavalue['included']);
	$details = htmlspecialchars_decode($datavalue['details']);

	//echo $sku." ".$title."<br>";
	$storeproducts[$sku] = $json;
	$productslugs[$sku] = $slug;


	$products[$sku]['title'] = $title;
	$products[$sku]['image'] = $image;
	$products[$sku]['firstimg'] = $firstimg;
	$products[$sku]['profit'] = $markup;
	$products[$sku]['details'] = $details;
	$products[$sku]['attributes'] = $attributes;
	$products[$sku]['selectby'] = $selectby;
	$products[$sku]['visible'] = $visible;
	$products[$sku]['pricing'] = $pricing;
	$products[$sku]['fixedprice'] = $fixedprice;
	$products[$sku]['strikeprice'] = $strikeprice;
	$products[$sku]['showstrikeprice'] = $showstrikeprice;
	$products[$sku]['hiddenvids'] = $hiddenvids;
	$products[$sku]['bundleprices'] = $bundlejson;
	$products[$sku]['customimages'] = $customimages;
	$products[$sku]['included'] = $whatincluded;
	$products[$sku]['imageset'] = explode(",", $imageset);
}


//echo "<pre>";
//print_r($storeproducts);
//print_r($products);
//echo "</pre>";

?>