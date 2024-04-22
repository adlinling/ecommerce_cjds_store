<style>

.info{
  margin:0 auto;
  /*border:1px black solid;*/
  max-width:800px;
  text-align:center;
}
</style>

<?php

include "functions.php";
include "credentials.php";
include "productslist.php";




$sku = isset($_GET['sku'])?$_GET['sku']:"";


$prodjson = $storeproducts[$sku];

//echo "The JSON: $prodjson<br>";


$supplier = "";

if(preg_match("/ae_sku_property_dtos/", $prodjson)){
	//echo "Supplier is AE<br>";
	$supplier = "AE";
}

if(preg_match("/variantNameEn/", $prodjson)){
	//echo "Supplier is CJ<br>";
	$supplier = "CJ";
}



if(!isset($_POST['submit'])){

//echo "Supplier: $supplier<br>";
$variantsData = json_decode($storeproducts[$sku]);
$imageset = $products[$sku]['imageset'];
//$imagestring = implode(",", $imageset);
//echo "Image string:<br> $imagestring<br>";


echo "<pre>";
//print_r($imageset);
//print_r($variantsData);
echo "</pre>";



?>
<form method="POST" action="">

What to do with selection:
<input type="radio" name="edit" value="keep">Keep 
<input type="radio" name="edit" value="delete" checked>Delete<br><br>
<?php

foreach($variantsData as $key => $variant){

	echo "$key <input type='checkbox' name='selection[]' value='$key'>";
	echo "<pre>";
	print_r($variant);
	echo "</pre>";

}

echo "<input type='hidden' name='supplier' value='$supplier'>";
?>


<input type="submit" name="submit" value="Submit">

</form>


<?php

//if(isset($_POST['submit'])){
}else{


	$variantsData = json_decode($storeproducts[$sku]);
	$imageset = $products[$sku]['imageset'];


	echo "<pre>";
	//print_r($_POST);
	echo "</pre>";

	$supplier = $_POST['supplier'];


	if(isset($_POST['selection'])){

		$newvariants = array();
		$newimgset = array();
		$removeimgs = array();

		if($_POST['edit'] == "keep"){

			foreach($_POST['selection']  as $selectedindex){
				$newvariants[] = $variantsData[$selectedindex];
			}


			foreach($variantsData  as $varkey => $variant){
				//Collect CJ or AE images to be removed
				if(!in_array($varkey, $_POST['selection'])){

					if($supplier == "CJ"){
						$image_to_remove = $variant->variantImage;
					}

					if($supplier == "AE"){

						if(count($variant->ae_sku_property_dtos->ae_sku_property_d_t_o) == 1){

							//echo "Only one element in ae_sku_property_d_t_o exists<br>";
							$image_to_remove = $variant->ae_sku_property_dtos->ae_sku_property_d_t_o[0]->sku_image;					

						}else{

							//echo "More than one element in ae_sku_property_d_t_o exists<br>";
							foreach($variant->ae_sku_property_dtos->ae_sku_property_d_t_o as $dtokey  =>  $dtovalue){

								if(isset($variant->ae_sku_property_dtos->ae_sku_property_d_t_o[$dtokey]->sku_image)){

									$image_to_remove = $variant->ae_sku_property_dtos->ae_sku_property_d_t_o[$dtokey]->sku_image;

								}

							}
							

						}

				
					}

					echo "Image to delete from set: $image_to_remove<br>";
					$removeimgs[] = $image_to_remove;

				}
			}




		}


		if($_POST['edit'] == "delete"){

			foreach($variantsData  as $varkey => $variant){

				if(!in_array($varkey, $_POST['selection'])){
					$newvariants[] = $variant;
					
				}else{
					//Collect CJ or AE images to be removed
					if($supplier == "CJ"){
						$image_to_remove = $variant->variantImage;
					}

					if($supplier == "AE"){

						if(count($variant->ae_sku_property_dtos->ae_sku_property_d_t_o) == 1){

							//echo "Only one element in ae_sku_property_d_t_o exists<br>";
							$image_to_remove = $variantsData[$key]->ae_sku_property_dtos->ae_sku_property_d_t_o[0]->sku_image;					

						}else{

							//echo "More than one element in ae_sku_property_d_t_o exists<br>";
							foreach($variant->ae_sku_property_dtos->ae_sku_property_d_t_o as $dtokey  =>  $dtovalue){

								if(isset($variant->ae_sku_property_dtos->ae_sku_property_d_t_o[$dtokey]->sku_image)){

									$image_to_remove = $variant->ae_sku_property_dtos->ae_sku_property_d_t_o[$dtokey]->sku_image;

								}

							}
							

						}

				
					}

					echo "Image to delete from set: $image_to_remove";
					$removeimgs[] = $image_to_remove;
				}

			}


		}


		foreach($imageset as $image){
			if(!in_array($image, $removeimgs)){
				$newimgset[] = $image;
			}
		}	



		echo "<hr>";
	
		echo "<pre>";
		print_r($newimgset);
		print_r($newvariants);
		echo "</pre>";

		$newimgset = implode(",", $newimgset);
		$json = json_encode($newvariants);
		$json = htmlspecialchars($json);//convert back using htmlspecialchars_decode()

		include "dbconnect_prepstmt.php";
		
		$query = "UPDATE cjproducts SET imageset = ?, json = ? WHERE sku = ?";
		$stmt = $conn->prepare($query);

		$stmt->bind_param("sss", $newimgset, $json, $sku);

		$stmt->execute();


		// Check for errors and affected rows
		if ($stmt->error) {
			die("Error during execution: " . $stmt->error);
		}

		$affectedRows = $stmt->affected_rows;


		//echo "New json: $json";

		echo "<div class='info'>";

		if($affectedRows){

			echo "Product updated.<br><br>";


		}else{
			echo "Failed to update product.  All values submitted were the same as those in the database.<br><br>";
		}


		//echo "Number of affected rows: " . $affectedRows."<br><br>";
		echo "<a class='prodlink' href='?pg=editproduct&sku=$sku'>OK</a>";

		echo "</div>";//class='info'


		$stmt->close();




	}else{
		echo "Nothing selected<br>";
	}

}
?>