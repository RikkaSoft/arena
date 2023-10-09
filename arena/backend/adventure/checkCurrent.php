<?php

require_once(__ROOT__."/backend/adventure/adventureFunctions.php");


if (isset($_SESSION['characterProperties']['specificAdventure'])){
    specificAdventure($_SESSION['characterProperties']['adventureArea'],$_SESSION['characterProperties']['specificAdventure'],0);
}
elseif(isset($_SESSION['characterProperties']['adventureRoll'])){
    specificAdventure($_SESSION['characterProperties']['adventureArea'],0,$_SESSION['characterProperties']['adventureRoll']);
}
else{
    getInformation("forest");
}

?>