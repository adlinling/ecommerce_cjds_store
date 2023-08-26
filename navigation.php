<?php

$indexpage = $_SERVER['PHP_SELF'];

?>
<div id="navigation">



	<div class="nav"><a href="<?php echo $indexpage;?>"><font class="navigation">Home</font></a></div>
	<div class="nav"><a href="<?php echo $indexpage;?>?pg=store"><font class="navigation">Store</font></a></div>
	<div class="nav"><a href="<?php echo $indexpage;?>?pg=viewcart"><font class="navigation">Cart</font></a></div>
	<div class="nav"><a href="<?php echo $indexpage;?>?pg=contact"><font class="navigation">Contact</font></a></div>




<?php

if(isset($_COOKIE['session_id']) || isset($_SESSION['session_id'])){

//echo "navigation.php ";
//echo "Cookie: ".$_COOKIE['session_id']." ";
//echo "Session: ".$_SESSION['session_id'];


?>

	<div class="nav"><a href="<?php echo $indexpage;?>?pg=account"><font class="navigation">Account</a></font></div>
	<div class="nav"><a href="<?php echo $indexpage;?>?pg=logout"><font class="navigation">Log Out</a></font></div>


<?php

}else{

?>
	<div class="nav"><a href="<?php echo $indexpage;?>?pg=register"><font class="navigation">Register</a></font></div>
	<div class="nav"><a href="<?php echo $indexpage;?>?pg=login"><font class="navigation">Log In</a></font></div>


<?php
}
?>



</div>
