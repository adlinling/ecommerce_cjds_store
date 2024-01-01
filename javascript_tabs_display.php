<style>

.producttext{
  display:flex;
  flex-flow:column;
  width:400px;

}


.tabscontainer{
  display:flex;
  width:100%;

}


.tab{
  display:flex;
  flex:1;
  justify-content:center;
  background-color:#f0f0f0;
}


.description{
  background-color:#ffffff;
}



#tabdisplay{
  min-height:600px;
  padding:10px 10px 10px;

}


#Description, #Shipping, #Refund{
  display:none;
}

</style>


<script>
function viewtab(divID) {

  // Get all tab divs
  const tabDivs = document.querySelectorAll(".tab");

  // Reset all tab divs to the default background color
  tabDivs.forEach(tabDiv => tabDiv.style.backgroundColor = "#f0f0f0");

  // Get the clicked tab div
  const clickedTab = document.querySelector(`.tab.${divID.toLowerCase()}`);

  // Set the background color of the clicked tab to white
  clickedTab.style.backgroundColor = "#ffffff";


  // Get references to the divs
  const tabContentDiv = document.getElementById(divID);
  const tabDisplayDiv = document.getElementById("tabdisplay");

  // Hide any currently displayed content
  tabDisplayDiv.innerHTML = "";

  // Check if the requested div exists
  if (tabContentDiv) {
    // Get the content of the div
    const content = tabContentDiv.innerHTML;

    // Display the content in the tab display div
    tabDisplayDiv.innerHTML = content;
  } else {
    console.error("Div with ID '" + divID + "' not found.");
  }
}

</script>



<div class="producttext">

<div class="tabscontainer">

<div class="tab description" onclick="viewtab('Description');">
Description
</div>

<div class="tab shipping" onclick="viewtab('Shipping');">
Shipping
</div>

<div class="tab refund" onclick="viewtab('Refund');">
Refund
</div>

</div><!-- class="tabscontainer"-->



<div id="Description">
Description text. <br><br>  <a href='#link'>link</a> to somewhere
</div>

<div id="Shipping">
Shipping text
</div>


<div id="Refund">
Refund text
</div>


<div id="tabdisplay">
This is is what is initially displayed when the page is loaded.  It could be the content of in the Description div.

</div><!--id="tabdisplay"-->

</div><!--class="producttext"-->