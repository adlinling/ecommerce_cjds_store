<div style="margin: 0 auto;padding:20px;text-align:left;background-color:#454545;background-image: url();">

<div style='text-align:center;background-color:#353535;padding:20px;'>
	<h1>Orders</h1>
</div>

<?php
/*
Revisions:  Removed description for each item in checkout.php so that $purchases contains only vid, quantity, price.  This will cause 
the string value of $orderinfo['data']['object']['metadata']['purchases'] below to change


*/
	if(isset($_COOKIE['session_id'])){
		//echo "Cookie:  ".$_COOKIE['session_id']."<br/>";
		$sessionid = $_COOKIE['session_id'];

	}else
	if(isset($_SESSION['session_id'])){
		//echo "Session:  $session_id";
		$sessionid = $_SESSION['session_id'];
	}


	include "dbconnect.php";
	include "productslist.php";
	include "functions.php";

	//regdate is the account number
	$query = "SELECT regdate FROM cjusers WHERE sessionid='$sessionid'";

	$result = mysqli_query($link, $query);

	while($row = mysqli_fetch_array($result)){
		$account = $row['regdate'];
		//$replycmtnotif = $row['replycmtnotif'];

	}

	mysqli_free_result($result);





//echo "Account number: $account<br>";


//$query = "SELECT refnum, ordernum, recipient, ship_addr FROM cjorders WHERE account='$account'"; //This will get incomplete orders where the webhook from payment processor has not been received
$query = "SELECT refnum, ordernum, recipient, ship_addr FROM cjorders WHERE account='$account' AND ordernum<>''";//<>'' means NOT emtpy


$result = mysqli_query($link, $query);

$refnums = array();
$ordernums = array();
$recipients = array();
$shipaddresses = array();

while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$refnums[] = $row['refnum'];
		$ordernums[] = $row['ordernum'];
		$recipients[] = $row['recipient'];
		$shipaddresses[] = $row['ship_addr'];
		//echo "<pre>";
		//print_r($row);
		//echo "</pre>";
}

mysqli_close($link);


echo "<br><br>";



if(count($refnums)){

	foreach($refnums as $refkey => $refnum){


		$subtotal = 0;
		$grandtotal = 0;




		//echo "$refkey<br>";


		//$reference_id = time() from checkout.php
		//this value is passed to the paypal webhook as $orderinfo['resource']['purchase_units'][0]['reference_id']

		$dir = "orders/";
		$file = $dir."order_".$refnum.".htm";

		$contents = file_get_contents($file);

		$contents = strip_tags($contents);

		//echo "$contents";

		$orderinfo = json_decode($contents, true);//true flag will give you arrays instead of std Objects


		//echo "<pre>";
		//print_r($orderinfo);
		//echo "</pre>";

		//https://www.php.net/manual/en/datetime.format.php
		$orderdate = date("F j, Y", $refnum);


		$recipient = $recipients[$refkey];
		$shippingaddr = $shipaddresses[$refkey];

		$addresspieces = explode(",", $shippingaddr);

		$street = $addresspieces[0];
		$city = $addresspieces[1];
		$state = $addresspieces[2];
		$zip = $addresspieces[3];
		$country = $addresspieces[4];

			$countries = array(
				"US" => "United States",
				"AF" => "Afghanistan",
				"AX" => "Åland Islands",
				"AL" => "Albania",
				"DZ" => "Algeria",
				"AS" => "American Samoa",
				"AD" => "Andorra",
				"AO" => "Angola",
				"AI" => "Anguilla",
				"AQ" => "Antarctica",
				"AG" => "Antigua and Barbuda",
				"AR" => "Argentina",
				"AM" => "Armenia",
				"AW" => "Aruba",
				"AU" => "Australia",
				"AT" => "Austria",
				"AZ" => "Azerbaijan",
				"BS" => "Bahamas",
				"BH" => "Bahrain",
				"BD" => "Bangladesh",
				"BB" => "Barbados",
				"BY" => "Belarus",
				"BE" => "Belgium",
				"BZ" => "Belize",
				"BJ" => "Benin",
				"BM" => "Bermuda",
				"BT" => "Bhutan",
				"BO" => "Bolivia",
				"BQ" => "Bonaire, Sint Eustatius and Saba",
				"BA" => "Bosnia and Herzegovina",
				"BW" => "Botswana",
				"BV" => "Bouvet Island",
				"BR" => "Brazil",
				"IO" => "British Indian Ocean Territory",
				"BN" => "Brunei Darussalam",
				"BG" => "Bulgaria",
				"BF" => "Burkina Faso",
				"BI" => "Burundi",
				"CV" => "Cabo Verde",
				"KH" => "Cambodia",
				"CM" => "Cameroon",
				"CA" => "Canada",
				"KY" => "Cayman Islands",
				"CF" => "Central African Republic",
				"TD" => "Chad",
				"CL" => "Chile",
				"CN" => "China",
				"CX" => "Christmas Island",
				"CC" => "Cocos (Keeling) Islands",
				"CO" => "Colombia",
				"KM" => "Comoros",
				"180" => "Congo (the Democratic Republic of OD",
				"CG" => "Congo",
				"CK" => "Cook Islands",
				"CR" => "Costa Rica",
				"CI" => "Côte d'Ivoire",
				"HR" => "Croatia",
				"CU" => "Cuba",
				"CW" => "Curaçao",
				"CY" => "Cyprus",
				"CZ" => "Czechia",
				"DK" => "Denmark",
				"DJ" => "Djibouti",
				"DM" => "Dominica",
				"DO" => "Dominican Republic",
				"EC" => "Ecuador",
				"EG" => "Egypt",
				"SV" => "El Salvador",
				"GQ" => "Equatorial Guinea",
				"ER" => "Eritrea",
				"EE" => "Estonia",
				"ET" => "Ethiopia",
				"FK" => "Falkland Islands",
				"FO" => "Faroe Islands",
				"FJ" => "Fiji",
				"FI" => "Finland",
				"FR" => "France",
				"GF" => "French Guiana",
				"PF" => "French Polynesia",
				"TF" => "French Southern Territories",
				"GA" => "Gabon",
				"GM" => "Gambia",
				"GE" => "Georgia",
				"DE" => "Germany",
				"GH" => "Ghana",
				"GI" => "Gibraltar",
				"GR" => "Greece",
				"GL" => "Greenland",
				"GD" => "Grenada",
				"GP" => "Guadeloupe",
				"GU" => "Guam",
				"GT" => "Guatemala",
				"GG" => "Guernsey",
				"GN" => "Guinea",
				"GW" => "Guinea-Bissau",
				"GY" => "Guyana",
				"HT" => "Haiti",
				"HM" => "Heard Island and McDonald Islands",
				"VA" => "Holy See",
				"HN" => "Honduras",
				"HK" => "Hong Kong",
				"HU" => "Hungary",
				"IS" => "Iceland",
				"IN" => "India",
				"ID" => "Indonesia",
				"IR" => "Iran",
				"IQ" => "Iraq",
				"IE" => "Ireland",
				"IM" => "Isle of Man",
				"IL" => "Israel",
				"IT" => "Italy",
				"JM" => "Jamaica",
				"JP" => "Japan",
				"JE" => "Jersey",
				"JO" => "Jordan",
				"KZ" => "Kazakhstan",
				"KE" => "Kenya",
				"KI" => "Kiribati",
				"KP" => "North Korea",
				"KR" => "South Korea",
				"KW" => "Kuwait",
				"KG" => "Kyrgyzstan",
				"LA" => "Lao People's Democratic Republic",
				"LV" => "Latvia",
				"LB" => "Lebanon",
				"LS" => "Lesotho",
				"LR" => "Liberia",
				"LY" => "Libya",
				"LI" => "Liechtenstein",
				"LT" => "Lithuania",
				"LU" => "Luxembourg",
				"MO" => "Macao",
				"MK" => "Macedonia",
				"MG" => "Madagascar",
				"MW" => "Malawi",
				"MY" => "Malaysia",
				"MV" => "Maldives",
				"ML" => "Mali",
				"MT" => "Malta",
				"MH" => "Marshall Islands",
				"MQ" => "Martinique",
				"MR" => "Mauritania",
				"MU" => "Mauritius",
				"YT" => "Mayotte",
				"MX" => "Mexico",
				"FM" => "Micronesia",
				"MD" => "Moldova",
				"MC" => "Monaco",
				"MN" => "Mongolia",
				"ME" => "Montenegro",
				"MS" => "Montserrat",
				"MA" => "Morocco",
				"MZ" => "Mozambique",
				"MM" => "Myanmar",
				"NA" => "Namibia",
				"NR" => "Nauru",
				"NP" => "Nepal",
				"NL" => "Netherlands",
				"NC" => "New Caledonia",
				"NZ" => "New Zealand",
				"NI" => "Nicaragua",
				"NE" => "Niger",
				"NG" => "Nigeria",
				"NU" => "Niue",
				"NF" => "Norfolk Island",
				"MP" => "Northern Mariana Islands",
				"NO" => "Norway",
				"OM" => "Oman",
				"PK" => "Pakistan",
				"PW" => "Palau",
				"PS" => "Palestine, State of",
				"PA" => "Panama",
				"PG" => "Papua New Guinea",
				"PY" => "Paraguay",
				"PE" => "Peru",
				"PH" => "Philippines",
				"PN" => "Pitcairn",
				"PL" => "Poland",
				"PT" => "Portugal",
				"PR" => "Puerto Rico",
				"QA" => "Qatar",
				"RE" => "Réunion",
				"RO" => "Romania",
				"RU" => "Russian Federation",
				"RW" => "Rwanda",
				"BL" => "Saint Barthélemy",
				"SH" => "Saint Helena, Ascension and Tristan da Cunha",
				"KN" => "Saint Kitts and Nevis",
				"LC" => "Saint Lucia",
				"MF" => "Saint Martin (French part)",
				"PM" => "Saint Pierre and Miquelon",
				"VC" => "Saint Vincent and the Grenadines",
				"WS" => "Samoa",
				"SM" => "San Marino",
				"ST" => "Sao Tome and Principe",
				"SA" => "Saudi Arabia",
				"SN" => "Senegal",
				"RS" => "Serbia",
				"SC" => "Seychelles",
				"SL" => "Sierra Leone",
				"SG" => "Singapore",
				"SX" => "Sint Maarten (Dutch part)",
				"SK" => "Slovakia",
				"SI" => "Slovenia",
				"SB" => "Solomon Islands",
				"SO" => "Somalia",
				"ZA" => "South Africa",
				"GS" => "South Georgia and the South Sandwich Islands",
				"SS" => "South Sudan",
				"ES" => "Spain",
				"LK" => "Sri Lanka",
				"SD" => "Sudan",
				"SR" => "Suriname",
				"SJ" => "Svalbard and Jan Mayen",
				"SZ" => "Swaziland",
				"SE" => "Sweden",
				"CH" => "Switzerland",
				"SY" => "Syrian Arab Republic",
				"TW" => "Taiwan",
				"TJ" => "Tajikistan",
				"TZ" => "Tanzania",
				"TH" => "Thailand",
				"YK" => "The Republic of Kosovo",
				"TL" => "Timor-Leste",
				"TG" => "Togo",
				"TK" => "Tokelau",
				"TO" => "Tonga",
				"TT" => "Trinidad and Tobago",
				"TN" => "Tunisia",
				"TR" => "Turkey",
				"TM" => "Turkmenistan",
				"TC" => "Turks and Caicos Islands",
				"TV" => "Tuvalu",
				"UG" => "Uganda",
				"UA" => "Ukraine",
				"AE" => "United Arab Emirates(the)",
				"GB" => "United Kingdom of Great Britain and Northern Irela",
				"UM" => "United States Minor Outlying Islands",
				"UY" => "Uruguay",
				"UZ" => "Uzbekistan",
				"VU" => "Vanuatu",
				"VE" => "Venezuela",
				"VN" => "Viet Nam",
				"VG" => "Virgin Islands (British)",
				"VI" => "Virgin Islands (U.S.)",
				"WF" => "Wallis and Futuna",
				"EH" => "Western Sahara*",
				"YE" => "Yemen",
				"ZM" => "Zambia",
				"ZW" => "Zimbabwe"
			);


		echo "<div class='orders-top'>";


		echo "<div class='orders-top-elements'>";
		echo "Order Date:<br><br>";
		echo "Ref#: <br><br>";
		echo "Ship To:";
		echo "</div>";

		echo "<div class='orders-top-elements'>";
		echo "$orderdate<br><br>";

		echo "$refnum<br><br>";
		echo "$recipient<br>";
		echo "$street<br>";
		echo "$city, $state $zip<br>";
		echo $countries[$country];
		echo "</div>";

		echo "<div class='orders-top-elements'>";
		echo "</div>";

		echo "<div class='orders-top-elements'>";
		echo "</div>";


		
		echo "</div>";





		echo "<div class='orders-container' >";

		$ordernumber = $ordernums[$refkey];

		//echo "Order number: ".$ordernumber."<br><br>";



		?>

		<div class="order-item"></div>
		<div class="order-item"></div>
 		<div class="order-item">Quantity</div>
 		<div class="order-item">Price Ea.</div>
		<div class="order-item">Total</div>
		</div>



		<?php

		$paymethod = "";
		$shipping = "";

		echo "<div class='orders-container' >";

		//stripe vs paypal
		if(isset($orderinfo['resource'])){//Paypal

			$paymethod = "paypal";
			$items = $orderinfo['resource']['purchase_units'][0]['items'];
			$shipping = $orderinfo['resource']['purchase_units'][0]['amount']['breakdown']['shipping']['value'];
		}


		if(isset($orderinfo['data'])){//Stripe
			$paymethod = "stripe";
			//$stripepurchases = $orderinfo->data->object->metadata->purchases;
			$itemsbought = $orderinfo['data']['object']['metadata']['purchases'];
			$shipping = $orderinfo['data']['object']['metadata']['shipping'];
			$items = explode("&", $itemsbought);

		}


		foreach($items as $itemkey => $item){

			if($paymethod == "paypal"){
				$price = $item['unit_amount']['value'];
				$quantity = $item['quantity'];
				$itemname = $item['name'];
			}else{

				$breakitem = explode("#", $item);				
				$price = $breakitem[2];
				$quantity = $breakitem[1];
				$itemname = getVariantName($breakitem[0], $storeproducts);//function stored in functions.php
			}

			$itemnameshort = substr($itemname, 0, 40);

			foreach($products as $prodkey => $product){
				$productname = $product['title'];

				if(preg_match("/$itemnameshort/i", $productname)){
					//echo "$prodkey $productname<br>";
					$sku = $prodkey;
				}
			}


			$itemtotal = $price*$quantity;

			$subtotal = $subtotal + $itemtotal;

			echo "<div class='order-item'><a href='?pg=store&sku=$sku'><img src='' width='100' height='100'></a></div>";
			echo "<div class='order-item'><a href='?pg=store&sku=$sku'>$itemname</a></div>";
			echo "<div class='order-item' id='purchquantity'>".$quantity."</div>";
			echo "<div class='order-item' id='purchprice'>$".$price."</div>";
			echo "<div class='order-item' id='purchtotal'>\$".$itemtotal."</div>";
			//echo $item['sku']."<br><br>";
		}

		//echo "<br><br>";
		//echo "<pre>";
		//print_r($orderinfo);
		//echo "</pre>";


			echo "<div class='order-item' id='trackorder'><a href='?pg=track&ordernum=$ordernumber'>Track Order</a></div>";
			echo "<div class='order-item'></div>";
			echo "<div class='order-item'></div>";
			echo "<div class='order-item'>Subtotal</div>";
			echo "<div class='order-item'>\$$subtotal</div>";

			echo "<div class='order-item' id='invoice'><a href='invoice.php?refnum=$refnum&ordernum=$ordernumber'>Invoice</a></div>";
			echo "<div class='order-item'></div>";
			echo "<div class='order-item'></div>";
			echo "<div class='order-item'>Shipping</div>";
			echo "<div class='order-item'>\$$shipping</div>";

			$grandtotal = $subtotal + $shipping;

			echo "<div class='order-item'></div>";
			echo "<div class='order-item'></div>";
			echo "<div class='order-item'></div>";
			echo "<div class='order-item'>Grand Total</div>";
			echo "<div class='order-item'>\$$grandtotal</div>";

		echo "</div>";

		echo "<br><br><br>";


		}
}else{
	echo "No orders found.";


	for($i=0;$i<30;$i++){
		echo "<br/>";
	}

}




?>

</div>

