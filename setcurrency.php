<?php
session_start();

$currencystr =  isset($_GET['currency'])?$_GET['currency']:"USD:1";

//list($currency, $exchangerate) = explode(":", $currencystr);

$_SESSION['currency'] = $currencystr;

echo "Currency set to: ".$_SESSION['currency'];
?>