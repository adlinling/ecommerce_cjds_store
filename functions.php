<?php
	$shipexclude = array("CJPacket Sea");

	$apikey = "[your key]";


	//Sandbox token
	$token = "[your token]";
	//Sandbox refesh token
	$refreshtoken = "[your refresh token]";



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




function getVariantName($vid, $products){

	$skutoget = "";
	$varname = "";

	foreach($products as $storkey => $storprod){
		if(preg_match("/$vid/", $storprod)){
			//echo "$storkey";
			$skutoget = $storkey;
		}	

	}


	$variantsarray = json_decode($products[$skutoget], true);


	foreach($variantsarray as $varkey => $vararray){
		if($vararray['vid'] == $vid){
			//echo $vararray['vid']."<br>";
			//echo $vararray['variantNameEn']."<br>";
			$varname = $vararray['variantNameEn'];
		}
	}

	return $varname;
}


?>