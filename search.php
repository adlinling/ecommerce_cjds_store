<?php


if(isset($_POST['submit'])){
	
	$searchterm = $_POST['search'];

	$searchwords = explode(" ", $searchterm);


	include "dbconnect_prepstmt.php";


	$sql = "SELECT title, sku FROM cjproducts";
	$stmt = $conn->prepare($sql); 
	$stmt->execute();

	$result = $stmt->get_result();

	$data = $result->fetch_all(MYSQLI_ASSOC);

	$stmt->close();
	$conn->close();

	//echo "<pre>";
	//print_r($data);
	//echo "</pre>";

	$searchresults = array();

	foreach($data as $key => $subarray){

		$title = $subarray['title'];
		$sku = $subarray['sku'];
		//echo "$title<br>";

		foreach($searchwords as $searchword){
			if(preg_match("/".$searchword."/i", $title)){
				$searchresults[$sku] =  $title;
			}
		}
		
	}


	echo "<br><br>Search results for \"$searchterm\"<br><br>";


	if(count($searchresults)){


		//echo "<pre>";
		//print_r($searchresults);
		//echo "</pre>";

		$productsfound = array();

		foreach($searchresults as $sku => $producttitle){
			$productsfound[] = $sku;
		}

		include "productslist.php";


		$thisfile = $_SERVER['PHP_SELF'];

		$find = array("singquote", "dubquote");
		$replace = array("&#039;", "&quot;");

		echo "<div class='productgrid'>\n";

		foreach($storeproducts as $sku => $prodjson){
			if(in_array($sku, $productsfound)){
				$imgurl = $products[$sku]['image'];
				//$imgurl = "image0.png";
				$title = str_replace($find, $replace, $products[$sku]['title']);
				echo "<div class='productsgriditem'><a class='prodlink' href='$thisfile?pg=store&sku=$sku'><div class='prodimgdiv'><img class='productgridimg' src='$imgurl'></div><div class='prodtitle'>$title</div></a></div>\n";
			}
		}

		echo "</div>";




	}else{
		echo "No products found for your search term.";
	}

}else{
	
	header("Location: index.php");
}

?>