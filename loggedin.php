<?php

	$cookie = isset($_COOKIE['session_id'])?$_COOKIE['session_id']:NULL;
	//echo "Cookie already set:  $cookie<br/>";
	//if(isset($_SESSION['session_id'])){
	//	echo "Session ID: ".$_SESSION['session_id']."<br/>";
	//}
	//echo "You are logged in.<br/>";
	//echo "<a href='logout.php'>Log Out</a>";

	include "homepg.php";
?>