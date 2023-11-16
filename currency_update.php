<?php
//Run currency_trigger_update.php in localhost to trigger this script on a remote server

date_default_timezone_set('America/Chicago');

$timeofpost = time();

$timeofpost = date("m-d-Y h:i:s", $timeofpost)." CT";


$string = "currency_update.php received post at ".$timeofpost."<br/>";


$timenow = time();
$rowID = "1";

include "dbconnect_prepstmt.php";

$sql = "SELECT * FROM cjcurrency WHERE rowID=?";
$stmt = $conn->prepare($sql); 
$stmt->bind_param("s", $rowID);
$stmt->execute();

$result = $stmt->get_result();

$data = $result->fetch_all(MYSQLI_ASSOC);

$stmt->close();

$string .= "Old exchange rates";
$string .= "<pre>";
$string .= json_encode($data, JSON_PRETTY_PRINT);
$string .= "</pre>";

$lastupdate = $data[0]['lastupdate'];
$timelapsed = $timenow - $lastupdate;

$onehour = 3600;
$oneday = $onehour*24;

$hourslapsed = $timelapsed/$onehour;

$string .= "Time of last update: $lastupdate<br>";
$string .= "Hours since last update: $hourslapsed<br>";

$frequency = isset($_GET['freq'])?$_GET['freq']:24;//hours
//$frequency = 0.00166;//hours

echo "<a href='$thisfile?freq=1'>Update Now</a><br><br>";


if($hourslapsed > $frequency){
//if(true){

	$string .= "Time to update exchange rates.<br>";

	//See currencylayercom.php for login credentials for the API account

	// set API Endpoint and access key (and any options of your choice)
	$endpoint = 'live';
	$access_key = '[your API key]';
	$currencies = "CAD,AUD,GBP,EUR,JPY,INR,PKR,NZD";//Paypal does not support PKR https://developer.paypal.com/docs/reports/reference/paypal-supported-currencies/


	$url = "http://apilayer.net/api/live?access_key=$access_key&currencies=$currencies&source=USD&format=1";


	//API code start 
	// Initialize CURL:
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	// Store the data:
	$json = curl_exec($ch);
	curl_close($ch);

	
	// Decode JSON response:
	$exchRates = json_decode($json, true);

	// Access the exchange rate values, e.g. GBP:
	//$string .= $exchRates['quotes']['USDGBP'];

	
	$string .= "<pre>";
	$string .= $json;
	$string .= "</pre>";

	$success = $exchRates['success'];

	$string .= "Success: $success<br>";

	$exchangerates = array("USD:1");

	foreach($exchRates['quotes'] as $curr => $rate){
		$curr = str_replace("USD", "", $curr);
		$str = $curr.":".$rate;
		$exchangerates[] = $str;
	}

	//API code end



	//This is for testing.  Comment out the API code above when using it.
	//$success = 1;
	//$exchangerates = array("USD:1","CAD:1.".rand(0,300),"AUD:1.".rand(0,300),"GBP:0.".rand(0,300),"EUR:0.".rand(0,300),"JPY:".rand(0,300),"INR:".rand(0,300),"PKR:".rand(0,300),"NZD:".rand(0,300),);
	

	

	$json = json_encode($exchangerates, JSON_PRETTY_PRINT);


	$string .= "<pre>";
	$string .= $json;
	$string .= "</pre>";


	if($success){

		$stmt = $conn->prepare("UPDATE cjcurrency SET lastupdate=?, rates=? WHERE rowID=?");

		$stmt->bind_param("sss", $timenow, $json, $rowID);

		$stmt->execute();


		// Check for errors and affected rows
		if ($stmt->error) {
			die("Error during execution: " . $stmt->error);
		}

		$affectedRows = $stmt->affected_rows;
		$string .= "Number of affected rows: " . $affectedRows;
		$stmt->close();
	}


}else{
	$string .= "Not time to update yet";
}


$conn->close();

file_put_contents("debugging_currency_update.htm", $string, FILE_APPEND);

?>