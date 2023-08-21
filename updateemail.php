<div style="margin:0 auto;width:480px;padding:10px;text-align:center;background-color:#404040;background-image: url();">
<font color="#ffffff"><h3>Update Email</h3></font>
</div>

<br>
<div style="margin:0 auto;width:460px;padding:20px;text-align:left;background-color:#404040;background-image: url();">
<font style="font-size:0.8em;font-family:San-serif,Verdana,Arial;color:#a2a2a2;">
<?php



	if($_GET['id']){
		$id = $_GET['id'];
	}else
	if($_POST['id']){
		$id = $_POST['id'];
	}else{
		$id = $_SESSION['identifier'];//in case user's login fails
	}

	if($_GET['email']){
		$newemail = $_GET['email'];
	}else
	if($_POST['newemail']){
		$newemail = $_POST['newemail'];
	}else{
		$newemail = $_SESSION['newemail'];//in case user's login fails
	}


	include "dbconnect_prepstmt.php";


	$query = "UPDATE cjusers SET email=? WHERE identifier=?";

	$stmt = $conn->prepare($query);
	$stmt->bind_param("ss", $newemail, $id);

	//echo "Excuting query $query<br>";
	$stmt->execute();


	// Check for errors and affected rows
	if ($stmt->error) {
		echo "Error during execution: ".$stmt->error;
	}


	$affectedRows = $stmt->affected_rows;

	$stmt->close();


	if($affectedRows == 1){

		echo "Email updated!<br><br>";
		echo "<a href='index.php?pg=settings'>OK</a>";

	}else{

		echo "Unable to update your email address.<br><br>";
		echo "<a href='index.php?pg=settings'>OK</a>";
	}



	$conn->close();



?>


<br><br><br><br><br><br>
</font>

</div>