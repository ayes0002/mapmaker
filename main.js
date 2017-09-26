var tilesetImage = document.querySelector("#tilesetImg");
var selectorImage = document.querySelector("#selectorImg");
var canvasElement = document.querySelector("#mapCanvas");
var context = canvasElement.getContext("2d");
canvasElement.width = 640;
canvasElement.height = 640;

var clickedLocation = new position();
var lastLocation = new position();

canvasElement.addEventListener("mousedown",mInputDown, false);


var map = [
	[0,0,0,0,0,0,0,0,0,0],
	[0,0,0,0,0,0,0,0,0,0],
	[0,0,0,0,0,0,0,0,0,0],
	[0,0,0,0,0,0,0,0,0,0],
	[0,0,0,0,0,0,0,0,0,0],
	[0,0,0,0,0,0,0,0,0,0],
	[0,0,0,0,0,0,0,0,0,0],
	[0,0,0,0,0,0,0,0,0,0],
	[0,0,0,0,0,0,0,0,0,0],
	[0,0,0,0,0,0,0,0,0,0]
	];

updateMap();



function mInputDown(e)
{
	clickedLocation.x = (Math.floor(e.pageX/64) * 64);
	clickedLocation.y = (Math.floor(e.pageY/64) * 64);
	if(lastLocation.x == clickedLocation.x && lastLocation.y == clickedLocation.y)
	{
		var posX=lastLocation.x/64;
		var posY=lastLocation.y/64;
		
		map[posY][posX] ++;
		if(map[posY][posX] > 15){map[posY][posX]=0;}
	}

	lastLocation.x = clickedLocation.x;
	lastLocation.y = clickedLocation.y;
	
	updateMap();
	placeSelector();
}


function position(newX=(-1), newY=(-1))
{
	this.x = newX;
	this.y = newY;
}


function placeSelector()
{
	context.drawImage(selectorImage, clickedLocation.x, clickedLocation.y, 64, 64);
}


function updateMap()
{
	for(var y=0;y<10;y++)
	{
		for(var x=0;x<10;x++)
		{
			var xReadLoc = (map[y][x] % 4) * 64;
			var yReadLoc = (Math.floor(map[y][x] / 4)) * 64;
			var xWriteLoc = (x*64);
			var yWriteLoc = (y*64);
			context.drawImage(tilesetImage, xReadLoc , yReadLoc, 64, 64, xWriteLoc, yWriteLoc, 64, 64);
		}
	}
}




