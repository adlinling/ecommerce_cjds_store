<?php

$indexpage = $_SERVER['PHP_SELF'];

?>
<div id="navigation">


<form name="search" class="searchform" action="index.php?pg=search" method="POST" ">
<input type="text" name="search" class="search-field" placeholder="Search">
<button name="submit" class="search-button"><img src="magnifier.png"></button>
</form>

	<div class="nav"><a href="<?php echo $indexpage;?>"><font class="navigation">Home</font></a></div>
	<div class="nav"><a href="<?php echo $indexpage;?>?pg=store"><font class="navigation">Store</font></a></div>
	<div class="nav"><a href="<?php echo $indexpage;?>?pg=contact"><font class="navigation">Contact</font></a></div>



	<!--

	<div class="nav"><a href="<?php echo $indexpage;?>?pg=tutorials">Tutorials</a></div>
	<div class="nav"><a href="<?php echo $indexpage;?>?pg=links">Links</a></div>
	-->




<?php

if(isset($_COOKIE['session_id']) || isset($_SESSION['session_id'])){

//echo "navigation.php ";
//echo "Cookie: ".$_COOKIE['session_id']." ";
//echo "Session: ".$_SESSION['session_id'];


?>

	<div class="nav"><a href="<?php echo $indexpage;?>?pg=account"><font class="navigation">Account</a></font></div>
	<div class="nav"><a href="<?php echo $indexpage;?>?pg=logout"><font class="navigation">Log&nbsp;out</a></font></div>


<?php

}else{

?>
	<div class="nav"><a href="<?php echo $indexpage;?>?pg=register"><font class="navigation">Register</a></font></div>
	<div class="nav"><a href="<?php echo $indexpage;?>?pg=login"><font class="navigation">Log&nbsp;In</a></font></div>

<?php
}
?>

	<div class="carticon" title="Cart"><a href="<?php echo $indexpage;?>?pg=viewcart"><image class="cart" src="cart.png"></a></div>

</div>
