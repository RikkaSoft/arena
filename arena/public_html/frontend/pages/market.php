<?php require_once(__ROOT__."/backend/other/get-vendor.php");?>	
			<script>
			
				$(document).ready(function() {
					$('#mainPage').scroll(function() { 
					    if (window.matchMedia("(min-width: 768px)").matches) {
                            $('.storeMainContent').animate({top:$(this).scrollTop()},10,"linear");
                        } else {
                            
                        }
					});
				    getYourItems();
				});
				function itemOutput(type,subType,itemName,info)
				{
					var form = "<div id=\"storeInfoContainer\">";
					if ('<?php if(isset($_SESSION['characterProperties']['id'])){echo "1";} ?>' == 1){
						var buyButton = "</div><br><br><button onclick=\"buyItem('" + itemName + "','" + type + "','" + subType + "')\">Buy Item</button>";
						var closeButton = "<button onclick=\"closeOutput()\" style=\"margin-left:30px;\">Close</button>";
					}
					else{
						var buyButton = "</div>";
						var closeButton = "<button onclick=\"closeOutput()\" >Close</button>";
					}
				    $('.storeMainContent').hide().html(form+info+buyButton+closeButton).fadeIn('500');
				    if (window.matchMedia("(min-width: 768px)").matches) {
                       
                    }
                    else{
                        $('#mainPage').animate({
                           scrollTop: 0
                        }, 'slow');
                    }
				}
				function itemOutputSell(type,subType,itemName,info){
					
					var form = "<div id=\"storeInfoContainer\">";
					var sellButton = "</div><br><br><button onclick=\"sellItem('" + itemName + "','" + type + "','" + subType + "')\">Sell Item</button>";
					var closeButton = "<button onclick=\"closeOutput()\" style=\"margin-left:30px;\">Close</button>";
				    $('.storeMainContent').hide().html(form+info+sellButton+closeButton).fadeIn('500');;
				    if (window.matchMedia("(min-width: 768px)").matches) {
				       
				    }
				    else{
				        $('#mainPage').animate({
                           scrollTop: 0
                        }, 'fast');
				    }
				}
				
				function closeOutput(){
				    $('.storeMainContent').fadeOut('500');
				}
								
				function buyItem(itemName,itemType,itemSubType){
					 $(".storeMainContent").load("index.php?cpage=buy-item&nonUI", {
				           itemName: itemName,
				           itemType: itemType,
				           itemSubType: itemSubType
				       }, function () {
				       	getYourItems();
				       	updateChar()
				       	});
				}
				
				function getYourItems(){
					$("#yourStoreItems").load("index.php?opage=get-vendor&nonUI&yourItems=GoGetThem");
				}
				function sellItem(itemName,itemType,itemSubType){
					 $(".storeMainContent").load("index.php?cpage=sell-item&nonUI", {
				           itemName: itemName,
				           itemType: itemType,
				           itemSubType: itemSubType
				       }, function () {
				       	getYourItems()
				       	updateChar();});
				}
				
				
			</script>
			<div class="storeMainContent" style='display:none;'></div>
<div id="storePageInfo">
	<h3>Buy Equipment</h3>
	Weapons
	<ul id="weapons">
		<li>
			<label for="sword-toggle"><img src="frontend/design/images/bullets/sword.png"> Swords</label>
			<input type="checkbox" id="sword-toggle"/>
			<ul id="swords">
				<?php getItems("weapons","swords","na");?>
			</ul>
		</li>
		<li>
			<label for="greatsword-toggle"><img src="frontend/design/images/bullets/sword.png"> Greatswords</label>
			<input type="checkbox" id="greatsword-toggle"/>
			<ul id="greatswords">
				<?php getItems("weapons","greatswords","na");?>
			</ul>
		</li>
		<li>
			<label for="dagger-toggle"><img src="frontend/design/images/bullets/dagger.png"> Daggers</label>
			<input type="checkbox" id="dagger-toggle"/>
			<ul id="daggers">
				<?php getItems("weapons","daggers","na");?>
			</ul>
		</li>
		
		<li>
			<label for="spear-toggle"><img src="frontend/design/images/bullets/spear.png"> Spears</label>
			<input type="checkbox" id="spear-toggle"/>
			<ul id="spears">
				<?php getItems("weapons","spears","na");?>
			</ul>
		</li>
		<li>
			<label for="axe-toggle"><img src="frontend/design/images/bullets/axe.png"> Axes</label>
			<input type="checkbox" id="axe-toggle"/>
			<ul id="axes">
				<?php getItems("weapons","axes","na");?>
			</ul>
		</li>
		<li>
			<label for="battleaxe-toggle"><img src="frontend/design/images/bullets/battleaxe.png"> Battleaxes</label>
			<input type="checkbox" id="battleaxe-toggle"/>
			<ul id="battleaxes">
				<?php getItems("weapons","battleaxes","na");?>
			</ul>
		</li>
		<li>
			<label for="club-toggle"><img src="frontend/design/images/bullets/club.png"> Clubs</label>
			<input type="checkbox" id="club-toggle"/>
			<ul id="clubs">
				<?php getItems("weapons","clubs","na");?>
			</ul>
		</li>
		<li>
			<label for="largeclub-toggle"><img src="frontend/design/images/bullets/club.png"> Large Clubs</label>
			<input type="checkbox" id="largeclub-toggle"/>
			<ul id="largeclubs">
				<?php getItems("weapons","large clubs","na");?>
			</ul>
		</li>
		<li>
			<label for="hammers-toggle"><img src="frontend/design/images/bullets/hammer.png"> Hammers</label>
			<input type="checkbox" id="hammers-toggle"/>
			<ul id="hammers">
				<?php getItems("weapons","hammers","na");?>
			</ul>
		</li>
	</ul>
	<br>
	Secondary
	<ul id="secondary">
	    <li>
	        <label for="bow-toggle"><img src="frontend/design/images/bullets/bow.png"> Bows</label>
            <input type="checkbox" id="bow-toggle"/>
            <ul id="bows">
                <?php getItems("weapons","bows","na");?>
            </ul>
	    </li>
	    <li>
            <label for="crossbow-toggle"><img src="frontend/design/images/bullets/crossbow.png"> Crossbows</label>
            <input type="checkbox" id="crossbow-toggle"/>
            <ul id="crossbows">
                <?php getItems("weapons","crossbows","na");?>
            </ul>
        </li>
	</ul>
	<br>
	Armours
	<ul id="armours">
		<li>
		
		<label for="light-toggle"><img src="frontend/design/images/bullets/light.png"> Light Armour</label>
		<input type="checkbox" id="light-toggle"/>
		<ul id="lights">
			<li>
				<label for="head-toggle"><img src="frontend/design/images/bullets/head.png"> Helmets</label>
				<input type="checkbox" id="head-toggle"/>
				<ul id="heads">
					<?php getItems("armours","heads","Light Armour");?>
				</ul>
			</li>
			<li>
				<label for="chest-toggle"><img src="frontend/design/images/bullets/chest.png"> Chests</label>
				<input type="checkbox" id="chest-toggle"/>
				<ul id="chests">
					<?php getItems("armours","chests","Light Armour");?>
				</ul>
			</li>
			<li>
				<label for="arm-toggle"><img src="frontend/design/images/bullets/arms.png"> Arms</label>
				<input type="checkbox" id="arm-toggle"/>
				<ul id="arms">
					<?php getItems("armours","arms","Light Armour");?>
				</ul>
			</li>
			<li>
				<label for="leg-toggle"><img src="frontend/design/images/bullets/legs.png"> Legs</label>
				<input type="checkbox" id="leg-toggle"/>
				<ul id="legs">
					<?php getItems("armours","legs","Light Armour");?>
				</ul>
			</li>
			<li>
				<label for="feet-toggle"><img src="frontend/design/images/bullets/feet.png"> Boots</label>
				<input type="checkbox" id="feet-toggle"/>
				<ul id="feets">
					<?php getItems("armours","feets","Light Armour");?>
				</ul>
			</li>
			</li>
		</ul>
		<br>
		<label for="heavy-toggle"><img src="frontend/design/images/bullets/heavy.png"> Heavy Armour</label>
		<input type="checkbox" id="heavy-toggle"/>
		<ul id="heavys">
			<li>
				<label for="dhead-toggle"><img src="frontend/design/images/bullets/head.png"> Helmets</label>
				<input type="checkbox" id="dhead-toggle"/>
				<ul id="dheads">
					<?php getItems("armours","heads","Heavy Armour");?>
				</ul>
			</li>
			<li>
				<label for="dchest-toggle"><img src="frontend/design/images/bullets/chest.png"> Chests</label>
				<input type="checkbox" id="dchest-toggle"/>
				<ul id="dchests">
					<?php getItems("armours","chests","Heavy Armour");?>
				</ul>
			</li>
			<li>
				<label for="darm-toggle"><img src="frontend/design/images/bullets/arms.png"> Arms</label>
				<input type="checkbox" id="darm-toggle"/>
				<ul id="darms">
					<?php getItems("armours","arms","Heavy Armour");?>
				</ul>
			</li>
			<li>
				<label for="dleg-toggle"><img src="frontend/design/images/bullets/legs.png"> Legs</label>
				<input type="checkbox" id="dleg-toggle"/>
				<ul id="dlegs">
					<?php getItems("armours","legs","Heavy Armour");?>
				</ul>
			</li>
			<li>
				<label for="dfeet-toggle"><img src="frontend/design/images/bullets/feet.png"> Boots</label>
				<input type="checkbox" id="dfeet-toggle"/>
				<ul id="dfeets">
					<?php getItems("armours","feets","Heavy Armour");?>
				</ul>
			</li>
		</ul>
	</ul>
	<br>
	Shields
	<ul id="shieldCat" >
	
		<li>
			<label for="shield-toggle"><img src="frontend/design/images/bullets/shield.png"> Shields</label>
			<input type="checkbox" id="shield-toggle"/>
			<ul id="shields">
				<?php getItems("weapons","shields","na");?>
			</ul>
		</li>
	</ul>
	<br>
	Trinkets
	<ul id="trinketCat" >
	
		<li>
			<label for="trinket-toggle"><img src="frontend/design/images/bullets/trinket.png"> Trinkets</label>
			<input type="checkbox" id="trinket-toggle"/>
			<ul id="trinkets">
				<?php getTrinkets();?>
			</ul>
		</li>
	</ul>
	<br>
	<?php if(isset($_SESSION['characterProperties']['id'])){ ?>
		Parts
		<ul id="partsCat" >

			<?php getParts();?>
		</ul>
	<?php }?>
	<br><br>
	<div id="yourStoreItems"></div>
<?php #getYourItems();?>
</div>


	
				