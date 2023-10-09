<?php 
	if(isset($_SESSION['characterProperties']['id'])){
	#fullRefresh($_SESSION['characterProperties']['id']);
	include(__ROOT__."/backend/character/get-character-status.php");
?>
<script>
	
	$(document).ready(function(){
		loadInventory();
		resizeLeft();
		}
	);	
	function reloadStats(){
		$('#statTable').load('index.php?cpage=get-character-status&nonUI&reload=stats');
	}
	function loadInventory(){
		$('#equipmentDiv').load('index.php?cpage=get-inventory&nonUI');
	}
	
	function reloadInventory(itemType, wantedItem){
		$('#equipmentDiv').load('index.php?cpage=get-inventory&nonUI', function(){
			var currentItem = ($('#' + itemType + '').find(":selected").text());
			if (currentItem != wantedItem){
				alert ('Not enough skill to equip ' + wantedItem + '\n\nIt could also just be a fake message, try again and it should work, working on a better solution for this');
			}
			else{
                reloadStatus();
                reloadStats();
            }
		});
	}
	function reloadInventoryWep(itemType, wantedItem){
		$('#equipmentDiv').load('index.php?cpage=get-inventory&nonUI', function(){
			var currentItem = $('#' + itemType + '').find(":selected").text();
			if (currentItem != wantedItem){
				alert ('Could not equip ' + wantedItem + '. Make sure you have enough strength to equip the item, and if it\'s a two handed weapon please unequip both your right and left hand before trying to equip it.\n\nIt could also just be a fake message, try again and it should work, working on a better solution for this');
			}
			else{
			    reloadStatus();
			}
			
		});
	}
	function reloadStatus(){
	    $('#characterStatus').load('index.php?cpage=get-character-status&reloadStats=true&nonUI', function(){
	        $('#skillTable').load('index.php?cpage=loadSkills&nonUI');
	    });
	    
	}
	function resizeLeft(){
		if (window.matchMedia("(min-width: 768px)").matches) {
			var dynamic = $('#yourChar');
		    var static = $('.pageInfo');
		    static.height(dynamic.height()-20);	       
	    }
	    else{
	
	    }
		
	}
	
</script>
<?php 
	global $extraStr,$extraDex;
	getDetails(); 
?>
<div class='characterSheet'>
	<div class='characterSubWrapper'>
		<div id="charSheetHeaderDelete">
			<form action="index.php?cpage=delete-character&nonUI" method="post" onsubmit="return confirm('Are you sure you want to delete your character?');">
				<input type="hidden" name="deleteme" value="<?php echo $_SESSION['characterProperties']['id'] . $_SESSION['loggedInId'];?>" />
				<input type="submit" id="delChar" name="delete" value="Delete Character" />
			</form>
		</div>
		<div class='leftChar'>
			<div class='characterPicture'>
				<br><br>
				There might be a picture of your character here some day

			</div>
        </div>
		<div class='rightChar'>
			<div class='characterStats'>
				<table id="charTable" style="width:100%">
					<tbody>
					  <tr>
						<td>
							<img src='frontend/design/images/character/icons/name.png' class='skillIcon'>
							<a><span title="">
								<input type="text" id="name" name="name" disabled required class='characterFields' value="<?php echo $_SESSION['characterProperties']['name'] ?>">
							</span></a>
							<label>Name</label>
						</td>
					</tr>
					<tr>
						<td>
							<img src='frontend/design/images/character/icons/gender.png' class='skillIcon'>
							<input type="text" id="yourCharInfo" class="characterFields" name="gender" disabled value="<?php echo $_SESSION['characterProperties']['gender'] ?>">
						<label>Gender</label>
						</td>
					</tr>
					<tr>
						<td>
							<img src='frontend/design/images/character/icons/race.png' class='skillIcon'>
							
							<input type="text" id="yourCharInfo" class="characterFields" name="race" disabled value="<?php echo $_SESSION['characterProperties']['race'] ?>">
						<label>Race</label>
						
						</td>
				  	</tr>
				  	<?php getStats();?>
			 		
                    <?php include(__ROOT__."/backend/character/loadSkills.php"); ?>
            </tbody>
        </table>
    </div>
</div>
</div>
    <div class='characterSubWrapper characterExtras'>
    				<div id=characterStatusWrapper>
						<strong>Detailed information</strong>
	                    <div id="characterStatus">
	                    	
	                        
	                        
	                    </div>
	                </div>
                    <div id="equipmentDiv">
                        
                    </div>
            <script>
                $('#statusTable').appendTo('#characterStatus');
                $('#statusTable2').appendTo('#characterStatus');
            </script>
	</div>
</div>
<div class="pageInfo hidden-sm hidden-md hidden-lg">
    <a class="headerButtonLink" href="index.php?page=your-character" >
        <div class="charButton" >
            My Character
        </div>
    </a>
    <a class="headerButtonLink" href="index.php?page=match_history">
        <div class="charButton">
            Match History
        </div>
    </a>
    <a class="headerButtonLink" href="index.php?page=playerIcon">
        <div class="charButton">
            Player Icon
        </div>
    </a>
    <a class="headerButtonLink" href="index.php?page=inventory">
        <div class="charButton">
            Inventory
        </div>
    </a>
    <a href="index.php?page=specifics">
        Click here for more information about skills and stats
    </a>
</div>
<?php 
}
else{
	include(__ROOT__."/public_html/frontend/pages/notallowed.php");
}
?>