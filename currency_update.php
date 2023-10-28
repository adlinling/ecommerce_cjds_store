<?php

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

echo "Old exchange rates";
echo "<pre>";
print_r($data);
echo "</pre>";

$lastupdate = $data[0]['lastupdate'];
$timelapsed = $timenow - $lastupdate;

$onehour = 3600;
$oneday = $onehour*24;

$hourslapsed = $timelapsed/$onehour;

echo "Time of last update: $lastupdate<br>";
echo "Hours since last update: $hourslapsed<br>";

$frequency = 24;//hours

if($hourslapsed > $frequency){


	echo "Time to update exchange rates.<br>";



	//See currencylayercom.php for login credentials for the API account

	// set API Endpoint and access key (and any options of your choice)
	$endpoint = 'live';
	$access_key = 'YOUR API KEY';
	$currencies = "CAD,AUD,GBP,EUR,JPY,INR,PKR,NZD";

	$url = "http://apilayer.net/api/live?access_key=$access_key&currencies=$currencies&source=USD&format=1";

	
	// Initialize CURL:
	//$ch = curl_init('https://api.currencylayer.com/'.$endpoint.'?access_key='.$access_key.'');
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	// Store the data:
	$json = curl_exec($ch);
	curl_close($ch);
	
	// Decode JSON response:
	$exchRates = json_decode($json, true);

	// Access the exchange rate values, e.g. GBP:
	//echo $exchRates['quotes']['USDGBP'];

	echo "<pre>";
	print_r($exchRates);
	echo "</pre>";

	$success = $exchRates['success'];

	echo "Success: $success<br>";

	$exchangerates = array("USD:1");

	foreach($exchRates['quotes'] as $curr => $rate){
		$curr = str_replace("USD", "", $curr);
		$str = $curr.":".$rate;
		$exchangerates[] = $str;
	}


	echo "<pre>";
	print_r($exchangerates);
	echo "</pre>";

	//This is for testing
	//$exchangerates = array("USD:1","CAD:1.".rand(0,300),"AUD:1.".rand(0,300),"GBP:0.".rand(0,300),"EUR:0.".rand(0,300),"JPY:".rand(0,300),"INR:".rand(0,300),"PKR:".rand(0,300),"NZD:".rand(0,300),);
		
	$json = json_encode($exchangerates);

	echo $json."<br>";


	if($success){

		$stmt = $conn->prepare("UPDATE cjcurrency SET lastupdate=?, rates=? WHERE rowID=?");

		$stmt->bind_param("sss", $timenow, $json, $rowID);

		$stmt->execute();


		// Check for errors and affected rows
		if ($stmt->error) {
			die("Error during execution: " . $stmt->error);
		}

		$affectedRows = $stmt->affected_rows;
		echo "Number of affected rows: " . $affectedRows;
		$stmt->close();
	}


}else{
	echo "Not time to update yet";
}


$conn->close();



?>