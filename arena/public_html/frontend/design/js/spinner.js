function Spinner(winner,length,parentId){
	var running = 1;
	var tSize = 64;
	var slowDown = tSize;
	var arr = [];
	var arrLength = length; //should not be lower than 10 or higher than 100 (max speed is reached with 100);
	for (var i=0, t=7; i<arrLength; i++) {
		arr.push(Math.round(Math.random() * t));
	}
	
	//arr[2] = winner // the third element is always the winner
	
	var yStop = 0-104;
	var ySpeed = Math.round((arrLength*tSize)/90);
	if (ySpeed > 70){
		ySpeed = 70;
	}
	else if(ySpeed < 40){
		ySpeed = 40;
	}
	
	var slowPoint = -Math.abs((10 * tSize)); //slow down when there are 10 characters left
	
	var yPos = -Math.abs(tSize*arrLength);
	var yPosMax = yPos;
	var imgArray;
	
	this.setup = function(){
		var sword = loadImage("frontend/design/images/bullets/sword.png"); 
		var greatsword = loadImage("frontend/design/images/bullets/sword.png"); 
		var axe = loadImage("frontend/design/images/bullets/axe.png"); 
		var battleaxe = loadImage("frontend/design/images/bullets/battleaxe.png"); 
		var spear = loadImage("frontend/design/images/bullets/spear.png"); 
		var dagger = loadImage("frontend/design/images/bullets/dagger.png"); 
		var club = loadImage("frontend/design/images/bullets/club.png"); 
		var gold = loadImage("frontend/design/images/bullets/gold.png"); 
		this.imgArray = Array(sword,greatsword,axe,battleaxe,spear,dagger,club,gold);
		
		this.frameRate(30);
		var canvas = createCanvas(100,200);
		canvas.parent(parentId);
		
		
	}
	
	this.draw = function(){
		if (running === 1){		
			if(ySpeed > 2){
				if((yPos > slowPoint) ){
					if(slowDown >= 64){
						ySpeed = Math.pow(ySpeed,0.88);
						if (ySpeed < 2){
							ySpeed = 2;
						}
						slowDown = slowDown-64; 
					}
					slowDown = slowDown + ySpeed;
				}
			}
			DrawStuff();
			if(yPos > yStop-5){
				running = 0;
			}
		}
		else{
			if(yPos > yStop-5){
				ySpeed = ySpeed-0.4;
				DrawStuff();
			}
			else{
				throw new Error('This is not an error. This is just to abort javascript');
			}
		}
	}
	
	function DrawStuff(){
		yPos = yPos + ySpeed;
		background(0);
		fill(255);
		rect(10,10,width-20,height-20,20);
		
		for(var i = 0;i < arr.length;i++){
			image(this.imgArray[arr[i]],width/2 - tSize/3,yPos + (tSize * (i+1)),40,40);
		}
		fill(0);
		rect(0,0,width,10);
		rect(0,height-10,width,height);
		triangle(10,height/2-10,20,height/2,10,height/2+10);
		triangle(width-10,height/2-10,width-20,height/2,width-10,height/2+10);
	}
}