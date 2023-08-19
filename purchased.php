<?php


$host = $_SERVER['SERVER_NAME'];
setcookie("cart", "", time()-3600*24*7, "/", $host);
setcookie("prices", "", time()-3600*24*7, "/", $host);



//After the purchase, create an account using the email and shipping address from the webhook and 
//send message to the email saying an account has been created with a randomly 
//generated password to log in to track order
//The user could also create an account before purchase by clicking on Register link.

//$email = isset($_GET['email'])?$_GET['email']:"";
//echo "Email:  $email<br>";



//from http://www.hackingwithphp.com/4/11/0/pausing-script-execution, sleep(5); doesn't seem to work on byethose, so have to use this solution
$now = time();
while ($now + 5 > time()) {
    // do nothing
}




$referencecode = isset($_GET['refid'])?$_GET['refid']:"";

echo "Thank you for your order.  The order reference number is $referencecode.  ";

	include "dbconnect_prepstmt.php";


	$query = "SELECT account FROM cjorders WHERE refnum=?";

	$stmt = $conn->prepare($query); 
	$stmt->bind_param("s", $referencecode);
	$stmt->execute();

	$result = $stmt->get_result();

	$data = $result->fetch_all(MYSQLI_ASSOC);

	$affectedRows = $stmt->affected_rows;

	//echo "Affected rows: $affectedRows<br>";

	$stmt->close();



	if($affectedRows == 0){

		echo "No order with given reference code found.<br>";

	}else{
		//echo "Reference code found in orders table.  Getting the account number.<br>";
		$account = $data[0]['account'];
	}



	$query = "SELECT activated FROM cjusers	WHERE regdate=?";

	$stmt = $conn->prepare($query); 
	$stmt->bind_param("s", $account);
	$stmt->execute();

	$result = $stmt->get_result();

	$data = $result->fetch_all(MYSQLI_ASSOC);

	$affectedRows = $stmt->affected_rows;

	//echo "Affected rows: $affectedRows<br>";

	$stmt->close();


	if($affectedRows == 0){
		//Since a row with the account number has already been added to the table by paid.php in checkout.php, this line will never run unless it is new buyer
		echo "No account with given account number found.<br>";

	}else{
		
		$activated = $data[0]['activated'];

		if($activated == "1"){
				echo "You can check the status of your order by logging in to your account.<br>";
		}else{
				echo "A new account has been created.  An activation link and password has been sent to the email you provided.<br>";
		}

	}



	$conn->close();
?>



