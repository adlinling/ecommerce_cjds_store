<div style="text-align:center;padding:10px;background-color:#ffffff">
<font style="font-family:BebasNeue,San-serif,Verdana,Arial;color:#000000;font-size:1.9em;">Delete Product</font>
</div>




<br><br>
<div style="text-align:center;color:#000000;padding:20px;">

<?php


$sku = isset($_GET['sku'])?$_GET['sku']:0;





if(isset($_POST['yes'])){

	//echo "Deleting product with SKU $sku";	

	include "dbconnect_prepstmt.php";

	$query = "DELETE FROM cjproducts WHERE sku = ?";

	$stmt = $conn->prepare($query);
	$stmt->bind_param("s", $sku);
	$stmt->execute();

	// Check for errors and affected rows
	if ($stmt->error) {
		die("Error during execution: " . $stmt->error);
	}

	$affectedRows = $stmt->affected_rows;

	if($affectedRows){
		//echo "Number of affected rows: " . $affectedRows;
		echo "Product with SKU $sku deleted.";
	}else{
		echo "Failed to delete product with the SKU $sku.";
	}


	echo "<br><br><a class='prodlink' href='?pg=editproduct'>OK</a>";

	$stmt->close();
	$conn->close();

}else
if(isset($_POST['no'])){


	echo "Deletion cancelled";
	echo "<br><br><a class='prodlink' href='?pg=editproduct'>OK</a>";



}else{
	?>

	Are you sure?
	<br><br>
	<form action="" method="POST">
	<input type="submit" name="yes" value="Yes">
	<input type="submit" name="no" value="No">
	</form>

	<?php

}

?>

</div>