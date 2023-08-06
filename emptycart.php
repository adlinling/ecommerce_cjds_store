<?php

$host = $_SERVER['SERVER_NAME'];
setcookie("cart", "", time()-3600*24*7, "/", $host);
setcookie("prices", "", time()-3600*24*7, "/", $host);


echo "Cart emptied!<br>";
echo "<a href='index.php?pg=viewcart'>View Cart</a><br>";
echo "<a href='index.php?pg=store'>Store</a>";




?>