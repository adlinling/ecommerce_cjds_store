<?php
/*
Revisions:  See revision notes in purchases_10.php
/*
?>

<style>

.invoice-container{
  display:block;
  #background-color:#293829;
  margin:20px;
}


.invoice-top{
  text-align:left;
  #border: 1px #298392 solid;
}


.invoice-title{
  text-align:center;
}



.invoice-shipdate, .payment-title{
  text-align:center;
  border: 2px #392839 solid;
  padding:10px;
}

.invoice-items{
  display:flex;
  #background-color:#583929;
  border-left: 2px #583929 solid;
  border-right: 2px #583929 solid;
  justify-content:space-between;
}


.invoice-items-left{
  text-align:left
  #background-color:#583929;
  padding:10px;
  flex:50;
}


.invoice-items-quantity{
  text-align: center;
  #background-color:#583929;
  padding:10px;
  flex:1;
}




.invoice-items-price{
  text-align:center;
  #background-color:#583929;
  padding:10px;
  flex:2;
}



.invoice-shipaddr{
  text-align:left;
  #background-color:#492839;
  border: 2px #583929 solid;
  margin-bottom:30px;
  padding:10px;
}


.invoice-payment{
  display:flex;
  border-left: 2px #392839 solid;
  border-right: 2px #392839 solid;
  border-bottom: 2px #392839 solid;
  justify-content: space-between;

}


.billing{
  padding:10px;
  flex:4;
}

.payment-totals-labels{
  text-align:right;
  flex:30;
  padding:10px;
}

.payment-totals{
  padding:10px;
  text-align:right;
  flex:1;
}

</style>


<?php

include "productslist.php";
include "functions.php";


$refnum = isset($_GET['refnum'])?$_GET['refnum']:"";
$ordernum = isset($_GET['ordernum'])?$_GET['ordernum']:"";



$orderdate = date("F j, Y", $refnum);


//echo "Ship To: $recipient $shipaddr<br>";


		$subtotal = 0;
		$grandtotal = 0;
		$paymethod = "";

		$dir = "orders/";
		$file = $dir."order_".$refnum.".htm";

		$contents = file_get_contents($file);

		$contents = strip_tags($contents);

		//echo "$contents";

		$orderinfo = json_decode($contents, true);

		if(isset($orderinfo['resource'])){//Paypal

			$payprocessor = "paypal";
			$grandtotal = $orderinfo['resource']['purchase_units'][0]['amount']['value'];
			$subtotal = $orderinfo['resource']['purchase_units'][0]['amount']['breakdown']['item_total']['value'];
			$shippingcost = $orderinfo['resource']['purchase_units'][0]['amount']['breakdown']['shipping']['value'];
			$items = $orderinfo['resource']['purchase_units'][0]['items'];


			$recipient = $orderinfo['resource']['purchase_units'][0]['shipping']['name']['full_name'];
			$street = $orderinfo['resource']['purchase_units'][0]['shipping']['address']['address_line_1'];
			$city = $orderinfo['resource']['purchase_units'][0]['shipping']['address']['admin_area_2'];
			$state = $orderinfo['resource']['purchase_units'][0]['shipping']['address']['admin_area_2'];
			$zip = $orderinfo['resource']['purchase_units'][0]['shipping']['address']['postal_code'];
			$country = $orderinfo['resource']['purchase_units'][0]['shipping']['address']['country_code'];

			$custom_id = $orderinfo['resource']['purchase_units'][0]['custom_id'];
			$breakcustom = explode("-", $custom_id);
			$shippingmethod = $breakcustom[0];
		}


		if(isset($orderinfo['data'])){//Stripe

			$payprocessor = "stripe";
			$subtotal = $orderinfo['data']['object']['metadata']['subtotal'];
			$shippingcost = $orderinfo['data']['object']['metadata']['shipping'];
			$grandtotal = $subtotal + $shippingcost;
			$itemsbought = $orderinfo['data']['object']['metadata']['purchases'];
			$items = explode("&", $itemsbought);

			$diffaddr = $orderinfo['data']['object']['metadata']['diffaddr'];

			if($diffaddr){

				$firstname = $orderinfo['data']['object']['metadata']['firstname'];
				$lastname = $orderinfo['data']['object']['metadata']['lastname'];

				$street = $orderinfo['data']['object']['metadata']['line1']." ".$orderinfo['data']['object']['metadata']['line2'];
				$city = $orderinfo['data']['object']['metadata']['city'];
				$state = $orderinfo['data']['object']['metadata']['state'];
				$zip = $orderinfo['data']['object']['metadata']['zip'];
				$country = $orderinfo['data']['object']['metadata']['country'];
			}else{
				$firstname = $orderinfo['data']['object']['metadata']['firstnameb'];
				$lastname = $orderinfo['data']['object']['metadata']['lastnameb'];

				$street = $orderinfo['data']['object']['metadata']['line1b']." ".$orderinfo['data']['object']['metadata']['line2b'];
				$city = $orderinfo['data']['object']['metadata']['cityb'];
				$state = $orderinfo['data']['object']['metadata']['stateb'];
				$zip = $orderinfo['data']['object']['metadata']['zipb'];
				$country = $orderinfo['data']['object']['metadata']['countryb'];

			}


			$recipient = $firstname." ".$lastname;
			$shippingmethod = $orderinfo['data']['object']['metadata']['shippingmethod'];

		}

		//echo "<pre>";
		//print_r($orderinfo);
		//echo "</pre>";


?>

<div class="invoice-container">



<div class="invoice-title">
Ecommerestore.com<br>
123 Street<br>
Big City, State, 39283<br><br>


Invoice for Order #<?php echo $ordernum;?><br>
Print this page for your records<br><br>
</div>



<div class="invoice-top">
<?php
echo "Order Date:  $orderdate<br>";


include "dbconnect_prepstmt.php";


$sql = "SELECT * FROM cjorders WHERE refnum=?";
$stmt = $conn->prepare($sql); 
$stmt->bind_param("s", $refnum);
$stmt->execute();

$result = $stmt->get_result();


$data = $result->fetch_all(MYSQLI_ASSOC);



$stmt->close();
$conn->close();


//echo "<pre>";
//print_r($data);
//echo "</pre>";

$buyer = $data[0]['buyer'];
$paymethod = $data[0]['paymethod'];
$addr_bill_pieces = explode(",", $data[0]['bill_addr']);

$street_bill = $addr_bill_pieces[0];
$city_bill = $addr_bill_pieces[1];
$state_bill = $addr_bill_pieces[2];
$zip_bill = $addr_bill_pieces[3];
$country_bill = $addr_bill_pieces[4];


//echo "Buyer: $buyer<br>";


?>
</div>


<div class="invoice-shipdate">
<b>Order Details</b>
</div>




<div class="invoice-items">

<div class="invoice-items-left">
<b>Items Ordered:</b><br>

<?php
foreach($items as $itkey => $item){

	if($payprocessor == "paypal"){
		$itemname = $item['name'];
		//$itemprice = $item['unit_amount']['value'];
		//$itemquantity = $item['quantity'];
	}else{

		$breakitem = explode("#", $item);	
		$itemname = getVariantName($breakitem[0], $storeproducts);
		//$itemquantity = $breakitem[2];
	}

	//echo "Item: $itemname<br>";
	echo "<i>$itemname</i><br><br>";

}
?>
</div>


<div class="invoice-items-quantity">
<b>Quantity</b><br>
<?php


foreach($items as $itkey => $item){

	if($payprocessor == "paypal"){
		//$itemname = $item['name'];
		//$itemprice = $item['unit_amount']['value'];
		$itemquantity = $item['quantity'];
	}else{
		$breakitem = explode("#", $item);
		$itemquantity = $breakitem[1];
	}

	echo "$itemquantity<br><br>";
}

?>

</div>




<div class="invoice-items-price">
<b>Price Ea.</b><br>
<?php


foreach($items as $itkey => $item){

	if($payprocessor == "paypal"){
		//$itemname = $item['name'];
		$itemprice = $item['unit_amount']['value'];
		//$itemquantity = $item['quantity'];
	}else{
		$breakitem = explode("#", $item);
		$itemprice = $breakitem[2];
	}
	echo "\$$itemprice<br><br>";
}

?>

</div>



</div>


<div class="invoice-shipaddr">

<?php
echo "<b>Shipping Address:</b><br>";
echo "$recipient<br>";
echo "$street<br>";
echo "$city, $state $zip<br>";



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


echo $countries[$country]."<br><br>";

echo "<b>Shipping Method:</b><br> $shippingmethod<br>";
echo "<br><br>";

?>
</div>



<div class="payment-title">
<b>Payment Information</b>
</div>

<div class="invoice-payment">


<div class="billing">
<b>Payment Method:</b> <?php echo ucwords($paymethod);?><br><br>
<b>Billing Address:</b><br>
<?php

echo "$buyer<br>";
echo "$street_bill<br>";
echo "$city_bill, $state_bill $zip_bill<br>";
echo $countries[$country_bill]."<br>";

?>
</div>



<div class="payment-totals-labels">

<?php
echo "Subtotal:<br>";
echo "Shipping:<br>";
echo "<br>";
echo "<b>Grand Total:</b>";
?>
</div>


<div class="payment-totals">

<?php
echo "\$$subtotal<br>";
echo "\$$shippingcost<br>";
echo "---------<br>";
echo "<b>\$$grandtotal</b>";
?>
</div>

</div>







</div>

