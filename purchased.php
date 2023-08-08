<?php




//include_once "functions.php";

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


	include "dbconnect.php";

	$query = "SELECT account FROM cjorders WHERE refnum='$referencecode'";

	$result = mysqli_query($link, $query);

	if(mysqli_num_rows($result) == 0){

		echo "No order with given reference code found.<br>";

	}else{
		//echo "Reference code found in orders table.  Getting the account number.<br>";

		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){

			$account = $row['account'];
		}
	}


	/* free result set */
	mysqli_free_result($result);



	$query = "SELECT activated FROM cjusers	WHERE regdate='$account'";

	$result = mysqli_query($link, $query);


	if(mysqli_num_rows($result) == 0){
		//Since a row with the account number has already been added to the table by paid.php in checkout.php, this line will never run unless it is new buyer
		echo "No account with given account number found.<br>";

	}else{
		
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			$activated = $row['activated'];
		}

		if($activated == "1"){
				echo "You can check the status of your order by logging in to your account.<br>";
		}else{
				echo "A new account has been created.  An activation link and password has been sent to the email you provided.<br>";
		}

	}


	/* free result set */
	mysqli_free_result($result);


	/* close connection */
	mysqli_close($link);
?>



