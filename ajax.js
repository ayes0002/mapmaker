function newMap()
{
	/*processAJAX("action=New");*/
	
	for(var y=0;y<10;y++)
	{
		for(var x=0;x<10;x++)
		{
			map[y][x] = 0;
		}
	}
	updateMap();
	//callback must call updateMap(); and for Load and Download must first set the new array data in the 'map' var.
	
}




function downloadMap(){
	processAJAX("action=Download");
}

function uploadMap(){
	processAJAX("action=Upload&jsonMapData=" + JSON.stringify(map));
}


function loadMap(){
	processAJAX("action=Load");
}

function saveMap(){
	processAJAX("action=Save&jsonMapData=" + JSON.stringify(map));
}


function exportMap(){
	processAJAX("action=Export&jsonMapData=" + JSON.stringify(map));
}


function processAJAX(phpGetParams)
{
	// Make a variable that will hold our HTTP connection object
	var xmlhttp;
	
	// Initialize object for IE7 and up, and other modern browsers
	if (window.XMLHttpRequest){
		xmlhttp=new XMLHttpRequest();
	}
	// Or, initialize object for IE 6 and 5
	else{
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	
	// Setup your message to the server.
	xmlhttp.open("GET","processRequest.php?" + phpGetParams,true);
	// Then send the message.
	xmlhttp.send();
	
	// Now we write code to handle the response from the server.
	xmlhttp.onreadystatechange= function() {
		if (xmlhttp.readyState==4 && xmlhttp.status==200){
			// The readyState==4 and status=200 are HTTP codes.
			// We expect JSON back from the server so we parsing it into an array
			//console.log("JSON_RESPONSE: " + xmlhttp.responseText);
			if(phpGetParams == "action=Download" || phpGetParams == "action=Load" || phpGetParams == "action=New")
			{
				map = JSON.parse(xmlhttp.responseText);
			}
			
			updateMap();
		}
	}
}
