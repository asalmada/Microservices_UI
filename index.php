<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>

<?php

include 'getItems.php';
$result = RetrieveItems();

?>

<script>
var RETRY_INTERVAL = 5000;
var customerId = Math.floor((Math.random() * 999) + 1);
var items = <?php echo $result?>;

function loadItems(items){
    if (items.error !== undefined) {
        reloadCatalog();
        return;
    }
    var i = 0;
    console.log("Load Items: " + items.rows);
    document.getElementById("loading").innerHTML = "";
    for(i = 0; i < items.rows.length; ++i){
        addItem(items.rows[i].doc);
    }
}

function reloadCatalog() {
    showErrorMessage("The catalog is not currently available, retrying...");
    window.setTimeout(
        function() {
            $.ajax ({
                type: "GET",
                contentType: "application/json",
                url: "ajaxGetItems.php",
                success: function(result) {
                    loadItems(JSON.parse(result));
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    reloadCatalog();
                } 
            })
        },
        RETRY_INTERVAL
    );
}

function showErrorMessage(message) {
    document.getElementById("loading").innerHTML = message;
}

function addItem(item){
	var div = document.createElement('div');
	div.className = 'item';
	div.innerHTML = "<div class ='well'><img width='100%' height='auto' src = '"+item.imgsrc+"'/><br><button onclick='orderItem(\""+item._id+"\")'><b>Request</b></button><br><u>"+item.name+"</u><br>"+item.description+"<br><b>$"+item.usaDollarPrice + "</b></div>";
	document.getElementById('boxes').appendChild(div);
}

function orderItem(itemID){
    var count = Math.floor((Math.random() * 99) + 1);
    var myjson = {"itemid": itemID, "customerid": customerId, "count": count};
    
    $.ajax ({
    	type: "POST",
    	contentType: "application/json",
	    url: "submitOrders.php",
	    data: JSON.stringify(myjson),
	    dataType: "json",
	    success: function( result ) {
	        if(result.httpCode != "201" && result.httpCode != "200"){
	        	alert("Failure: check that your JavaOrders API App is running and your user-provided service has the correct URL.");
	        }
	        else{
	        	alert("Order Submitted! Check your Java Orders API to see your orders: \n" + result.ordersURL);
	        }
	    },
	    error: function(XMLHttpRequest, textStatus, errorThrown) { 
	    	alert("Error");
        	console.log("Status: " , textStatus); console.log("Error: " , errorThrown); 
    }  
	});

}

</script>
<html>
<head>
	<title>Acme Airlines COVID-19 Safety Store</title>
	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
	<link rel="stylesheet" href="style.css">
</head>
<table class="headerTable">
	<tr>
		<td><span class="pageTitle"><h1>Acme Airlines COVID-19 Safety Store</h1></span></td> 
	</tr>
</table>
<h2>What Aircraft Maintenance Workers Need to Know about COVID-19</h2>
<h3>
    Coronavirus disease 2019 (COVID-19) is a respiratory illness caused by a virus called SARS-CoV-2. Symptoms often include a fever, cough, or shortness of breath. Our understanding of how the virus spreads is evolving as we learn more about it, so check the CDC website for the latest information. The virus is thought to spread mainly from person-to-person:
</h3>
<ul>
    <li>Between people who are in close contact with one another (within about 6 feet).</li>
    <li>Through respiratory droplets produced when an infected person coughs, sneezes, or talks.</li>
</ul>
<h3>Recent studies indicate that the virus can be spread by people before they develop symptoms or who never develop symptoms. It also may be possible that a person can get COVID-19 by touching a surface or object that has the virus on it and then touching their own mouth, nose, or possibly their eyes. However, this is not thought to be the main way the virus spreads. Older adults and people of any age who have serious underlying medical conditions may be at higher risk for more serious complications from COVID-19.</h3>
<body onload="loadItems(items)">
	<div class="container">
		<div id='boxes' class="notes"></div>
	</div>
	<div id="loading"><br>Loading...</div>
	<a href="./autoLoadTest.html">Catalog Load Tester</a>
</body>
</html>

