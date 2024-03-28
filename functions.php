<?php


//Derived from From paypal_transaction_search.php
//Get a json of transactions from $daysago days ago until present day
function getPaypalTransactions(string $daysago = "5", string $ppalaccessToken): string
{

    //Use for end_date
    $datetoday = date("Y-m-d")."T23:59:59-0700";;

    //echo "end_date: $datetoday<br>";

    //Use for start_date
    $datepast = date("Y-m-d", strtotime("-".$daysago." days"))."T00:00:00-0700";

    //echo "start_date: $datepast<br>";


    //echo "Getting transactions from Paypal<br>";
    // Set your PayPal API endpoint
    //$apiEndpoint = 'https://api-m.sandbox.paypal.com/v1/reporting/transactions?transaction_id=3VD971693M516431V&fields=all&page_size=100&page=1';
    //$apiEndpoint = 'https://api-m.sandbox.paypal.com/v1/reporting/transactions?start_date=2023-12-02T00:00:00-0700&end_date=2023-12-25T23:59:59-0700&fields=all&page_size=100&page=1';
    $apiEndpoint = 'https://api-m.sandbox.paypal.com/v1/reporting/transactions?start_date='.$datepast.'&end_date='.$datetoday.'&fields=all&page_size=100&page=1';


    // Set the cURL options
    $options = array(
        CURLOPT_URL            => $apiEndpoint,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING       => '',
        CURLOPT_MAXREDIRS      => 10,
        CURLOPT_TIMEOUT        => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST  => 'GET',
        CURLOPT_HTTPHEADER     => array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $ppalaccessToken,
        ),
    );

    // Initialize cURL session
    $ch = curl_init();

    // Set cURL options
    curl_setopt_array($ch, $options);

    // Execute cURL session and get the result
    $response = curl_exec($ch);

    // Check for cURL errors
    if (curl_errno($ch)) {
        echo 'cURL Error: ' . curl_error($ch);
    }

    // Close cURL session
    curl_close($ch);


    return $response;
}


//Get $ppal_trans_json from paypal_transaction_search.php
function getPaypalTransactionID(string $invoice_id, string $ppal_trans_json): string
{

	$transidvalue = "";

	$ppal_trans_json = json_decode($ppal_trans_json, true);

	foreach($ppal_trans_json['transaction_details'] as $trankey => $tranarray){

		$invoiceid = $tranarray['transaction_info']['invoice_id'];
		$tranid = $tranarray['transaction_info']['transaction_id'];

		//echo "Invoice ID: $invoiceid<br>";

		if($invoiceid == $invoice_id){
			//echo "Transaction ID: $tranid<br>";
			$transidvalue = $tranid;
		}
	}

	return $transidvalue;

}




function getpaypalaccesstoken(): array
{

    //https://developer.paypal.com/api/rest/#link-getaccesstoken

    $clientId = "";
    $clientSecret = "";


    $url = "https://api-m.sandbox.paypal.com/v1/oauth2/token";



    $curl = curl_init();

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER,
    
    true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, [
        "Content-Type: application/x-www-form-urlencoded",
        "Authorization: Basic " . base64_encode("$clientId:$clientSecret"),
    ]);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query([
        "grant_type" => "client_credentials",
    ]));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        $returnarray = array("cURL Error: $err");
    } else {
        $returnarray = json_decode($response, true);
    }

    return $returnarray;

}



function paypalsendtracking(string $accessToken, string $trackingjson): array
{
        
    // Set your PayPal API endpoint
    $apiEndpoint = 'https://api-m.sandbox.paypal.com/v1/shipping/trackers-batch';


    // Set the cURL options
    //Carriers:  https://developer.paypal.com/docs/tracking/reference/carriers/
    $options = array(
        CURLOPT_URL            => $apiEndpoint,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING       => '',
        CURLOPT_MAXREDIRS      => 10,
        CURLOPT_TIMEOUT        => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST  => 'POST',
        CURLOPT_POSTFIELDS     => '{
            "trackers": '.$trackingjson.'
        }',
        CURLOPT_HTTPHEADER     => array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $accessToken,
        ),
    );

    // Initialize cURL session
    $ch = curl_init();

    // Set cURL options
    curl_setopt_array($ch, $options);

    // Execute cURL session and get the result
    $response = curl_exec($ch);

    // Check for cURL errors
    if (curl_errno($ch)) {
        echo 'cURL Error: ' . curl_error($ch);
    }

    // Close cURL session
    curl_close($ch);

    // Output the response
    $response = json_decode($response, true);

    //echo "<pre>";
    //print_r($response);
    //echo "</pre>";

    return $response;
}





function random_str(
    $length,
    $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
) {
    $str = '';
    $max = mb_strlen($keyspace, '8bit') - 1;
    if ($max < 1) {
        throw new Exception('$keyspace must be at least two characters long');
    }
    for ($i = 0; $i < $length; ++$i) {
        $str .= $keyspace[random_int(0, $max)];
    }
    return $str;
}


//You can also get the product name from vid with curl_variantinquiry.php
//How to use:  1. include productslist.php   2. Assign getVariantName($vid, $storeproducts); to a variable
function getVariantName($the_vid, $storeproductsarray){

	$skutoget = 0;
	$varname = FALSE;

	foreach($storeproductsarray as $storkey => $storprod){
		if(preg_match("/$the_vid/", $storprod)){
			//echo "$storkey";
			$skutoget = $storkey;
		}	

	}


	if($skutoget){

		//echo "Sku to get: $skutoget";

		$variantsarray = json_decode($storeproductsarray[$skutoget], true);

		//echo "<pre>";
		//print_r($variantsarray);
		//echo "</pre>";


		foreach($variantsarray as $varkey => $vararray){


			if(isset($vararray['vid'])){

				if($vararray['vid'] == $the_vid){
					//echo $vararray['vid']."<br>";
					//echo $vararray['variantNameEn']."<br>";
					$varname = $vararray['variantNameEn'];
				}

			}else{

				if($vararray['sku_id'] == $the_vid){
					//echo $vararray['vid']."<br>";
					//echo $vararray['variantNameEn']."<br>";
					$varname = $vararray['ae_sku_property_dtos']['ae_sku_property_d_t_o'][1]['property_value_definition_name'];
				}


			}

		}
	}

	return $varname;
}



//Include productslist.php and plug in $storeproducts into $storeprodsarray
function getSKU($vid, $storeprodsarray){

	foreach($storeprodsarray as $sku => $json){
		$jsonarr = json_decode($json, true);

		//echo "<pre>";
		//print_r($jsonarr);
		//echo "</pre>";

		foreach($jsonarr as $jkey => $jsnarray){
			//echo $jsnarray['vid']."<br>";
			if($jsnarray['vid'] == $vid){
				$skustring = $jsnarray['variantSku'];
				$breakskustr = explode("-", $skustring);
				$productSKU = $breakskustr[0];
			}
		}
	}

	return $productSKU;
}



//Usage:  getSKUfromURLslug($productslugs, $url_slug);
function getSKUfromURLslug($productslugs, $url_slug){

	
	$prod_sku = "";
	$prod_sku_found = 0;

	foreach($productslugs as $sku => $prod_slug){
			//echo "$prod_slug<br>";
		if(preg_match("/".$url_slug."/i", $prod_slug) && !$prod_sku_found){
			//echo "$sku<br>";
			$prod_sku = $sku;
			$prod_sku_found = 1;
		}

	}

	return $prod_sku;

}








			$countries = array(
				"US" => "United States of America",
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
				"180" => "Congo (the Democratic Republic of OD)",
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
				"FK" => "Falkland Islands [Malvinas]",
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
				"TZ" => "Tanzania, United Republic of",
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
				"AE" => "United Arab Emirates",
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


?>