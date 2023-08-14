
function updateProdctInfo() {

  var variant = document.myForm.variant.value;

  if ((variant == null) || (variant == "")) return;

  var pieces = variant.split("#");	

  var vid = pieces[0];
  var price = pieces[1];

  var file = "get_cjinfo.php?vid=" + escape(vid) + "&price=" + escape(price);
  //var file = "test.php";


  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
     document.getElementById("myDiv").innerHTML = this.responseText;
    }
  };
  xhttp.open("GET", file, true);
  xhttp.send();

}


function validate(refid, ip) {


            let shippingMethodSelect = document.getElementById("shippingMethod");
                shipmethod_cost = shippingMethodSelect.options[shippingMethodSelect.selectedIndex].value;
		subtotal = document.getElementById("subtotal").innerHTML;

		shipinfo = shipmethod_cost.split("#");

                shippingMethod = shipinfo[0];
                updatedShipping = shipinfo[1];


		grandtotal = parseFloat(subtotal) + parseFloat(updateShipping);

		var usediffaddr;
		var stopsubmit = 0;
		var emptyfields = "These fields cannot be blank:\n";
		const diffaddr = document.getElementById('diffaddr');
		

		if(diffaddr.checked){
			usediffaddr = 'TRUE';

	    		countrySelect = document.getElementById("countrySelect");
	    		countrySelectb = document.getElementById("countrySelectb");

			line1 = document.getElementById("line1").value;

			if(isEmpty(line1)){
				emptyfields += "Shipping: Address Line 1\n";
				stopsubmit = 1;
			}

			line1b = document.getElementById("line1b").value;

			if(isEmpty(line1b)){
				emptyfields += "Billing: Address Line 1\n";
				stopsubmit = 1;
			}

			line2 = document.getElementById("line2").value;
			line2b = document.getElementById("line2b").value;

			city = document.getElementById("city").value;

			if(isEmpty(city)){
				emptyfields += "Shipping: City\n";
				stopsubmit = 1;
			}

			cityb = document.getElementById("cityb").value;

			if(isEmpty(cityb)){
				emptyfields += "Billing: City\n";
				stopsubmit = 1;
			}


			state = document.getElementById("state").value;


			if(isEmpty(state)){
				emptyfields += "Shipping: State/Province\n";
				stopsubmit = 1;
			}


			stateb = document.getElementById("staeb").value;

			if(isEmpty(stateb)){
				emptyfields += "Billing: State/Province\n";
				stopsubmit = 1;
			}


			zip = document.getElementById("zip").value;

			if(isEmpty(zip)){
				emptyfields += "Shipping: Postal Code\n";
				stopsubmit = 1;
			}


			zipb = document.getElementById("zipb").value;

			if(isEmpty(zipb)){
				emptyfields += "Billing: Postal Code\n";
				stopsubmit = 1;
			}


			fname = document.getElementById("first_name").value;

			if(isEmpty(fname)){
				emptyfields += "Shipping: First Name\n";
				stopsubmit = 1;
			}

			fnameb = document.getElementById("first_nameb").value;

			if(isEmpty(fnameb)){
				emptyfields += "Billing: First Name\n";
				stopsubmit = 1;
			}



			lname = document.getElementById("last_name").value;

			if(isEmpty(lname)){
				emptyfields += "Shipping: Last Name\n";
				stopsubmit = 1;
			}

			lnameb = document.getElementById("last_nameb").value;

			if(isEmpty(lnameb)){
				emptyfields += "Billing: Last Name\n";
				stopsubmit = 1;
			}



			shipping_line1 = line1 + '&billing:' + line1b;
			shipping_line2 = line2 + '&billing:' + line2b;
			shipping_city = city + '&billing:' + cityb;
			shipping_state = state + '&billing:' + stateb;
			shipping_postal_code = zip + '&billing:' + zipb;
			firstname = fname + '&billing:' + fnameb;
			lastname = lname + '&billing:' + lnameb;

			shipping_country_code = country +  '&billing:' + countryb;

		}else{
			usediffaddr = 'FALSE';

			countrySelect = document.getElementById("countrySelectb");

			line1b = document.getElementById("line1b").value;

			if(isEmpty(line1b)){
				emptyfields += "Address Line 1\n";
				stopsubmit = 1;
			}


			line2b = document.getElementById("line2b").value;

			cityb = document.getElementById("cityb").value;

			if(isEmpty(cityb)){
				emptyfields += "City\n";
				stopsubmit = 1;
			}



			zipb = document.getElementById("zipb").value;

			if(isEmpty(zipb)){
				emptyfields += "Postal Code\n";
				stopsubmit = 1;
			}

			fnameb = document.getElementById("first_nameb").value;


			if(isEmpty(fnameb)){
				emptyfields += "First Name\n";
				stopsubmit = 1;
			}

			lnameb = document.getElementById("last_nameb").value;

			if(isEmpty(lnameb)){
				emptyfields += "Last Name\n";
				stopsubmit = 1;
			}


			country = countrySelect.options[countrySelect.selectedIndex].value;


			shipping_line1 = line1b;
			shipping_line2 = line2b;
			shipping_city = cityb;
			shipping_state = stateb;
			shipping_postal_code = zipb;

			firstname = fnameb;
			lastname = lnameb;

			shipping_country_code = country;

		}


		addr1 = shipping_line1;
		addr2 = shipping_line2;

		accountnum = document.getElementById("accountnum").value;
		email = document.getElementById("emailaddr").value;

		phone = document.getElementById("phone").value;
		phonecountry = countryPhoneSelect.options[countryPhoneSelect.selectedIndex].value;
		phonenumber = phonecountry + "-" + phone;

		total_amt = grandtotal;

		if(stopsubmit){
			alert(emptyfields);
			return false;
		}else{
			//alert(firstname + "\n" + lastname + "\n" + shipping_line1 + "\n" + shipping_line2 + "\n" + shipping_city + "\n" + shipping_state + "\n" + shipping_postal_code + "\n" + "\nCountry: " + shipping_country_code + "\nDifferenet Addr: " + usediffaddr + "\nReference ID: " + refid + "\nAccount Number: " + accountnum + "\nPhone: " + phonenumber + "\n" + email);
			sendFetchpost('paid.php', 'Credit Card', usediffaddr, accountnum, refid, email, firstname, lastname, addr1, addr2, shipping_city, shipping_state, shipping_country_code, shipping_postal_code, phonenumber, total_amt.toFixed(2), ip)
		}

    return true;

}


//from https://stackoverflow.com/questions/3937513/javascript-validation-for-empty-input-field
function isEmpty(str) {
    return !str.trim().length;
}


function spwnshipaddr(jsonurl){

  //for multiple checkboxes, uses getElementsByClassName https://stackoverflow.com/questions/11599666/get-the-value-of-checked-checkbox

  var diffaddr = document.getElementById('diffaddr');
  var shippingform = "";
  var country = "US";
  var state = "AZ";


  var countries = {
		"US": "United States of America",
		"AF": "Afghanistan",
		"AX": "Åland Islands",
		"AL": "Albania",
		"DZ": "Algeria",
		"AS": "American Samoa",
		"AD": "Andorra",
		"AO": "Angola",
		"AI": "Anguilla",
		"AQ": "Antarctica",
		"AG": "Antigua and Barbuda",
		"AR": "Argentina",
		"AM": "Armenia",
		"AW": "Aruba",
		"AU": "Australia",
		"AT": "Austria",
		"AZ": "Azerbaijan",
		"BS": "Bahamas",
		"BH": "Bahrain",
		"BD": "Bangladesh",
		"BB": "Barbados",
		"BY": "Belarus",
		"BE": "Belgium",
		"BZ": "Belize",
		"BJ": "Benin",
		"BM": "Bermuda",
		"BT": "Bhutan",
		"BO": "Bolivia",
		"BQ": "Bonaire, Sint Eustatius and Saba",
		"BA": "Bosnia and Herzegovina",
		"BW": "Botswana",
		"BV": "Bouvet Island",
		"BR": "Brazil",
		"IO": "British Indian Ocean Territory",
		"BN": "Brunei Darussalam",
		"BG": "Bulgaria",
		"BF": "Burkina Faso",
		"BI": "Burundi",
		"CV": "Cabo Verde",
		"KH": "Cambodia",
		"CM": "Cameroon",
		"CA": "Canada",
		"KY": "Cayman Islands",
		"CF": "Central African Republic",
		"TD": "Chad",
		"CL": "Chile",
		"CN": "China",
		"CX": "Christmas Island",
		"CC": "Cocos (Keeling) Islands",
		"CO": "Colombia",
		"KM": "Comoros",
		"180": "Congo (the Democratic Republic of OD)",
		"CG": "Congo",
		"CK": "Cook Islands",
		"CR": "Costa Rica",
		"CI": "Côte d'Ivoire",
		"HR": "Croatia",
		"CU": "Cuba",
		"CW": "Curaçao",
		"CY": "Cyprus",
		"CZ": "Czechia",
		"DK": "Denmark",
		"DJ": "Djibouti",
		"DM": "Dominica",
		"DO": "Dominican Republic",
		"EC": "Ecuador",
		"EG": "Egypt",
		"SV": "El Salvador",
		"GQ": "Equatorial Guinea",
		"ER": "Eritrea",
		"EE": "Estonia",
		"ET": "Ethiopia",
		"FK": "Falkland Islands [Malvinas]",
		"FO": "Faroe Islands",
		"FJ": "Fiji",
		"FI": "Finland",
		"FR": "France",
		"GF": "French Guiana",
		"PF": "French Polynesia",
		"TF": "French Southern Territories",
		"GA": "Gabon",
		"GM": "Gambia",
		"GE": "Georgia",
		"DE": "Germany",
		"GH": "Ghana",
		"GI": "Gibraltar",
		"GR": "Greece",
		"GL": "Greenland",
		"GD": "Grenada",
		"GP": "Guadeloupe",
		"GU": "Guam",
		"GT": "Guatemala",
		"GG": "Guernsey",
		"GN": "Guinea",
		"GW": "Guinea-Bissau",
		"GY": "Guyana",
		"HT": "Haiti",
		"HM": "Heard Island and McDonald Islands",
		"VA": "Holy See",
		"HN": "Honduras",
		"HK": "Hong Kong",
		"HU": "Hungary",
		"IS": "Iceland",
		"IN": "India",
		"ID": "Indonesia",
		"IR": "Iran",
		"IQ": "Iraq",
		"IE": "Ireland",
		"IM": "Isle of Man",
		"IL": "Israel",
		"IT": "Italy",
		"JM": "Jamaica",
		"JP": "Japan",
		"JE": "Jersey",
		"JO": "Jordan",
		"KZ": "Kazakhstan",
		"KE": "Kenya",
		"KI": "Kiribati",
		"KP": "North Korea",
		"KR": "South Korea",
		"KW": "Kuwait",
		"KG": "Kyrgyzstan",
		"LA": "Lao People's Democratic Republic",
		"LV": "Latvia",
		"LB": "Lebanon",
		"LS": "Lesotho",
		"LR": "Liberia",
		"LY": "Libya",
		"LI": "Liechtenstein",
		"LT": "Lithuania",
		"LU": "Luxembourg",
		"MO": "Macao",
		"MK": "Macedonia",
		"MG": "Madagascar",
		"MW": "Malawi",
		"MY": "Malaysia",
		"MV": "Maldives",
		"ML": "Mali",
		"MT": "Malta",
		"MH": "Marshall Islands",
		"MQ": "Martinique",
		"MR": "Mauritania",
		"MU": "Mauritius",
		"YT": "Mayotte",
		"MX": "Mexico",
		"FM": "Micronesia",
		"MD": "Moldova",
		"MC": "Monaco",
		"MN": "Mongolia",
		"ME": "Montenegro",
		"MS": "Montserrat",
		"MA": "Morocco",
		"MZ": "Mozambique",
		"MM": "Myanmar",
		"NA": "Namibia",
		"NR": "Nauru",
		"NP": "Nepal",
		"NL": "Netherlands",
		"NC": "New Caledonia",
		"NZ": "New Zealand",
		"NI": "Nicaragua",
		"NE": "Niger",
		"NG": "Nigeria",
		"NU": "Niue",
		"NF": "Norfolk Island",
		"MP": "Northern Mariana Islands",
		"NO": "Norway",
		"OM": "Oman",
		"PK": "Pakistan",
		"PW": "Palau",
		"PS": "Palestine, State of",
		"PA": "Panama",
		"PG": "Papua New Guinea",
		"PY": "Paraguay",
		"PE": "Peru",
		"PH": "Philippines",
		"PN": "Pitcairn",
		"PL": "Poland",
		"PT": "Portugal",
		"PR": "Puerto Rico",
		"QA": "Qatar",
		"RE": "Réunion",
		"RO": "Romania",
		"RU": "Russian Federation",
		"RW": "Rwanda",
		"BL": "Saint Barthélemy",
		"SH": "Saint Helena, Ascension and Tristan da Cunha",
		"KN": "Saint Kitts and Nevis",
		"LC": "Saint Lucia",
		"MF": "Saint Martin (French part)",
		"PM": "Saint Pierre and Miquelon",
		"VC": "Saint Vincent and the Grenadines",
		"WS": "Samoa",
		"SM": "San Marino",
		"ST": "Sao Tome and Principe",
		"SA": "Saudi Arabia",
		"SN": "Senegal",
		"RS": "Serbia",
		"SC": "Seychelles",
		"SL": "Sierra Leone",
		"SG": "Singapore",
		"SX": "Sint Maarten (Dutch part)",
		"SK": "Slovakia",
		"SI": "Slovenia",
		"SB": "Solomon Islands",
		"SO": "Somalia",
		"ZA": "South Africa",
		"GS": "South Georgia and the South Sandwich Islands",
		"SS": "South Sudan",
		"ES": "Spain",
		"LK": "Sri Lanka",
		"SD": "Sudan",
		"SR": "Suriname",
		"SJ": "Svalbard and Jan Mayen",
		"SZ": "Swaziland",
		"SE": "Sweden",
		"CH": "Switzerland",
		"SY": "Syrian Arab Republic",
		"TW": "Taiwan",
		"TJ": "Tajikistan",
		"TZ": "Tanzania, United Republic of",
		"TH": "Thailand",
		"YK": "The Republic of Kosovo",
		"TL": "Timor-Leste",
		"TG": "Togo",
		"TK": "Tokelau",
		"TO": "Tonga",
		"TT": "Trinidad and Tobago",
		"TN": "Tunisia",
		"TR": "Turkey",
		"TM": "Turkmenistan",
		"TC": "Turks and Caicos Islands",
		"TV": "Tuvalu",
		"UG": "Uganda",
		"UA": "Ukraine",
		"AE": "United Arab Emirates",
		"GB": "United Kingdom of Great Britain and Northern Irela",
		"UM": "United States Minor Outlying Islands",
		"UY": "Uruguay",
		"UZ": "Uzbekistan",
		"VU": "Vanuatu",
		"VE": "Venezuela",
		"VN": "Viet Nam",
		"VG": "Virgin Islands (British)",
		"VI": "Virgin Islands (U.S.)",
		"WF": "Wallis and Futuna",
		"EH": "Western Sahara*",
		"YE": "Yemen",
		"ZM": "Zambia",
		"ZW": "Zimbabwe"
	};



  if(diffaddr.checked){
    //alert("Shipping address is different");
    shippingform += '<h3 class="text-center">Shipping Information</h3><hr>';

            shippingform += '<div class="form-group">';
                shippingform += '<label for="first_name" class="col-sm-5 control-label">First Name</label>';
                shippingform += '<div class="col-sm-7">';
                    shippingform += '<input class="form-control" type="text" id="first_name" name="first_name" value="Sarah">';
                shippingform += '</div>';
            shippingform += '</div>';
            shippingform += '<div class="form-group">';
                shippingform += '<label for="last_name" class="col-sm-5 control-label">Last Name</label>';
                shippingform += '<div class="col-sm-7">';
                    shippingform += '<input class="form-control" type="text" id="last_name" name="last_name" value="Gellar">';
                shippingform += '</div>';
            shippingform += '</div>';
            shippingform += '<div class="form-group">';
                shippingform += '<label for="line1" class="col-sm-5 control-label">Address Line 1</label>';
                shippingform += '<div class="col-sm-7">';
                    shippingform += '<input class="form-control" type="text" id="line1" name="line1" value="123 Gellar Street">';
                shippingform += '</div>';
            shippingform += '</div>';
            shippingform += '<div class="form-group">';
                shippingform += '<label for="line2" class="col-sm-5 control-label">Address Line 2</label>';
                shippingform += '<div class="col-sm-7">';
                    shippingform += '<input class="form-control" type="text" id="line2" name="line2" value="Suite 3">';
                shippingform += '</div>';
            shippingform += '</div>';
            shippingform += '<div class="form-group">';
                shippingform += '<label for="city" class="col-sm-5 control-label">City</label>';
                shippingform += '<div class="col-sm-7">';
                    shippingform += '<input class="form-control" type="text" id="city" name="city" value="Gellar City">';
                shippingform += '</div>';
            shippingform += '</div>';

		shippingform += '<div class="form-group">';
			shippingform += '<label for="state" class="col-sm-5 control-label">State/Province</label>';
			shippingform += '<div class="col-sm-7" id="stateselect">';


			if (country == "US" || country == "") {
			  shippingform += '<select class="form-control" id="state" name="state">';

			  var states = "AL,AK,AR,AS,AZ,CA,CO,CT,DC,DE,FL,GA,GU,HI,IA,ID,IL,IN,KS,KY,LA,MA,MD,ME,MI,MN,MO,MP,MS,MT,NC,NE,NH,NJ,NM,NV,NY,ND,OH,OK,OR,PA,PR,RI,SC,SD,TN,TX,UM,UT,VI,VT,VA,WA,WI,WV,WY";

			  var statesArray = states.split(",");

			  for (var i = 0; i < statesArray.length; i++) {
				var stateoption = statesArray[i];

				if (stateoption == state) {
				  shippingform += '<option value="' + stateoption + '" selected>' + stateoption + '</option>';
				} else {
				  shippingform += '<option value="' + stateoption + '">' + stateoption + '</option>';
				}
			  }

			  shippingform += '</select>';

			} else {

			  shippingform += '<input type="text" name="state" id="state" value="' + state + '">';

			}

                shippingform += '</div>';
            shippingform += '</div>';

            shippingform += '<div class="form-group">';
                shippingform += '<label for="zip" class="col-sm-5 control-label">Postal Code</label>';
                shippingform += '<div class="col-sm-7">';
                    shippingform += '<input class="form-control" type="text" id="zip" name="zip" value="12345" oninput="onlynumbers(\'zip\', 10);">';
                shippingform += '</div>';
            shippingform += '</div>';


           shippingform += '<div class="form-group">';
                shippingform += '<label for="countrySelect" class="col-sm-5 control-label">Country</label>';
                shippingform += '<div class="col-sm-7">';
                    shippingform += '<select class="form-control" name="countrySelect" id="countrySelect" onChange="changeshipping(\'ship\', \'' + productsjsonurl + '\');">';


			for (var ckey in countries) {
			  if (ckey === country) {
				shippingform += '<option value="' + ckey + '" selected>' + countries[ckey] + '</option>';
			  } else {
				shippingform += '<option value="' + ckey + '">' + countries[ckey] + '</option>';
			  }
			}


                    shippingform += '</select>';
                shippingform += '</div>';
            shippingform += '</div>';



    document.getElementById("spawnshipaddr").innerHTML = shippingform;

  }else{
    //alert("Shipping address is same");
    document.getElementById("spawnshipaddr").innerHTML = '';
  }

}



function onlynumbers(id_name, maxLength) {

	let input =  document.getElementById(id_name).value;

	// Remove non-numeric characters using regular expression
	input = input.replace(/\D/g, '');

	if (input.length > maxLength) {
  	  input = input.slice(0, maxLength);
  	}

	document.getElementById(id_name).value = input;
}




function checkouttotal(){

            let shippingMethodSelect = document.getElementById("shippingMethod"),
                shipmethod_cost = shippingMethodSelect.options[shippingMethodSelect.selectedIndex].value,
		subtotal = document.getElementById("subtotal").innerHTML,

		shipinfo = shipmethod_cost.split("#");

                shippingMethod = shipinfo[0],
                updatedShipping = shipinfo[1],


		grandtotal = parseFloat(subtotal) + parseFloat(updatedShipping),

		//alert("Shipping:" + updatedShipping);
		document.getElementById("shipcost").innerHTML = updatedShipping;
		document.getElementById("grandtotal").innerHTML = grandtotal.toFixed(2);

		if(updatedShipping == "0"){
			//Hide the paypal button when shipping is unavailable
			document.getElementById("paypalCheckoutContainer").innerHTML = "Shipping to this country for some or all products is unavailable.";
		}else{
			//Put the paypal button back when user switches to a country to which shipping is available
			//Have to clear the div first or else multiple buttons will appear when switching shipping options
			document.getElementById("paypalCheckoutContainer").innerHTML = "";
			showpaypalbutton();
		}

}





//https://developer.mozilla.org/en-US/docs/Web/JavaScript/Guide/Working_with_objects
function custmer(paymethod, diffaddr, accountnum, referenceid, email, firstname, lastname, address1, address2, city, state, country, zip, phone, totalamt, ip){
	this.paymethod = paymethod;
	this.diffaddr = diffaddr;
	this.accountnum = accountnum;
	this.referenceid = referenceid;
	this.email = email;
	this.firstname = firstname;
	this.country = country;
	this.zip = zip;
	this.phone = phone;
	this.totalamt = totalamt;
	this.ip = ip;
}



function sendFetchpost(phpreceiver, paymethod, diffaddr, accountnum, referenceid, email, firstname, lastname, address1, address2, city, state, country, zip, phone,  totalamt, ip){

const obj = new customer(paymethod, diffaddr, accountnum, referenceid, email, firstname, lastname, address1, address2, city, state, country, zip, phone,  totalamt, ip);

myJSON = JSON.strngify(obj);

//from https://www.freecodecamp.org/news/javascript-post-request-how-to-send-an-http-post-request-in-js/
fetch(phpreceiver, {
  method: "POST",
  body: myJSON,
  headers: {
    "Content-type": "application/json; charset=UTF-8"
  }
})
  .then((response) => response.json())
  .then((json) => console.log(json));

}


function chngshipping(billorship, productsjsonurl) {

if(billorship == "bill"){
	var country = document.myForm.countrySelectb.value;
}

if(billorship == "ship"){
	var country = document.myForm.countrySelect.value;
}

	var stateinput;
	var products = productsjsonurl;
	var diffaddrchecked = document.getElementById('diffaddr').checked;

	//alert(country);
	if(country == "US"){


		//https://support.skubana.com/hc/en-us/articles/218094157-What-is-the-address-format-needed-when-shipping-to-Puerto-Rico-and-other-US-Territories-


		if(billorship == "bill"){
				stateinput = '<select id="stateb" name="stateb">';
		}

		if(billorship == "ship"){
				stateinput = '<select id="state" name="state">';
		}
						stateinput += '<option value="AL">AL</option>';
						stateinput += '<option value="AK">AK</option>';
						stateinput += '<option value="AR">AR</option>';
						stateinput += '<option value="AS">AS</option>';
						stateinput += '<option value="AZ">AZ</option>';
						stateinput += '<option value="CA">CA</option>';
						stateinput += '<option value="CO">CO</option>';
						stateinput += '<option value="CT">CT</option>';
						stateinput += '<option value="DC">DC</option>';
						stateinput += '<option value="DE">DE</option>';
						stateinput += '<option value="FL">FL</option>';
						stateinput += '<option value="GA">GA</option>';
						stateinput += '<option value="GU">GU</option>';
						stateinput += '<option value="HI">HI</option>';
						stateinput += '<option value="IA">IA</option>';
						stateinput += '<option value="ID">ID</option>';
						stateinput += '<option value="IL">IL</option>';
						stateinput += '<option value="IN">IN</option>';
						stateinput += '<option value="KS">KS</option>';
						stateinput += '<option value="KY">KY</option>';
						stateinput += '<option value="LA">LA</option>';
						stateinput += '<option value="MA">MA</option>';
						stateinput += '<option value="MD">MD</option>';
						stateinput += '<option value="ME">ME</option>';
						stateinput += '<option value="MI">MI</option>';
						stateinput += '<option value="MN">MN</option>';
						stateinput += '<option value="MO">MO</option>';
						stateinput += '<option value="MP">MP</option>';
						stateinput += '<option value="MS">MS</option>';
						stateinput += '<option value="MT">MT</option>';
						stateinput += '<option value="NC">NC</option>';
						stateinput += '<option value="NE">NE</option>';
						stateinput += '<option value="NH">NH</option>';
						stateinput += '<option value="NJ">NJ</option>';
						stateinput += '<option value="NM">NM</option>';
						stateinput += '<option value="NV">NV</option>';
						stateinput += '<option value="NY">NY</option>';
						stateinput += '<option value="ND">ND</option>';
						stateinput += '<option value="OH">OH</option>';
						stateinput += '<option value="OK">OK</option>';
						stateinput += '<option value="OR">OR</option>';
						stateinput += '<option value="PA">PA</option>';
						stateinput += '<option value="PR">PR</option>';
						stateinput += '<option value="RI">RI</option>';
						stateinput += '<option value="SC">SC</option>';
						stateinput += '<option value="SD">SD</option>';
						stateinput += '<option value="TN">TN</option>';
						stateinput += '<option value="TX">TX</option>';
						stateinput += '<option value="UM">UM</option>';
						stateinput += '<option value="UT">UT</option>';
						stateinput += '<option value="VI">VI</option>';
						stateinput += '<option value="VT">VT</option>';
						stateinput += '<option value="VA">VA</option>';
						stateinput += '<option value="WA">WA</option>';
						stateinput += '<option value="WI">WI</option>';
						stateinput += '<option value="WV">WV</option>';
						stateinput += '<option value="WY">WY</option>';
				stateinput += '</select>';

	}else{

		if(billorship == "bill"){
		 	stateinput =  '<input class="form-control" type="text" id="stateb" name="stateb" value="">';
		}

		if(billorship == "ship"){
		 	stateinput =  '<input class="form-control" type="text" id="state" name="state" value="">';
		}

	}



if(billorship == "bill"){
		document.getElementById("stateselectb").innerHTML = stateinput;

		if(diffaddrchecked){
			document.getElementById('diffaddr').click();
		}

		document.getElementById('diffaddr').checked = false;
		document.getElementById("spawnshipaddr").innerHTML = "";



}

if(billorship == "ship"){
		document.getElementById("stateselect").innerHTML = stateinput;
}


  var file = "shipping_ajax.php?products=" + escape(products) + "&country=" + escape(country);

  //document.getElementById("debug").innerHTML = file;

  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
     document.getElementById("shippingoptions").innerHTML = this.responseText;
    checkouttotal();
    }
  };
  xhttp.open("GET", file, true);
  xhttp.send();

}