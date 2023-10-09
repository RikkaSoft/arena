<?php 
	if(isset($_SESSION['characterProperties']['id'])){
?>
<?php require_once(__ROOT__."/backend/other/enchantressFunctions.php");?>

<?php


if (enchantNotInProgress()){
?>
<div id="enchantressMainContent">
    <div id="enchantressItems">
        <?php 
            getInventory();
        ?>
    </div>
    <div id="enchantressArea">
        
    </div>
</div>
<div id="enchantressPageInfo">
    <h2>Enchantress</h2>
    <p>The enchantress can infuse your items with magical properties, she is however quite the novice and cannot control which effect your item will get</p>
    <p>Each Item can have up to two enchantments, when an item has two enchantments you can replace one of them for a new enchantment as many times as you can afford</p>
    <p>To enchant you just click on an item and then you click on the empty bars next to the item name.</p>
</div>
<?php 
}
	else{
		getPendingEnchant();
	}
}
else{
	include(__ROOT__."/public_html/frontend/pages/notallowed.php");
}
?>