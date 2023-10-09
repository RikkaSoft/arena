<div id='inventoryContainer'>
	<div id='inventoryCategoryBar'>
		<div class='inventoryCategory' id=1>
			Melee
		</div>
		<div class='inventoryCategory' id=2>
			Ranged
		</div>
		<div class='inventoryCategory' id=3>
			Armours
		</div>
		<div class='inventoryCategory' id=4>
			Parts
		</div>
		
	</div>
	<div id='inventoryArea'>
		
	</div>
</div>

<script>
	$('.inventoryCategory').click(function(){
		var id = $(this).attr('id');
		$('.inventoryCategory').removeClass("activeInventory");
		$(this).addClass("activeInventory");
		$('#inventoryArea').load('index.php?cpage=inventoryFunctions&nonUI&fetchItems='+id);
	});
</script>
<?php
	

?>