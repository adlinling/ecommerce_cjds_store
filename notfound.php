<div style="display:flex;justify-content:right;align-items:center;padding:30px 0px 30px;margin:0;background-color:#ffffff;">



</div>



<div id="landingtop" style="display:flex;justify-content:center;align-items:center;color:#ffffff;padding:80px 0px 80px;margin:0 auto;background-image:url('pexels-garrett-morrow-682933.jpg');background-size:100%;">



<div id="landingtoptext" style="text-align:center;background-color:rgb(0, 0, 0, 0.3);padding:120px 350px 120px;">

<font style="font-family:San-serif,Verdana,Arial;font-size:1.9em;">Page not found</font><br><br>

<font style="font-family:San-serif,Verdana,Arial;font-size:0.9em;">The page you were looking for doesn't exist. You may have mistyped the address or the page may have moved.</font>
<br><br>

</div><!--"landingtoptext"-->




</div><!--"landingtop"-->



<div style="display:flex;flex-direction:column;justify-content:center;align-items:center;padding:30px 100px 30px;margin:0;background-color:#ffffff;">


<font style="font-family: BebasNeue,San-serif,Verdana,Arial;font-size:1.9em;">OUR LATEST PRODUCTS</font>


<?php

include "productslist.php";


//$thisfile = $_SERVER['PHP_SELF'];

$find = array("singquote", "dubquote");
$replace = array("&#039;", "&quot;");


$landingpageproducts = array("CJXFLPYP00001", "CJYD1793469", "CJSJ1632273", "CJYS1129210");


	echo "<div class='productgrid'>\n";

	foreach($storeproducts as $sku => $prodjson){
		if(in_array($sku, $landingpageproducts)){
			$imgurl = $products[$sku]['image'];
			//$imgurl = "image0.png";
			$title = str_replace($find, $replace, $products[$sku]['title']);
			echo "<div class='productsgriditem'><a class='prodlink' href='/?pg=store&sku=$sku'><div class='prodimgdiv'><img class='productgridimg' src='$imgurl'></div><div class='prodtitle'>$title</div></a></div>\n";
		}
	}

	echo "</div>";


?>
</div>






<div id="landingmiddle" style="display:flex;justify-content:center;align-items:center;color:#ffffff;padding:80px 0px 80px;margin:0 auto;background-image:url('pexels-spencer-selover-706144.jpg');background-size:100%;">



<div id="landingmiddletext" style="text-align:center;background-color:rgb(0, 0, 0, 0.3);padding:60px 150px 60px;">

<font style="font-family:AsapCondensed,San-serif,Verdana,Arial;font-size:1.9em;">NICE TITLE</font>
<br><br>
<font style="font-family:San-serif,Verdana,Arial;font-size:0.9em;">Blah blah blah</font>


</div><!--"landingmiddletext"-->




</div><!--"landingmiddle"-->





<?php

//for($i=0;$i<5;$i++){
//	echo "<br>";
//}
?>