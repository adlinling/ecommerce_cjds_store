<style>


.formcontainer{
  display:flex;
  margin:0 auto;
  max-width:800px;
  flex-direction:column;
  justify-content:center;
  /*align-items:center;*/
  /*border:1px black solid;*/
}


.info{
  margin:0 auto;
  /*border:1px black solid;*/
  max-width:800px;
  text-align:center;
}



.imgchoicecontainer{
   display:flex;
   flex-wrap:wrap;
}


.imgchoice{
  display:flex;
  flex-direction:column;
  justify-content: center;
  padding:4px;
  border:1px white solid;
}

.imgchoice:hover{
  display:flex;
  flex-direction:column;
  justify-content: center;
  padding:4px;
  border:1px blue solid;
}


.choseimg{
  cursor: pointer;
}

.fields{
  display:flex;
  /*border:1px black solid;*/
  padding:10px 0px 10px;;

}


#detailslabel{
  align-items:start;
}


#updatebutton{
  text-align:right;
  padding:0px 30px; 0px 0px;
}


textarea{
 width:97%;
 height:400px;
}


.addprodfieldnames{
  flex: 1;
  display:flex;
  /*border:1px black solid;*/
  justify-content:right;
  align-items:center;
  padding:0px 10px 0px 0px; /*Top Right Left Bottom*/
}




.addprodfields{
  flex: 3;
}


.prodtoeditcontainer{
  margin: 0 auto;
  display:flex;
  flex-direction: column;
  /*border: 1px black solid;*/
  max-width:800px;
  padding:4px;
}

.prodrow{
  display:flex;
  margin: 0 auto;
  width:100%;
  height:180px;
  padding:10px 0px 10px;
  /*border: 1px black solid;*/
}



.prodimage {
  flex: 1;
  display:flex;
  justify-content:center;
  align-items:center;
  /*border: 1px blue solid;*/
  margin: 4px;
}


.prodtitle {
  flex: 2;
  display:flex;
  justify-content:left;
  align-items:center;
  /*border: 1px blue solid;*/
  margin: 4px;
  padding:0px 10px 0px;
}


</style>


<script src="checkout.js"></script>


<div style="text-align:center;padding:10px;background-color:#ffffff">
<font style="font-family:BebasNeue,San-serif,Verdana,Arial;color:#000000;font-size:1.9em;">Edit Product</font>
</div>




<br><br>
<?php


$sku = isset($_GET['sku'])?$_GET['sku']:0;

include "dbconnect_prepstmt.php";


$find = array("singquote", "dubquote");
$replace = array("&#039;", "&quot;");


if($sku){

	if(!isset($_POST['submit'])){

		$query = "SELECT title, image, imageset, markup, json, details FROM cjproducts WHERE sku=?";
		$stmt = $conn->prepare($query); 
		$stmt->bind_param("s", $sku);
		$stmt->execute();

		$result = $stmt->get_result();

		$data = $result->fetch_all(MYSQLI_ASSOC);
		

		//echo "<pre>";
		//print_r($data);
		//echo "</pre>";


		$prodtitle = str_replace($find, $replace, $data[0]['title']);
		$prodimg = $data[0]['image'];
		$prodimgset = explode(",", $data[0]['imageset']);
		$markup = $data[0]['markup'];
		$details= htmlspecialchars_decode($data[0]['details']);
		$details = str_replace("<br>", "\n", $details);
		$prodjson = $data[0]['json'];

		if($result){

		//Set store front image<br>
		//Change Title<br>
		//Change Markup<br>
		//Use str_replace() to change the title of the product in the json.
		//	<div class="prodmainimg"><img src="" style="height: 100%; width: 100%; object-fit: contain;"></div>
		?>

		<form action="" method="POST" class="formcontainer">


		<div class="fields"><div class="addprodfieldnames" title="The store front image">Main Image:</div><div class="addprodfields"><img src="<?php echo $prodimg;?>" style="height: 100%; width: 100%; object-fit: contain;"></div></div>

		<div class="fields">

		<div class="addprodfieldnames" title="Select the main image.">Choose Main Image:</div>

		<div class="addprodfields">


		<?php
		
		echo '<div class="imgchoicecontainer">';
		foreach($prodimgset as $imgkey => $imageoption){

			$idlabel = "imgchoice".$imgkey;

			echo "<div class='imgchoice'><label for='$idlabel'><img class='choseimg' src='$imageoption' style='width: 150px;height:auto;'></label>";

			if($imageoption == $prodimg){
				echo "<input type='radio' name='mainimgchoice' class='choseimg' checked='checked'  id='$idlabel' value='$imageoption'>";
			}else{

				echo "<input type='radio' name='mainimgchoice' class='choseimg'  id='$idlabel' value='$imageoption'>";
			}
			echo "</div>";

		}
		echo "</div>";//class="imgchoicecontainer"

		?>


		</div>

		</div>

		<div class="fields"><div class="addprodfieldnames" title="Enter a different title to change it">Title:</div><div class="addprodfields"><input type="text" name="title" value="<?php echo $prodtitle;?>"></div></div>

		<input type="hidden" name="json" value="<?php echo $prodjson;?>">
		<input type="hidden" name="oldtitle" value="<?php echo $prodtitle;?>">

		<div class="fields"><div class="addprodfieldnames" title="The profit you want to make.">Markup:</div><div class="addprodfields"><input type="text" name="markup" id="markup" value="<?php echo $markup;?>" oninput="onlyfloat('markup', 2);"></div></div>
		<div class="fields"><div class="addprodfieldnames" id="detailslabel" title="This will appear on the product page">Details:</div><div class="addprodfields"><textarea class="detailsbox" name="details"><?php echo $details;?></textarea></div></div>
		<div class="fields"><div class="addprodfieldnames"></div><div class="addprodfields" id="updatebutton"><input type="submit" name="submit" value="Update"></div></div>
		</form>


		<?php

		}else{
			echo "Unable to retrieve product information";
		}


		$stmt->close();


	}else{
		//echo "<pre>";
		//print_r($_POST);
		//echo "</pre>";

		$quotefind = array("'", "\"");
		$quotereplace = array("singquote", "dubquote");

		$title = str_replace($quotefind, $quotereplace, $_POST['title']);

		//echo "Title replaced: $title<br>";

		$oldtitle = $_POST['oldtitle'];

		$json = str_replace($oldtitle, $title, $_POST['json']);


		$json = htmlspecialchars($json, ENT_QUOTES);

		$title = htmlspecialchars($title, ENT_QUOTES);


		$details = str_replace("\n", "<br>", $_POST['details']);
		$details = htmlspecialchars($details);

		$markup = $_POST['markup'];
		$mainimg = $_POST['mainimgchoice'];


		$query = "UPDATE cjproducts SET title = ?, json = ?, image = ?, markup = ?, details = ? WHERE sku = ?";
		$stmt = $conn->prepare($query);

		$stmt->bind_param("ssssss", $title, $json, $mainimg, $markup, $details, $sku);

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

	}


}else{//if($sku){


	$query = "SELECT title, sku, image, markup FROM cjproducts ORDER BY sku ASC";
	$stmt = $conn->prepare($query); 
	$stmt->execute();

	$result = $stmt->get_result();

	if($result){

		echo "<div class='prodtoeditcontainer'>";

		$data = $result->fetch_all(MYSQLI_ASSOC);
		//echo "List products";

		//echo "<pre>";
		//print_r($data);
		//echo "</pre>";


		foreach($data as $datakey => $productarry){
			$prodsku = $productarry['sku'];
			$prodtitle = str_replace($find, $replace, $productarry['title']);
			$prodimg = $productarry['image'];
			echo "<div class='prodrow'><div class='prodimage'><img src='$prodimg' style='height: 100%; width: 100%; object-fit: contain;'></div><div class='prodtitle'><a class='prodlink' href='?pg=editproduct&sku=$prodsku'>$prodtitle</a></div></div>";

		}

		echo "</div>";//class='prodtoeditcontainer'


	}else{
		echo "Unable to retrieve products<br>";
	}

	$stmt->close();

}



$conn->close();


?>