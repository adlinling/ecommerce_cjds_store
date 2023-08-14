<?php

	if(isset($_COOKIE['session_id'])){
		$N = explode("+", $_COOKIE['session_id']);
		$sessionid = $N[0];
		//echo "$session_id<br/>";

	}else
	if(isset($_SESSION['session_id'])){
		$sessionid = $_SESSION['session_id'];
		//echo "$session_id";
	}



$host = $_SERVER['SERVER_NAME'];

setcookie('session_id', null, time()-3600*24*7, '/', $host);
setcookie('username', null, time()-3600*24*7, '/', $host);
unset($_SESSION['session_id']);

session_destroy();



	include "dbconnect_prepstmt.php";

	$blank = "";

	$stmt = $conn->prepare("UPDATE cjusers SET sessionid=? WHERE sessionid=?");

	$stmt->bind_param("ss", $blank, $sessionid);
	$stmt->execute();


	// Check for errors and affected rows
	if ($stmt->error) {
		die("Error during execution: " . $stmt->error);
	}

	//$affectedRows = $stmt->affected_rows;

	$stmt->close();
	$conn->close();


?>



<div style="margin: 0 auto;width:960px;border 1px white solid;background-color:#454545;padding:20px;">
You're logged out.

<?php

for($i=0;$i<30;$i++){
	echo "<br/>";
}
?>

</div>