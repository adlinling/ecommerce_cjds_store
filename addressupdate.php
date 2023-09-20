<?php

$inputJSON = file_get_contents('php://input');
$obj = json_decode( $inputJSON ); //see json.php and products.php for more insight on how to handle the array you get form json_decode.
$json = json_encode($obj, JSON_PRETTY_PRINT);

//print_r($obj);


$sessionid = $obj->sessionid;
$firstname = $obj->firstname;
$lastname = $obj->lastname;
$address =  $obj->address;
$city = $obj->city;
$state = $obj->state;
$country = $obj->country;
$zip = $obj->zip;
$firstnameship = $obj->firstnameship;
$lastnameship = $obj->lastnameship;
$addressship =  $obj->addressship;
$cityship = $obj->cityship;
$stateship = $obj->stateship;
$countryship = $obj->countryship;
$zipship = $obj->zipship;
$phonecountry = $obj->countryphone;
$phone = $obj->phone;


$string = "addressupdate.php received post from saveaddress() in settings.php <br>";
$string .= "<pre>$json</pre>";
$string .= "Session ID: $sessionid<br>";
$string .= "First Name: $firstname<br>";
$string .= "Last Name: $lastname<br>";
$string .= "Address: $address<br>";
$string .= "City: $city<br>";
$string .= "State: $state<br>";
$string .= "Country: $country<br>";
$string .= "Zip: $zip<br>";
$string .= "First Name Shipping: $firstnameship<br>";
$string .= "Last Name Shipping: $lastnameship<br>";
$string .= "Address Shipping: $addressship<br>";
$string .= "City Shipping: $cityship<br>";
$string .= "State Shipping: $stateship<br>";
$string .= "Country Shipping: $countryship<br>";
$string .= "Zip Shipping: $zipship<br>";
$string .= "Phone Country: $phonecountry<br>";
$string .= "Phone: $phone<br>";

$telephone = $phonecountry."-".$phone;

file_put_contents("javascriptposttest.htm", $string, FILE_APPEND);

?>


<div style="text-align:center;font-size:1.3em;width:100%;height:280px;">

<?php

include_once "dbconnect_prepstmt.php";

$query = "UPDATE cjusers SET firstname = ?, lastname = ?, address=?, city=?, state=?, zip=?, country=?, firstnames = ?, lastnames = ?, addresss=?, citys=?, states=?, zips=?, countrys=?, phone=? WHERE sessionid = ?";

$stmt = $conn->prepare($query);

$stmt->bind_param("ssssssssssssssss", $firstname, $lastname, $address, $city, $state, $zip, $country, $firstnameship, $lastnameship, $addressship, $cityship, $stateship, $zipship, $countryship, $telephone, $sessionid);

$stmt->execute();


//$affectedRows = $stmt->affected_rows;
//echo "Number of affected rows: " . $affectedRows;


// Check for errors and affected rows
if ($stmt->error) {
    die("Error during execution: " . $stmt->error);
}else{

$stmt->close();
$conn->close();


?>


Information Updated!<br>
<br><br><br>
<a style="text-decoration:none;color:#000000;font-family:AsapCondensed,San-serif,Verdana,Arial;font-size:1.2em;border: none;"  href="?pg=settings">OK</a>

<?php
}
?>



</div>