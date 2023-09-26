<div style="text-align:center;padding:10px;background-color:#ffffff">
<font style="font-family:BebasNeue,San-serif,Verdana,Arial;color:#000000;font-size:1.9em;">Delete Product</font>
</div>




<br><br>
<div style="text-align:center;color:#000000;padding:20px;">

<?php


$sku = isset($_GET['sku'])?$_GET['sku']:0;

//include "dbconnect_prepstmt.php";



if(isset($_POST['yes'])){
	echo "Delete product with SKU $sku";	
}else
if(isset($_POST['no'])){


	echo "Product not deleted";

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