<script src="checkout.js"></script>

<?php

/*
Paypal button parameters/variables 2019 https://developer.paypal.com/docs/checkout/integrate/#4-set-up-the-transaction (click on the "Orders Create" link).

The paypal code below is a heavily modified version of the original that was taken from the PayPalCheckoutServerSideV2_php.zip package found on https://demo.paypal.com/us/demo/code_samples under the PHP link in the section PayPal Checkout Server-side using Orders v2 APIs and PayPal JavaScript SDK



Stripe
webhook https://stripe.com/docs/payments/checkout/fulfill-orders
Links at bottom of the Quickstart page (https://stripe.com/docs/development/quickstart):
Prebuilt checkout page https://stripe.com/docs/checkout/quickstart
Custom Payment flow https://stripe.com/docs/payments/quickstart


Revisions: Switched to using prepared statements
*/





include_once "functions.php";
include_once "productslist.php";


$reference_id = time();

$accountnum = "";
$email = "";
$firstname= "";
$lastname = "";
$address = "";
$city = "";
$state = "";
$zip = "";
$country = "";
$firstnames= "";
$lastnames = "";
$addresss = "";
$citys = "";
$states = "";
$zips = "";
$countrys = "";
$phonenum = "";
$phone = "";



	if(isset($_COOKIE['session_id'])){
		$sessionid = $_COOKIE['session_id'];

	}else
	if(isset($_SESSION['session_id'])){
		$sessionid = $_SESSION['session_id'];
	}


if(isset($sessionid)){

	//echo "Session ID: $sessionid<br>";

	include "dbconnect_prepstmt.php";

	$query = "SELECT * FROM cjusers WHERE sessionid=?";

	$stmt = $conn->prepare($query); 
	$stmt->bind_param("s", $sessionid);
	$stmt->execute();

	$result = $stmt->get_result();

	$data = $result->fetch_all(MYSQLI_ASSOC);

	$affectedRows = $stmt->affected_rows;

	//echo "Affected rows: $affectedRows<br>";

	$stmt->close();
	$conn->close();

	//echo "<pre>";
	//print_r($data);
	//echo "</pre>";

	if($affectedRows == 1){
		$accountnum= $data[0]['regdate'];
		$email = $data[0]['email'];
		$firstname= $data[0]['firstname'];
		$lastname = $data[0]['lastname'];
		$address = $data[0]['address'];
		$city = $data[0]['city'];
		$state = $data[0]['state'];
		$zip = $data[0]['zip'];
		$country = $data[0]['country'];
		$firstnames= $data[0]['firstnames'];
		$lastnames = $data[0]['lastnames'];
		$addresss = $data[0]['addresss'];
		$citys = $data[0]['citys'];
		$states = $data[0]['states'];
		$zips = $data[0]['zips'];
		$countrys = $data[0]['countrys'];
		$phonenum = $data[0]['phone'];

	}

}



	if($phonenum){
		$breakphone = explode("-", $phonenum);
		$phonecountry = $breakphone[0];
		$phone = $breakphone[1];
	}


//Get visitor's ip address
//From http://www.xpertdeveloper.com/2011/09/get-real-ip-address-using-php/
if (!empty($_SERVER["HTTP_CLIENT_IP"]))
{
 //check for ip from share internet
 $ip = $_SERVER["HTTP_CLIENT_IP"];
}
elseif (!empty($_SERVER["HTTP_X_FORWARDED_FOR"]))
{
 // Check for the Proxy User
 $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
}
else
{
 $ip = $_SERVER["REMOTE_ADDR"];
}

//echo "IP $ip";

?>


<div style="text-align:center;padding:10px;background-color:#282828">
<font style="font-family:San-serif,Verdana,Arial;color:#ffffff;"><h3>Checkout</h3></font>
</div>

<br/>
<div class="checkoutpg-container">

<div class="totalstable">

		<div class='orders-container' >
		<div class="order-item"></div>
		<div class="order-item"></div>
 		<div class="order-item">Quantity</div>
 		<div class="order-item">Price Ea.</div>
		<div class="order-item">Total</div>
		</div>

<?php


		$subtotal = 0;
		$grandtotal = 0;



foreach($_POST['items'] as $cartitem){



		$breakcartitem = explode("#", $cartitem);

		$itemddescr = $breakcartitem[0];
		$vid = $breakcartitem[1];
		$itemquantity = $breakcartitem[2];
		$itemprice = $breakcartitem[3];
		$itemtotal = $itemquantity*$itemprice;

		$descripshort = substr($itemddescr, 0, 30);

		foreach($products as $prodkey => $product){
			$productname = $product['title'];
			//echo "$productname <br>";
			if(preg_match("/$descripshort/i", $productname)){
				//echo "$prodkey $productname<br>";
				$prodsku = $prodkey;
			}
		}


		$subtotal = $subtotal + $itemtotal;

		echo "<div class='orders-container' >";

		echo '<div class="order-item"><a href="?pg=store&sku='.$prodsku.'"><img src="" width="100" height="100"></a></div>';
		echo '<div class="order-item"><a href="?pg=store&sku='.$prodsku.'">'.$itemddescr.'</a></div>';
 		echo '<div class="order-item">'.$itemquantity.'</div>';
 		echo '<div class="order-item">'.number_format($itemprice, 2).'</div>';
		echo '<div class="order-item">'.number_format($itemtotal, 2).'</div>';


		echo "</div>";

}

?>


		<div class='orders-container' >

		<div class="order-item"></div>
		<div class="order-item" id="debug"></div>
 		<div class="order-item"></div>
 		<div class="order-item">Subtotal</div>
		<div class="order-item" id="subtotal"><?php echo number_format($subtotal, 2)";?> </div>

		</div>


		<div class='orders-container' >

		<div class="order-item"></div>
		<div class="order-item"></div>
 		<div class="order-item"></div>
 		<div class="order-item">Shipping</div>
		<div class="order-item" id="shipcost"></div>


		</div>



		<div class='orders-container' >

		<div class="order-item"></div>
		<div class="order-item"></div>
 		<div class="order-item"></div>
 		<div class="order-item">Grand Total</div>
		<div class="order-item" id="grandtotal"></div>

		</div>



	<?php
	//echo "<pre>";
	//print_r($_POST);
	//echo "</pre>";
	?>

</div>

<?php




		echo "<div style='flex:1;padding:10px;background-color:#282828;'>";


    $rootPath = "../";
    include_once('api/Config/Config.php');
    //include('../templates/header.php');



if(count($_POST)){


$subtotal = $_POST['subtotal'];
//$itemcount = $_POST['itemcount'];
$itemcount = 1;
$tax_amt = 0;
$handling_fee = 0;
$insurance_fee = 0;
$shipping_discount = 0;
$currency = "USD";

		$additional = $itemcount - 1;


		//if($country == "USA"){
		//	$shipping = 4.99 + 1.50*$additional;
		//}else{
		//	$shipping = 7.5 + 5.95*$additional;
		//}

		$shipping = 4.99 + 1.50*$additional;
		$shipping_ground = 10 + 0.75*$additional;
		$shipping_twoday = 20 + 2*$additional;
		$shipping_overnight = 25 + 3*$additional;


//$grandtotal = $subtotal + $tax_amt + $handling_fee + $shipping - $shipping_discount + $insurance_fee;
//echo "Grand total: $grandtotal<br/>";

$items = array();
$products = array();

foreach($_POST['items'] as $itemvalue){

	$item = array();


	//echo "$itemvalue<br/>";
	$hashbreak = explode("#", $itemvalue);

	list($name, $vid, $quantity, $price) = $hashbreak;


	$name = substr($name, 0, 60);

	//echo "Name: $name<br/>";
	$item['name'] = $name;//There is a max length allowed or else Paypal payment window will not open.


	$item['description'] = $name;

	//echo "SKU: $vid<br/>";

	$item['sku'] = $vid;//Must use sku as index name.  vid will not show up in webhook


	//echo "Price: $price<br/>";
	$item['unit_amount'] = (object)array("currency_code" => "USD", "value" => "$price");

	//echo "Quantity: $quantity<br/>";

	$item['quantity'] = $quantity;

	//echo "<br/>";

	$item = (object)$item;

	//echo "<pre>";
	//print_r($item);
	//echo json_encode($item, JSON_PRETTY_PRINT);
	//echo "</pre>";

	$items[] = $item;

	$products[] = array("quantity" => $quantity, "vid" => $vid);

}



$dataraw = array(
	"startCountryCode" => "CN",
	"endCountryCode" => $country,
	"products" => $products,
);


$productsjson = json_encode($products);
$productsjsonurl = urlencode($productsjson);


//$productsjsonurl = "the products json string url encoded";

	//echo "The items array:<br/>";


	//echo "<pre>";
	//print_r($items);
	//echo "</pre>";


	//echo "<pre>";
	//print_r($dataraw);
	//echo "</pre>";

	//echo $productsjsonurl."<br>";

	$items = str_replace("\"", "#", json_encode($items));


    $baseUrl = str_replace("pages/shipping.php", "", URL['current']);


    //echo "rootpath + services ordercapture: ".URL['services']['orderCapture'];


// Initializing curl
$curl = curl_init();

// Sending POST request to reqres.in
// server to get JSON data
curl_setopt($curl, CURLOPT_URL,	"https://developers.cjdropshipping.com/api2.0/v1/logistic/freightCalculate");


// Telling curl to store JSON
// data in a variable instead
// of dumping on screen
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

$headers = [
    'Content-Type: application/json',
    //Get this using cj_gettoken.php
    'CJ-Access-Token: '.$token //stored in functions.php

];

curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

$dataraw = json_encode($dataraw);



//From https://blog.cpming.top/p/php-curl-raw-json-xml
curl_setopt($curl, CURLOPT_POSTFIELDS, $dataraw);


// Executing curl
$response = curl_exec($curl);

// Checking if any error occurs
// during request or not
if($e = curl_error($curl)) {
	echo "Shipping cost error";
	//echo $e;
} else {

	// Decoding JSON data
	$decodedData =  json_decode($response, true);

	// Outputting JSON data in
	// Decoded form
	//var_dump($decodedData);
}

// Closing curl
curl_close($curl);

$shippingoptions = $decodedData;


//echo "<pre>";
//print_r($shippingoptions);
//echo "</pre>";


?>

<!-- HTML Content -->
<div class="row-fluid">
    <div class="col-md-offset-4 col-md-4">
        <h3 class="text-center">Billing Information</h3>
        <hr>
	<form class="form-horizontal" name="myForm" method="POST" action="https://hycty.com/stripe/public/checkout.php" onsubmit="return validate('<?php echo $reference_id;?>', '<?php echo $ip;?>');">		



	<?php
	//<form class="form-horizontal" name="myForm" method="POST" action="http://localhost:4242/checkout.php">	
	//	<form class="form-horizontal" name="myForm" method="POST" action="stripe/composer/public/checkout.php">



	foreach($_POST['items'] as $itemvalue){
		echo "<input type='hidden' name='items[]' value='$itemvalue'>";
	}

	echo "<input type='hidden' name='subtotal' value='".$_POST['subtotal']."'>";


	?>

            <div class="form-group">

                <div class="col-sm-7">
                    <input class="form-control"
                           type="hidden"
                           id="reference_id"
                           name="reference_id"
                           value="<?php echo $reference_id;?>">
                </div>
            </div>


            <!-- Shipping Information -->
            <div class="form-group">
                <label for="first_nameb" class="col-sm-5 control-label">First Name</label>
                <div class="col-sm-7">
                    <input class="form-control"
                           type="text"
                           id="first_nameb"
                           name="first_nameb"
                           value="<?php echo $firstname;?>">
                </div>
            </div>
            <div class="form-group">
                <label for="last_nameb" class="col-sm-5 control-label">Last Name</label>
                <div class="col-sm-7">
                    <input class="form-control"
                           type="text"
                           id="last_nameb"
                           name="last_nameb"
                           value="<?php echo $lastname;?>">
                </div>
            </div>
            <div class="form-group">
                <label for="line1b" class="col-sm-5 control-label">Address Line 1</label>
                <div class="col-sm-7">
                    <input class="form-control"
                           type="text"
                           id="line1b"
                           name="line1b"
                           value="<?php echo $address;?>">
                </div>
            </div>
            <div class="form-group">
                <label for="line2b" class="col-sm-5 control-label">Address Line 2</label>
                <div class="col-sm-7">
                    <input class="form-control"
                           type="text"
                           id="line2b"
                           name="line2b"
                           value="">
                </div>
            </div>
            <div class="form-group">
                <label for="cityb" class="col-sm-5 control-label">City</label>
                <div class="col-sm-7">
                    <input class="form-control"
                           type="text"
                           id="cityb"
                           name="cityb"
                           value="<?php echo $city;?>">
                </div>
            </div>
            <div class="form-group">
                <label for="stateb" class="col-sm-5 control-label">State/Province</label>
                <div class="col-sm-7" id="stateselectb">


			<?php
			if($country == "US" || $country == ""){
				echo '<select class="form-control" id="stateb" name="stateb">';

				$USstates = "AL,AK,AR,AS,AZ,CA,CO,CT,DC,DE,FL,GA,GU,HI,IA,ID,IL,IN,KS,KY,LA,MA,MD,ME,MI,MN,MO,MP,MS,MT,NC,NE,NH,NJ,NM,NV,NY,ND,OH,OK,OR,PA,PR,RI,SC,SD,TN,TX,UM,UT,VI,VT,VA,WA,WI,WV,WY";

				$USstates = explode(",", $USstates);

				foreach($USstates as $stateoption){

					if($stateoption == $state){
						echo '<option value="'.$stateoption.'" selected>'.$stateoption.'</option>';
					}else{
						echo '<option value="'.$stateoption.'">'.$stateoption.'</option>';
					}
				}
				echo '</select>';
			}else{
				echo "<input type='text' name='stateb' id='stateb' value='".$state."'>";
			}

			?>


                </div>
            </div>
            <div class="form-group">
                <label for="zipb" class="col-sm-5 control-label">Postal Code</label>
                <div class="col-sm-7">
                    <input class="form-control"
                           type="text"
                           id="zipb"
                           name="zipb"
                           value="<?php echo $zip;?>" oninput="onlynumbers('zipb', 10);">
                </div>
            </div>
            <div class="form-group">
                <label for="countrySelectb" class="col-sm-5 control-label">Country</label>
                <div class="col-sm-7">
                    <select class="form-control" name="countrySelectb" id="countrySelectb" onChange='changeshipping("bill", "<?php echo $productsjsonurl;?>");' >
			<?php

				//$countries is stored in functions.php
				foreach($countries as $ckey => $countryoption){

					if($ckey == $country){
						echo '<option value="'.$ckey.'" selected>'.$countryoption.'</option>';
					}else{
						echo '<option value="'.$ckey.'">'.$countryoption.'</option>';
					}
				}
			?>

                    </select>
                </div>
            </div>
            <div class="form-group">

                <div class="col-sm-7">
                    <input class="form-control"
                           type="hidden"
                           id="accountnum"
                           name="accountnum"
                           value="<?php echo $accountnum;?>">
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-7">
                <label for="diffaddr" class="col-sm-5 control-label">Ship to Different Address:</label>
                    <input class="form-control"
                           type="checkbox"
                           id="diffaddr"
                           name="diffaddr" onchange="spawnshipaddr('<?=$productsjsonurl;?>', '<?=$firstnames;?>', '<?=$lastnames;?>', '<?=$addresss;?>', '<?=$citys;?>', '<?=$states;?>', '<?=$zips;?>', '<?=$countrys."#".$country;?>');">
                </div>
            </div>



            <div class="form-group">
                <label for="emailaddr" class="col-sm-5 control-label">Email</label>
                <div class="col-sm-7">
                    <input class="form-control"
                           type="text"
                           id="emailaddr"
                           name="emailaddr"
                           value="<?php echo $email;?>">
                </div>
            </div>
            <div class="form-group">
                <label for="phone" class="col-sm-5 control-label">Phone Number</label>
                <div class="col-sm-7">

                  <select class="form-control" name="countryPhoneSelect" id="countryPhoneSelect"  style="width:54px;display:inline-flex;">
			<?php

			//Make drop down select menu change width: https://stackoverflow.com/questions/64961278/shrink-select-element-to-fit-in-nested-flexbox

			$countries_phone = array(
				"US" => "United States of America (the) +1",
				"AF" => "Afghanistan +93",
				"AX" => "Åland Islands +358",
				"AL" => "Albania +355",
				"DZ" => "Algeria +213",
				"AS" => "American Samoa +1",
				"AD" => "Andorra +376",
				"AO" => "Angola +244",
				"AI" => "Anguilla +1",
				"AQ" => "Antarctica +672",
				"AG" => "Antigua and Barbuda +1",
				"AR" => "Argentina +54",
				"AM" => "Armenia +374",
				"AW" => "Aruba +297",
				"AU" => "Australia +61",
				"AT" => "Austria +43",
				"AZ" => "Azerbaijan +994",
				"BS" => "Bahamas(the) +1",
				"BH" => "Bahrain +973",
				"BD" => "Bangladesh +880",
				"BB" => "Barbados +1",
				"BY" => "Belarus +375",
				"BE" => "Belgium +32",
				"BZ" => "Belize +501",
				"BJ" => "Benin +229",
				"BM" => "Bermuda +1",
				"BT" => "Bhutan +975",
				"BO" => "Bolivia (Plurinational State of) +591",
				"BQ" => "Bonaire, Sint Eustatius and Saba +599",
				"BA" => "Bosnia and Herzegovina +387",
				"BW" => "Botswana +267",
				"BV" => "Bouvet Island +47",
				"BR" => "Brazil +55",
				"IO" => "British Indian Ocean Territory (the) +246",
				"BN" => "Brunei Darussalam +673",
				"BG" => "Bulgaria +359",
				"BF" => "Burkina Faso +226",
				"BI" => "Burundi +257",
				"CV" => "Cabo Verde +238",
				"KH" => "Cambodia +855",
				"CM" => "Cameroon +237",
				"CA" => "Canada +1",
				"KY" => "Cayman Islands (the) +1",
				"CF" => "Central African Republic (the) +236",
				"TD" => "Chad +235",
				"CL" => "Chile +56",
				"CN" => "China +86",
				"CX" => "Christmas Island +61",
				"CC" => "Cocos (Keeling) Islands(the) +61",
				"CO" => "Colombia +57",
				"KM" => "Comoros (the) +269",
				"180" => "Congo (the Democratic Republic of OD +243",
				"CG" => "Congo (the) +242",
				"CK" => "Cook Islands (the) +682",
				"CR" => "Costa Rica +506",
				"CI" => "Côte d'Ivoire +225",
				"HR" => "Croatia +385",
				"CU" => "Cuba +53",
				"CW" => "Curaçao +599",
				"CY" => "Cyprus +357",
				"CZ" => "Czechia +420",
				"DK" => "Denmark +45",
				"DJ" => "Djibouti +253",
				"DM" => "Dominica +1",
				"DO" => "Dominican Republic (the) +1",
				"EC" => "Ecuador +593",
				"EG" => "Egypt +20",
				"SV" => "El Salvador +503",
				"GQ" => "Equatorial Guinea +240",
				"ER" => "Eritrea +291",
				"EE" => "Estonia +372",
				"ET" => "Ethiopia +251",
				"FK" => "Falkland Islands (the) [Malvinas] +500",
				"FO" => "Faroe Islands (the) +298",
				"FJ" => "Fiji +679",
				"FI" => "Finland +358",
				"FR" => "France +33",
				"GF" => "French Guiana +594",
				"PF" => "French Polynesia +689",
				"TF" => "French Southern Territories (the) +262",
				"GA" => "Gabon +241",
				"GM" => "Gambia (the) +220",
				"GE" => "Georgia +995",
				"DE" => "Germany +49",
				"GH" => "Ghana +233",
				"GI" => "Gibraltar +350",
				"GR" => "Greece +30",
				"GL" => "Greenland +299",
				"GD" => "Grenada +1",
				"GP" => "Guadeloupe +590",
				"GU" => "Guam +1",
				"GT" => "Guatemala +502",
				"GG" => "Guernsey +44",
				"GN" => "Guinea +224",
				"GW" => "Guinea-Bissau +245",
				"GY" => "Guyana +592",
				"HT" => "Haiti +509",
				"HM" => "Heard Island and McDonald Islands +672",
				"VA" => "Holy See (the) +379",
				"HN" => "Honduras +504",
				"HK" => "Hong Kong +852",
				"HU" => "Hungary +36",
				"IS" => "Iceland +354",
				"IN" => "India +91",
				"ID" => "Indonesia +62",
				"IR" => "Iran (Islamic Republic of) +98",
				"IQ" => "Iraq +964",
				"IE" => "Ireland +353",
				"IM" => "Isle of Man +44",
				"IL" => "Israel +972",
				"IT" => "Italy +39",
				"JM" => "Jamaica +1",
				"JP" => "Japan +81",
				"JE" => "Jersey +44",
				"JO" => "Jordan +962",
				"KZ" => "Kazakhstan +7",
				"KE" => "Kenya +254",
				"KI" => "Kiribati +686",
				"KP" => "Korea (the Democratic People's Republic of) +850",
				"KR" => "Korea (the Republic of) +82",
				"KW" => "Kuwait +965",
				"KG" => "Kyrgyzstan +996",
				"LA" => "Lao People's Democratic Republic (the) +856",
				"LV" => "Latvia +371",
				"LB" => "Lebanon +961",
				"LS" => "Lesotho +266",
				"LR" => "Liberia +231",
				"LY" => "Libya +218",
				"LI" => "Liechtenstein +423",
				"LT" => "Lithuania +370",
				"LU" => "Luxembourg +352",
				"MO" => "Macao +853",
				"MK" => "Macedonia (the former Yugoslav Republic of) +389",
				"MG" => "Madagascar +261",
				"MW" => "Malawi +265",
				"MY" => "Malaysia +60",
				"MV" => "Maldives +960",
				"ML" => "Mali +223",
				"MT" => "Malta +356",
				"MH" => "Marshall Islands (the) +692",
				"MQ" => "Martinique +596",
				"MR" => "Mauritania +222",
				"MU" => "Mauritius +230",
				"YT" => "Mayotte +262",
				"MX" => "Mexico +52",
				"FM" => "Micronesia (Federated States of) +691",
				"MD" => "Moldova (the Republic of) +373",
				"MC" => "Monaco +377",
				"MN" => "Mongolia +976",
				"ME" => "Montenegro +382",
				"MS" => "Montserrat +1",
				"MA" => "Morocco +212",
				"MZ" => "Mozambique +258",
				"MM" => "Myanmar +95",
				"NA" => "Namibia +264",
				"NR" => "Nauru +674",
				"NP" => "Nepal +977",
				"NL" => "Netherlands (the) +31",
				"NC" => "New Caledonia +687",
				"NZ" => "New Zealand +64",
				"NI" => "Nicaragua +505",
				"NE" => "Niger (the) +227",
				"NG" => "Nigeria +234",
				"NU" => "Niue +683",
				"NF" => "Norfolk Island +672",
				"MP" => "Northern Mariana Islands (the) +1",
				"NO" => "Norway +47",
				"OM" => "Oman +968",
				"PK" => "Pakistan +92",
				"PW" => "Palau +680",
				"PS" => "Palestine, State of +970",
				"PA" => "Panama +507",
				"PG" => "Papua New Guinea +675",
				"PY" => "Paraguay +595",
				"PE" => "Peru +51",
				"PH" => "Philippines (the) +63",
				"PN" => "Pitcairn +64",
				"PL" => "Poland +48",
				"PT" => "Portugal +351",
				"PR" => "Puerto Rico +1",
				"QA" => "Qatar +974",
				"RE" => "Réunion +262",
				"RO" => "Romania +40",
				"RU" => "Russian Federation (the) +7",
				"RW" => "Rwanda +250",
				"BL" => "Saint Barthélemy +590",
				"SH" => "Saint Helena, Ascension and Tristan da Cunha +290",
				"KN" => "Saint Kitts and Nevis +1",
				"LC" => "Saint Lucia +1",
				"MF" => "Saint Martin (French part) +590",
				"PM" => "Saint Pierre and Miquelon +508",
				"VC" => "Saint Vincent and the Grenadines +1",
				"WS" => "Samoa +685",
				"SM" => "San Marino +378",
				"ST" => "Sao Tome and Principe +239",
				"SA" => "Saudi Arabia +966",
				"SN" => "Senegal +221",
				"RS" => "Serbia +381",
				"SC" => "Seychelles +248",
				"SL" => "Sierra Leone +232",
				"SG" => "Singapore +65",
				"SX" => "Sint Maarten (Dutch part) +1",
				"SK" => "Slovakia +421",
				"SI" => "Slovenia +386",
				"SB" => "Solomon Islands +677",
				"SO" => "Somalia +252",
				"ZA" => "South Africa +27",
				"GS" => "South Georgia and the South Sandwich Islands +500",
				"SS" => "South Sudan +211",
				"ES" => "Spain +34",
				"LK" => "Sri Lanka +94",
				"SD" => "Sudan (the) +249",
				"SR" => "Suriname +597",
				"SJ" => "Svalbard and Jan Mayen +47",
				"SZ" => "Swaziland +268",
				"SE" => "Sweden +46",
				"CH" => "Switzerland +41",
				"SY" => "Syrian Arab Republic +963",
				"TW" => "Taiwan (Province of China) +886",
				"TJ" => "Tajikistan +992",
				"TZ" => "Tanzania, United Republic of +255",
				"TH" => "Thailand +66",
				"YK" => "The Republic of Kosovo +383",
				"TL" => "Timor-Leste +670",
				"TG" => "Togo +228",
				"TK" => "Tokelau +690",
				"TO" => "Tonga +676",
				"TT" => "Trinidad and Tobago +1",
				"TN" => "Tunisia +216",
				"TR" => "Turkey +90",
				"TM" => "Turkmenistan +993",
				"TC" => "Turks and Caicos Islands (the) +1",
				"TV" => "Tuvalu +688",
				"UG" => "Uganda +256",
				"UA" => "Ukraine +380",
				"AE" => "United Arab Emirates (the) +971",
				"GB" => "United Kingdom of Great Britain and Northern Irela +44",
				"UM" => "United States Minor Outlying Islands (the) +1",
				"UY" => "Uruguay +598",
				"UZ" => "Uzbekistan +998",
				"VU" => "Vanuatu +678",
				"VE" => "Venezuela (Bolivarian Republic of) +58",
				"VN" => "Viet Nam +84",
				"VG" => "Virgin Islands (British) +1",
				"VI" => "Virgin Islands (U.S.) +1",
				"WF" => "Wallis and Futuna +681",
				"EH" => "Western Sahara* +212",
				"YE" => "Yemen +967",
				"ZM" => "Zambia +260",
				"ZW" => "Zimbabwe +263");


				foreach($countries_phone as $ckey => $countryoption){

					$break = explode(" +", $countryoption);
					$ctryname = $break[0];
					$ctrynum = $break[1];

					//echo '<option value="'.ctrynum.'" selected>+'.$ctrynum.' '.$ctryname.'</option>';

					//if($ckey == $country){
					if($ckey == $phonecountry){
						//echo '<option value="'.$ckey.'" selected>'.$countryoption.'</option>';
						echo '<option value="'.$ckey.'" selected>+'.$ctrynum.' &nbsp;&nbsp;&nbsp;'.$ctryname.'</option>';
					}else{
						echo '<option value="'.$ckey.'">+'.$ctrynum.' &nbsp;&nbsp;&nbsp;'.$ctryname.'</option>';
					}
				}

			?>

                    </select>

                    <input class="form-control"
                           type="text"
                           id="phone"
                           name="phone"
			   value="<?php echo $phone;?>" oninput="onlynumbers('phone', 10);" style='width:50%;'>
                </div>
            </div>


	    <div id="spawnshipaddr">
	    </div>


            <div class="form-group">
                <label for="shippingMethod" class="col-sm-5 control-label">Shipping Method</label>
                <div class="col-sm-7" id='shippingoptions'>
                    <select class="form-control" name="shippingMethod" id="shippingMethod"  onchange="checkouttotal();">

			<?php


			foreach($shippingoptions['data'] as $shipkey => $shipoptionarray){


				$shiptime = $shipoptionarray['logisticAging'];
				$shipcost = $shipoptionarray['logisticPrice'];
				$shipnamefull = $shipoptionarray['logisticName'];
				$shipname = str_ireplace("CJPacket ", "", $shipnamefull);//i in ireplace makes it case insensitive

				//echo "Time: $shiptime<br>";
				//echo "Cost: $shipcost<br>";
				//echo "Name: $shipname<br>";
				//echo "<br><br>";

				//$shipexclude is stored in functions.php
				if(!in_array($shipnamefull, $shipexclude)){
					echo '<optgroup label="'.$shipname.'" style="font-style:normal;">';
						echo '<option value="'.$shipnamefull."#".$shipcost.'">';
						echo $shiptime.' days - $'.$shipcost.'</option>';
					echo '</optgroup>';
				}

			}




			?>


                    </select>

		    <script>
		    checkouttotal();
		    </script>

                </div>
            </div>
            <!-- Checkout Options -->
            <div class="form-group">
                <div class="col-sm-offset-5 col-sm-7">

                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-5 col-sm-7">
                    <!-- Container for PayPal Mark Checkout -->
                    <!-- Paypal Button -->
			<br><br>
                    <div id="paypalCheckoutContainer" style="width:150px;"></div>
                </div>
            </div>

                    <div id="stripe" style="width:150px;"><input type="submit" name="stripe" value="Pay with Credit Card" onsubmit="event.preventDefault();"></div>
        </form>
    </div>
</div>

<!-- Javascript Import -->
<script src="https://www.paypal.com/sdk/js?client-id=sb"></script>
<script src="<?='PayPalCheckoutServerSideV2_php/' ?>js/config.js"></script>


<!--***Important*** In order to add multiple items in the purchase_unit, you have to make sure all the numbers in "breakdown" and "items" all add up to match the value in "amount" in api/createOrder.php or else the window to initiate the transaction will fail to open.-->
<!-- See api/createOrder.php for purchase_unit components -->
<!-- PayPal In-Context Checkout script -->
<script type="text/javascript">

function showpaypalbutton(){

    paypal.Buttons({

        // Set your environment
        env: '<?= PAYPAL_ENVIRONMENT ?>',

        // Set style of button
        style: {
            layout: 'horizontal',   // horizontal | vertical
            size:   'medium',    // medium | large | responsive
            shape:  'pill',      // pill | rect
            color:  'gold',       // gold | blue | silver | black
	    label: 'buynow'  // paypal | checkout | buynow |
        },

        // Execute payment on authorize
        commit: true,

        // Wait for the PayPal button to be clicked
        createOrder: function() {

            let shippingMethodSelect = document.getElementById("shippingMethod"),
                shipmethod_cost = shippingMethodSelect.options[shippingMethodSelect.selectedIndex].value,

		emptyfields = "These fields cannot be blank:\n",

		shipinfo = shipmethod_cost.split("#"),

                shippingMethod = shipinfo[0],
                updatedShipping = shipinfo[1],


		diffaddr = document.getElementById('diffaddr'),

		phone = document.getElementById("phone").value,
		phonecountry = countryPhoneSelect.options[countryPhoneSelect.selectedIndex].value,

		phonenumber = phonecountry + "-" + phone,

                total_amt = parseFloat(<?php echo $subtotal; ?>) +
                        parseFloat(<?php echo $tax_amt; ?>) +
                        parseFloat(<?php echo $handling_fee; ?>) +
                        parseFloat(<?php echo $insurance_fee; ?>) +
                        parseFloat(updatedShipping) -
                        parseFloat(<?php echo $shipping_discount;?>),



                postData = new FormData();
                postData.append('item_amt','<?php echo $subtotal; ?>');
                postData.append('tax_amt','<?php echo $tax_amt; ?>');
                postData.append('handling_fee','<?php echo $handling_fee; ?>');
                postData.append('insurance_fee','<?php echo $insurance_fee; ?>');
                postData.append('shipping_discount','<?php echo $shipping_discount; ?>');
                postData.append('total_amt',total_amt.toFixed(2));
                postData.append('currency','USD');
                postData.append('refid',document.getElementById("reference_id").value);
                postData.append('ship_method', shippingMethod);
		postData.append('phone', phonenumber);

                postData.append('items','<?php echo $items; ?>');
                postData.append('return_url','<?= $baseUrl.URL["redirectUrls"]["returnUrl"]?>' + '?commit=true');
                postData.append('cancel_url','<?= $baseUrl.URL["redirectUrls"]["cancelUrl"]?>');





		if(diffaddr.checked){

			//alert("Use shipping address");

               	 	countrySelect = document.getElementById("countrySelect"),

			firstname = document.getElementById("first_name").value,
			lastname = document.getElementById("last_name").value,

			addrline1 = document.getElementById("line1").value,
			addrline2 = document.getElementById("line2").value,
			addrcity = document.getElementById("city").value,
			addrstate = document.getElementById("state").value,
			addrzip = document.getElementById("zip").value,

			firstnameb = document.getElementById("first_nameb").value,
			lastnameb = document.getElementById("last_nameb").value,

			addrline1b = document.getElementById("line1b").value,
			addrcityb = document.getElementById("cityb").value,
			addrstateb = document.getElementById("stateb").value,
			addrzipb = document.getElementById("zipb").value;

		}else{
			//alert("Use billing address");

               	 	countrySelect = document.getElementById("countrySelectb"),
			firstname = document.getElementById("first_nameb").value,
			lastname = document.getElementById("last_nameb").value,


			addrline1 = document.getElementById("line1b").value,
			addrline2 = document.getElementById("line2b").value,
			addrcity = document.getElementById("cityb").value,
			addrstate = document.getElementById("stateb").value,
			addrzip = document.getElementById("zipb").value,


			firstnameb = firstname,
			lastnameb = lastname,
			addrline1b = addrline1,
			addrcityb = addrcity;
			addrstateb = addrstate;
			addrzipb = addrzip;

		}





		if(isEmpty(firstname) || isEmpty(lastname) || isEmpty(addrline1) || isEmpty(addrcity) || isEmpty(firstnameb) || isEmpty(lastnameb) || isEmpty(addrline1b) || isEmpty(addrcityb) || isEmpty(addrstateb) || isEmpty(addrzipb)){

			if(diffaddr.checked){

				if(isEmpty(firstnameb)){
					emptyfields += "Billing: First Name\n";
				}


				if(isEmpty(lastnameb)){
					emptyfields += "Billing: Last Name\n";
				}



				if(isEmpty(addrline1b)){
					emptyfields += "Billing: Address Line 1\n";
				}


				if(isEmpty(addrcityb)){
					emptyfields += "Billing: City\n";
				}


				if(isEmpty(addrstateb)){
					emptyfields += "Billing: State/Province\n";
				}


				if(isEmpty(addrzipb)){
					emptyfields += "Billing: Postal Code\n";
				}





				if(isEmpty(firstname)){
					emptyfields += "Shipping: First Name\n";
				}


				if(isEmpty(lastname)){
					emptyfields += "Shipping: Last Name\n";
				}


				if(isEmpty(addrcity)){
					emptyfields += "Shipping: City\n";
				}


				if(isEmpty(addrstate)){
					emptyfields += "Shipping: State/Province\n";
				}


				if(isEmpty(addrzip)){
					emptyfields += "Shipping: Postal Code\n";
				}


			}else{
				if(isEmpty(firstname)){
					emptyfields += "First Name\n";
				}


				if(isEmpty(lastname)){
					emptyfields += "Last Name\n";
				}

				if(isEmpty(addrline1)){
					emptyfields += "Address Line 1\n";
				}

				if(isEmpty(addrcity)){
					emptyfields += "City\n";
				}


				if(isEmpty(addrstate)){
					emptyfields += "State/Province\n";
				}


				if(isEmpty(addrzip)){
					emptyfields += "Postal Code\n";
				}
			}


			alert(emptyfields);
			return;
		}

               	postData.append('shipping_line1',addrline1);
                postData.append('shipping_city',addrcity);
                postData.append('shipping_state',addrstate);
                postData.append('shipping_postal_code',addrzip);
                postData.append('shipping_country_code',countrySelect.options[countrySelect.selectedIndex].value);
		postData.append('shipping_recipient_name',firstname + " " + lastname);



            return fetch(
                '<?= URL['services']['orderCreate']?>',
                {
                    method: 'POST',
                    body: postData
                }
            ).then(function(response) {
                return response.json();
            }).then(function(resJson) {
                return resJson.data.id;
            });
        },

        // Wait for the payment to be authorized by the customer
        onApprove: function(data, actions) {
            // Capture Order
            let postData = new FormData();


            let shippingMethodSelect = document.getElementById("shippingMethod");
                shipmethod_cost = shippingMethodSelect.options[shippingMethodSelect.selectedIndex].value;

		shipinfo = shipmethod_cost.split("#");
                shippingMethod = shipinfo[0];
                updatedShipping = shipinfo[1];

	        ip = '<?php echo $ip;?>',

                total_amt = parseFloat(<?php echo $subtotal; ?>) +
                        parseFloat(<?php echo $tax_amt; ?>) +
                        parseFloat(<?php echo $handling_fee; ?>) +
                        parseFloat(<?php echo $insurance_fee; ?>) +
                        parseFloat(updatedShipping) -
                        parseFloat(<?php echo $shipping_discount;?>);



		diffaddr = document.getElementById('diffaddr');



		if(diffaddr.checked){

			usediffaddr = 'TRUE';
	    		countrySelect = document.getElementById("countrySelect");
	    		countrySelectb = document.getElementById("countrySelectb");

			shipping_line1 = document.getElementById("line1").value + '&billing:' + document.getElementById("line1b").value;
			shipping_line2 = document.getElementById("line2").value + '&billing:' + document.getElementById("line2b").value;
			shipping_city = document.getElementById("city").value + '&billing:' + document.getElementById("cityb").value;
			shipping_state = document.getElementById("state").value + '&billing:' + document.getElementById("stateb").value;
			shipping_postal_code = document.getElementById("zip").value + '&billing:' + document.getElementById("zipb").value;
			firstname = document.getElementById("first_name").value + '&billing:' + document.getElementById("first_nameb").value;
			lastname = document.getElementById("last_name").value + '&billing:' + document.getElementById("last_nameb").value;

			shipping_country_code = countrySelect.options[countrySelect.selectedIndex].value +  '&billing:' + countrySelectb.options[countrySelectb.selectedIndex].value;


		}else{
			usediffaddr = 'FALSE';
	    		countrySelect = document.getElementById("countrySelectb");

			shipping_line1 = document.getElementById("line1b").value;
			shipping_line2 = document.getElementById("line2b").value;
			shipping_city = document.getElementById("cityb").value;
			shipping_state = document.getElementById("stateb").value;
			shipping_postal_code = document.getElementById("zipb").value;
			firstname = document.getElementById("first_nameb").value;
			lastname = document.getElementById("last_nameb").value;

			shipping_country_code = countrySelect.options[countrySelect.selectedIndex].value;

		}




		addr1 = shipping_line1;
		addr2 = shipping_line2;

		accountnum = document.getElementById("accountnum").value;
		email = document.getElementById("emailaddr").value;

		phone = document.getElementById("phone").value;
		phonecountry = countryPhoneSelect.options[countryPhoneSelect.selectedIndex].value;
		phonenumber = phonecountry + "-" + phone;


            return fetch(
                '<?= URL['services']['orderCapture'] ?>',
                {
                    method: 'POST',
                    body: postData
                }
            ).then(function(res) {
                return res.json();
            }).then(function() {


		sendFetchpost('paid.php', 'paypal', usediffaddr, accountnum, <?php echo $reference_id;?>, email, firstname, lastname, addr1, addr2, shipping_city, shipping_state, shipping_country_code, shipping_postal_code, phonenumber, total_amt.toFixed(2), ip)
		//window.location.href = '?pg=purchased&email=' + escape(email) + '&firstname=' + firstname + '&lastname=' + lastname + '&ref=<?php echo $reference_id;?>' + '&total=' + total_amt + '&phone=' + phone;
		//Also set this in Stripe checkout.js in return_url line
		window.location.href = '?pg=purchased&refid=<?php echo $reference_id;?>';

            });
        }

    }).render('#paypalCheckoutContainer');
}


showpaypalbutton();


</script>

<?php

}else{//if(isset($_POST))

	echo "<a href='?pg=viewcart'>View Cart</a>";
}
?>

	</div>




</div>
</div>




</div>
</div>
