<?php 
	if(isset($_SESSION['characterProperties']['id'])){
		if($_SESSION['characterProperties']['healedDate'] == 0){
?>
<?php require_once(__ROOT__."/backend/fighting/get-creature.php"); ?>
<?php $_SESSION['unique'] = rand(0,1000)?>
<script>
        function beastInfo() {
           
            	var creature = encodeURIComponent(document.getElementById("beastList").value);
            	if (creature == "Choose%20a%20Beast"){

            	}
            	else {
            	    document.cookie = "previousBeast="+creature;
                	$('.pageOutput').load('index.php?fpage=get-creature&nonUI&creatureName='+creature);
                }
    	}
    	function setSurrender(sel){
    		var value = sel.value;
    		document.cookie = "trainingSurrenderDefault="+value;
    	}
    	
    	$(document).ready(function(){
            function getCookie(cname) {
                var name = cname + "=";
                var ca = document.cookie.split(';');
                for(var i = 0; i <ca.length; i++) {
                    var c = ca[i];
                    while (c.charAt(0)==' ') {
                        c = c.substring(1);
                    }
                    if (c.indexOf(name) == 0) {
                        return c.substring(name.length,c.length);
                    }
                }
                return "";
            }
    
           var previousBeast = getCookie("previousBeast");
           if (previousBeast != "") {
               $('.pageOutput').load('index.php?fpage=get-creature&nonUI&creatureName='+previousBeast);
           }
        });
</script>
<div class="col-xs-12 col-sm-8 col-md-8 col-lg-8 pageOutput" style='padding:10px;'>
	<h2>Training</h2>
	<p>
		You can fight beasts to increase your experience and win the praise of the spectators. <br>
	</p>
</div>
<div class="col-xs-12 col-sm-4 col-md-4 col-lg-4" style='text-align:center;padding-top:30px;'>
	<?php listCreatures() ?>
	<br><br>
	<?php listStats()?>
</div>
<div class="col-xs-6 col-sm-8 col-md-8 col-lg-8 " style='float:left'>
       <?php 
            if($_SESSION['characterProperties']['right_hand'] == "Nothing" && $_SESSION['characterProperties']['left_hand'] == "Nothing" && $_SESSION['characterProperties']['secondary'] == "Nothing"){
                echo "<br><br><a href='index.php?page=market' style='text-decoration: underline;'>You don't have a weapon equipped, it would be wise to equip one before fighting, go to the store to buy one and your character to equip it</p>";
            }
       ?>
</div>
<?php 
}
else{
	include("frontend/pages/no-character.php");
}
}
else{
	include(__ROOT__."/public_html/frontend/pages/notallowed.php");
}
?>