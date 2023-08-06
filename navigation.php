
<div id="navigation" style="margin: 0 auto;max-width:1200px;height:26px;border 1px white solid;background-color:#676767;text-align:right;font-family:Arial;font-size:0.9em;">

<?php


$indexpage = $_SERVER['PHP_SELF'];

if(isset($_COOKIE['session_id']) || isset($_SESSION['session_id'])){

//echo "navigation.php ";
//echo "Cookie: ".$_COOKIE['session_id']." ";
//echo "Session: ".$_SESSION['session_id'];


?>
	<div class="nav"><a href="<?php echo $indexpage;?>?pg=logout">Log Out</a></div>
	<div class="nav"><a href="<?php echo $indexpage;?>?pg=account">Account</a></div>

<?php

}else{

?>
	<div class="nav"><a href="<?php echo $indexpage;?>?pg=login">Log In</a></div>
	<div class="nav"><a href="<?php echo $indexpage;?>?pg=register">Register</a></div>

<?php
}
?>

	<div class="nav"><a href="<?php echo $indexpage;?>?pg=contact">Contact</a></div>
	<div class="nav"><a href="<?php echo $indexpage;?>?pg=viewcart">Cart</a></div>
	<div class="nav"><a href="<?php echo $indexpage;?>?pg=store">Store</a></div>

	<!--

	<div class="nav"><a href="<?php echo $indexpage;?>?pg=tutorials">Tutorials</a></div>
	<div class="nav"><a href="<?php echo $indexpage;?>?pg=links">Links</a></div>
	-->
	<div class="nav"><a href="<?php echo $indexpage;?>">Home</a></div>

</div>
