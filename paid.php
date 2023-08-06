<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


include_once "functions.php";

$host = $_SERVER['SERVER_NAME'];
$thispage = $_SERVER['PHP_SELF'];

$pathpieces = explode("/", $thispage);
$thisfile = array_pop($pathpieces);//Removes the name of this file at the end of the path from the array and assign it to $thisfile variable

$currentdir = implode("/", $pathpieces);

//After the purchase, create an account using the email and shipping address from the webhook and
//send message to the email saying an account has been created with a randomly
//generated password to log in to track order
//The user could also create an account before purchase by clicking on Register link.

//The post is sent from checkout.php using sendFetchpost() javascript function after the buyer authorizes the payment 
$inputJSON = file_get_contents('php://input');
$obj = json_decode( $inputJSON ); //see json.php and products.php for more insight on how to handle the array you get form json_decode.
$json = json_encode($obj, JSON_PRETTY_PRINT);


//print_r($obj);

//$json = json_encode($obj, JSON_PRETTY_PRINT);
//file_put_contents("javascriptposttest.htm", "<pre>$json</pre>", FILE_APPEND);


$accountexists = FALSE;


if(isset($obj->referenceid)){

	$paymethod = $obj->paymethod;
	$diffaddr = $obj->diffaddr;
	$accountnum = $obj->accountnum;
	$referencecode = $obj->referenceid;
	$email = $obj->email;


	if($diffaddr == "TRUE"){
		$fname = explode("&billing:", $obj->firstname);
		$lname = explode("&billing:", $obj->lastname);

		$firstname = $fname[0];
		$firstname_bill = $fname[1];

		$lastname = $lname[0];
		$lastname_bill = $lname[1];

		$addr1 = explode("&billing:", $obj->address1);
		$addr2 = explode("&billing:", $obj->address2);


		$address1 = trim($addr1[0]);
		$address2 = trim($addr2[0]);

		$address1_bill = trim($addr1[1]);
		$address2_bill = trim($addr2[1]);


		$citypieces = explode("&billing:", $obj->city);

		$city = $citypieces[0];
		$city_bill = $citypieces[1];

		$statepieces = explode("&billing:", $obj->state);

		$state = $statepieces[0];
		$state_bill = $statepieces[1];

		$zippieces = explode("&billing:", $obj->zip);

		$zip = $zippieces[0];
		$zip_bill = $zippieces[1];

		$countrypieces = explode("&billing:", $obj->country);

		$country = $countrypieces[0];
		$country_bill = $countrypieces[1];


	}else{
		$firstname = $obj->firstname;
		$lastname = $obj->lastname;
		$address1 = trim($obj->address1);
		$address2 = trim($obj->address2);
		$city = $obj->city;
		$state = $obj->state;
		$zip = $obj->zip;
		$country = $obj->country;

		$firstname_bill = $firstname;
		$lastname_bill = $lastname;
		$address1_bill = $address1;
		$address2_bill = $address2;
		$city_bill = $city;
		$state_bill = $state;
		$zip_bill = $zip;
		$country_bill = $country;

	}

	$total = $obj->totalamt;
	$phone = $obj->phone;
	$ip = $obj->ip;

	$recipient = $firstname." ".$lastname;
	$buyer = $firstname_bill." ".$lastname_bill;

	$address = $address1." ".$address2;

	$address_bill = $address1_bill." ".$address2_bill;


	$shipaddr = $address.",".$city.",".$state.",".$zip.",".$country;

	$billaddr = $address_bill.",".$city_bill.",".$state_bill.",".$zip_bill.",".$country_bill;

	$string = "<pre>$json</pre>";
	$string .= "paid.php received post!<br>";
	$string .= "Pay method: $paymethod<br>";
	$string .= "Ship to different address: $diffaddr<br>";
	$string .= "Reference Code: $referencecode<br>";
	$string .= "Email: $email<br><br>";
	$string .= "Shipping Information:<br>";
	$string .= "First Name: $firstname<br>";
	$string .= "Last Name: $lastname<br>";
	$string .= "Address: $shipaddr<br>";
	$string .= "City: $city<br>";
	$string .= "State: $state<br>";
	$string .= "Country: $country<br>";
	$string .= "Zip: $zip<br><br>";

	$string .= "Billing Information:<br>";
	$string .= "First Name: $firstname_bill<br>";
	$string .= "Last Name: $lastname_bill<br>";
	$string .= "Address: $billaddr<br>";
	$string .= "City: $city_bill<br>";
	$string .= "State: $state_bill<br>";
	$string .= "Country: $country_bill<br>";
	$string .= "Zip: $zip_bill<br>";


	$string .= "Total: $total<br>";
	$string .= "Phone: $phone<br>";
	$string .= "IP: $ip<br>";


	include "dbconnect.php";

	//echo "querying database";

	//The account number is the time() create value at account creation
	//If the buyer aleady has an account but is not logged in when making the purchase, you will have the email but not the account number (a registration date)
	//So if you search buy regdate (a previous registration time stamp) you will not find an existing one (since you don't have the account number) and a new account will be created.
	//So look for an existing email instead.   
	//$query = "SELECT regdate FROM cjusers WHERE regdate='$accountnum'";
	$query = "SELECT regdate FROM cjusers WHERE email='$email'";

	$result = mysqli_query($link, $query);


	if(mysqli_num_rows($result) !== 0){

		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			$accountnum = $row['regdate'];
		}

		//echo "An account with the username or email address you provided already exists. ";

		//echo "You can check the status of your order by logging in to your account and go to the orders page..";

		//Send email to customer with order reference number

		//Get the account number which will need be assigned to the order in cjorders table so the user can see all of his purchases after logging in.
		$string .= "An account aleady exists:  $accountnum $email<br>";

		$accountexists = TRUE;

		/* free result set */
		mysqli_free_result($result);


	}else{

		/* free result set */
		mysqli_free_result($result);

		//echo "Proceed with registration<br/>";

		$string .= "No account for this buyer exists yet.  Creating a new one.<br>";

		//time of registration
		$regtime = time();

		//generate identifier code for activating account
		$identifier = md5(uniqid(rand(),1));

		$password = random_str(20);

		$hash = password_hash($password, PASSWORD_DEFAULT);

		$query = "INSERT INTO cjusers (ip, regdate, email, pswd, identifier, activated, firstname, lastname, address, city, state, country, zip, phone) VALUES ('$ip', '$regtime', '$email', '$hash', '$identifier', '', '$firstname', '$lastname', '$address', '$city', '$state', '$country', '$zip', '$phone')";

		$result = mysqli_query($link, $query);

		if($result){


			//The purpose of requiring users to activate their account is actually to validate that the email address they provided is valid.
			//$activationlink = "http://cjstore.a0001.net/activation.php?id=$identifier";
			$activationlink = "http://cjstore.a0001.net/?pg=activation&id=$identifier";
			//$activationlink = $host.$currentdir."/?pg=activation&id=$identifier";

			//echo "<div style='border:black solid 1px;text-align:left;width:400px;padding:3px;word-break:break-all;word-wrap:break-word;'>";
			//echo "This will be sent to the email address the user provided<br/>";
			//echo "Thank you for registering.  Click here <a href='$activationlink' target='blank'>$activationlink</a> to activate your account.";
			//echo "</div>";

			// Import PHPMailer classes into the global namespace
			// These must be at the top of your script, not inside a function


			//Load Composer's autoloader
			require 'vendor/autoload.php';

			$mail = new PHPMailer(true); // Passing `true` enables exceptions
			try {
				//Server settings
				$mail->SMTPDebug = 0; // Enable verbose debug output
				$mail->isSMTP(); // Set mailer to use SMTP
				$mail->Host = 'smtp.gmail.com'; // Specify main and backup SMTP servers  Ex: $mail->Host = 'smtp.gmail.com;smtp2.example.com';
				$mail->SMTPAuth = true; // Enable SMTP authentication
				$mail->Username = 'youremail@gmail.com';// SMTP username
				$mail->Password = 'gmailapppassword'; // SMTP password
				$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
				$mail->Port = 587;
				//$mail->SMTPSecure = 'ssl';// Enable TLS encryption, `ssl` also accepted
				//$mail->Port = 465;  // TCP port to connect to

				//From https://stackoverflow.com/questions/34433459/gmail-returns-534-5-7-14-please-log-in-via-your-web-browser
				//If you keep getting failed authentication errors, turn ON allow Less Secure Apps and then turn off captcha: https://accounts.google.com/DisplayUnlockCaptcha


				//Recipients
				$mail->setFrom('youremailg@gmail.com', 'Your Name');
				$mail->addAddress($email);															   // Name is optional
				$mail->addReplyTo('youremail@gmail.com', 'Your Name');
				//$mail->addCC('cc@example.com');
				//$mail->addBCC('bcc@example.com');

				//Attachments
				//$mail->addAttachment('/var/tmp/file.tar.gz');										 // Add attachments
				// $mail->addAttachment('/tmp/image.jpg', 'new.jpg');// Optional name

				$body = "Thank you for your purchase.  An account with your email as your username has been created.  Click <a href='$activationlink' target='blank'>here</a> to activate your account.  Use the password $password to log in.";

				//Content
				$mail->isHTML(true);																																								  // Set email format to HTML
				$mail->Subject = 'Your Purchase at CJ Dev Store';



				$mail->Body	= $body;
				$mail->AltBody = strip_tags($body);

				$mail->send();


			} catch (Exception $e) {
				//echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
			}

		}else{
			//Error creating an account
			//printf("Error: %s\n", mysqli_error($link));
		}


	}//else for if(mysqli_num_rows($result) !== 0){ account existence check





	//Account number is the $regtime=time() value above when the account was created after the first purchase is made or, If it is the second or subsequent purchase then the number will come from the form in checkout.php
	
	if($accountexists){
		$account = $accountnum;
	}else{
		$account = $regtime;
	}
	



	$query = "INSERT INTO cjorders (account, refnum, recipient, ship_addr, phone, buyer, bill_addr, paymethod, total) VALUES ('$account', '$referencecode', '$recipient', '$shipaddr', '$phone', '$buyer', '$billaddr', '$paymethod', '$total')";

	$result = mysqli_query($link, $query);


	if($result){
		//echo "Reference number and username inserted into orders table";
		$string .= "Reference number and username inserted into orders table<br>";
	}else{
		//Error adding data to orders table
		$string .= "Error adding data to orders table<br>";
		//printf("Error: %s\n", mysqli_error($link));
	}


	/* close connection */
	mysqli_close($link);



	file_put_contents("javascriptposttest.htm", $string, FILE_APPEND);




}//if(isset($obj->referenceid)){


?>

