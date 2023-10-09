<?php
function getItemWithName($itemStr,$nameS,$type){
global $conn;
    $prefixS = "";
    $suffixS = "";
    
    $seperate = explode(":",$itemStr);
    $id = $seperate[0];
    $enchants = explode(";",$seperate[1]);
    $prefix = $enchants[0];
    $suffix = $enchants[1];
    if($prefix != 1 && $suffix == 1){
        $sql = "SELECT prefix FROM enchants WHERE id='$prefix' AND (type='$type' OR type='all')";
        $result = mysqli_query($conn,$sql);
        $row = mysqli_fetch_assoc($result);
        if($row['prefix'] != ""){
            $prefixS = $row['prefix'] . " ";
        }
    }
    elseif($prefix == 1 && $suffix != 1){
        $sql = "SELECT suffix FROM enchants WHERE id='$suffix' AND (type='$type' OR type='all')";
        $result = mysqli_query($conn,$sql);
        $row = mysqli_fetch_assoc($result);
        if($row['suffix'] != ""){
            $suffixS = " of " . $row['suffix'];
        }
    }
    elseif($prefix != 1 && $suffix != 1){
        $sql = "SELECT prefix FROM enchants WHERE id='$prefix' AND (type='$type' OR type='all')";
        $result = mysqli_query($conn,$sql);
        $row = mysqli_fetch_assoc($result);
        if($row['prefix'] != ""){
            $prefixS = $row['prefix'] . " ";
        }
        unset($row);
        $sql = "SELECT suffix FROM enchants WHERE id='$suffix' AND (type='$type' OR type='all')";
        $result = mysqli_query($conn,$sql);
        $row = mysqli_fetch_assoc($result);
        if($row['suffix'] != ""){
            $suffixS = " of " . $row['suffix'];
        }
        
    }
        return $prefixS . $nameS . $suffixS;
}

function getItemWithoutName($itemStr,$type){
global $conn;
    
    if($type == 1){
        $table = "weapons";
    }
    else{
        $table = "armours";
    }

    $prefixS = "";
    $suffixS = "";
    
    $seperate = explode(":",$itemStr);
    $id = $seperate[0];
    $sql = "SELECT name,type FROM $table WHERE id='$id'";
    $result = mysqli_query($conn,$sql);
    $row = mysqli_fetch_assoc($result);
    $nameS = $row['name'];
    $type = $row['type'];
    if($type == "1h" || $type == "2h"){
        $type = "melee";
    }
    elseif($type == "bow" || $type == "crossbow"){
        $type = "ranged";
    }
    elseif($type == "shield"){
        $type = "shield";
    }
    else{
        $type = "armour";
    }
    
    $enchants = explode(";",$seperate[1]);
    $prefix = $enchants[0];
    $suffix = $enchants[1];
    if($prefix != 1 && $suffix == 1){
        $sql = "SELECT prefix FROM enchants WHERE id='$prefix' AND (type='$type' OR type='all')";
        $result = mysqli_query($conn,$sql);
        $row = mysqli_fetch_assoc($result);
        if($row['prefix'] != ""){
            $prefixS = $row['prefix'] . " ";
        }
    }
    elseif($prefix == 1 && $suffix != 1){
        $sql = "SELECT suffix FROM enchants WHERE id='$suffix' AND (type='$type' OR type='all')";
        $result = mysqli_query($conn,$sql);
        $row = mysqli_fetch_assoc($result);
        if($row['suffix'] != ""){
            $suffixS = " of " . $row['suffix'];
        }
    }
    elseif($prefix != 1 && $suffix != 1){
        $sql = "SELECT prefix FROM enchants WHERE id='$prefix' AND (type='$type' OR type='all')";
        $result = mysqli_query($conn,$sql);
        $row = mysqli_fetch_assoc($result);
        if($row['prefix'] != ""){
            $prefixS = $row['prefix'] . " ";
        }
        unset($row);
        $sql = "SELECT suffix FROM enchants WHERE id='$suffix' AND (type='$type' OR type='all')";
        $result = mysqli_query($conn,$sql);
        $row = mysqli_fetch_assoc($result);
        if($row['suffix'] != ""){
            $suffixS = " of " . $row['suffix'];
        }
        
    }
        return $prefixS . $nameS . $suffixS;
}
function addBonuses($row,$type,$enchantTier){
    
    $enchantEffective = array("0.8","1","1.2","1.4","1.6");
    $enchantTier = $enchantTier-1;
    
    $enchant = "<strong>" . $row[$type] . "</strong>: ";
    if($row['damageBonusPercent'] > 0){
        $enchant .= "+" . $row['damageBonusPercent'] * $enchantEffective[$enchantTier] . "% Damage ";
    }
    if ($row['damageBonusPoint']  > 0){
        $enchant .= "+" . $row['damageBonusPoint'] * $enchantEffective[$enchantTier] . " Damage ";
    }
    if ($row['accuracyPercent']  > 0){
        $enchant .= "+" . $row['accuracyPercent'] * $enchantEffective[$enchantTier] . "% Accuracy ";
    }
    if ($row['armourPenetration']  > 0){
        $enchant .= "+" . round($row['armourPenetration'] * $enchantEffective[$enchantTier]) . " Armour Penetration ";
    }
    if ($row['armourBonus']  > 0){
        $enchant .= "+" . $row['armourBonus'] * $enchantEffective[$enchantTier] . " Armour ";
    }
    if ($row['weightReduction']  > 0){
        $enchant .= "-" . $row['weightReduction'] * $enchantEffective[$enchantTier] . " Weight ";
    }
    if ($row['oneSkill']  > 0){
        $enchant .= "+" . $row['oneSkill'] * $enchantEffective[$enchantTier] . " One-handed skill ";
    }
    if ($row['twoSkill']  > 0){
        $enchant .= "+" . $row['twoSkill'] * $enchantEffective[$enchantTier] . " Two-handed skill ";
    }
    if ($row['bowSkill']  > 0){
        $enchant .= "+" . $row['bowSkill'] * $enchantEffective[$enchantTier] . " Bow skill ";
    }
    if ($row['xBowSkill']  > 0){
        $enchant .= "+" . $row['xBowSkill'] * $enchantEffective[$enchantTier] . " Crossbow skill ";
    }
    if ($row['finesseSkill']  > 0){
        $enchant .= "+" . $row['finesseSkill'] * $enchantEffective[$enchantTier] . " Finesse skill ";
    }
    if ($row['initiativeSkill']  > 0){
        $enchant .= "+" . $row['initiativeSkill'] * $enchantEffective[$enchantTier] . " Initiative skill ";
    }
    if ($row['shieldSkill']  > 0){
        $enchant .= "+" . $row['shieldSkill'] * $enchantEffective[$enchantTier] . " Shield skill ";
    }
    if ($row['parrySkill']  > 0){
        $enchant .= "+" . $row['parrySkill'] * $enchantEffective[$enchantTier] . " Parry skill ";
    }
    if ($row['foulSkill']  > 0){
        $enchant .= "+" . $row['foulSkill'] * $enchantEffective[$enchantTier] . " Foul Play skill ";
    }
    if ($row['dodgeSkill']  > 0){
        $enchant .= "+" . $row['dodgeSkill'] * $enchantEffective[$enchantTier] . " Dodge skill ";
    }
	if ($row['blockPercent']  > 0){
        $enchant .= "+" . $row['blockPercent'] * $enchantEffective[$enchantTier] . " % block ";
    }
    
    return $enchant . "<br>";
}

function getItemWithoutNameWithStats($itemStr,$type,$enchantress){
global $conn;
    if($type == 1){
        $table = "weapons";
    }
    else{
        $table = "armours";
    }

    $prefixS = "";
    $suffixS = "";
    $prefixStats = "";
    $suffixStats = "";
    
    $seperate = explode(":",$itemStr);
    $id = $seperate[0];
    $sql = "SELECT name,type,item_type,enchantType,enchantTier FROM $table WHERE id='$id'";
    $result = mysqli_query($conn,$sql);
    $row = mysqli_fetch_assoc($result);

    $nameS = $row['name'];
    $type = $row['type'];
    $enchantTier = $row['enchantTier'];
    
    if($type == "1h" || $type == "2h"){
        $type = "melee";
        if($enchantress == 1){
            $_SESSION['other']['enchantSlot'] = "weapons";   
            $_SESSION['other']['enchantType'] = $row['enchantType'];
        }
    }
    elseif($type == "shield"){
        $_SESSION['other']['enchantSlot'] = "weapons";
        $_SESSION['other']['enchantType'] = $row['enchantType'];
    }
    elseif($type == "bow" || $type == "crossbow"){
        $type = "ranged";
        $_SESSION['other']['enchantSlot'] = "weapons";
        $_SESSION['other']['enchantType'] = $row['enchantType'];
    }
    else{
        $type = "armour";
        $_SESSION['other']['enchantSlot'] = $row['item_type'];
        $_SESSION['other']['enchantType'] = $row['enchantType'];
    }
    
    $enchants = explode(";",$seperate[1]);
    $prefix = $enchants[0];
    $suffix = $enchants[1];
    if($prefix != 1 && $suffix == 1){
        $sql = "SELECT * FROM enchants WHERE id='$prefix' AND (type='$type' OR type='all')";
        $result = mysqli_query($conn,$sql);
        $row = mysqli_fetch_assoc($result);
        if($row['prefix'] != ""){
            $prefixS = $row['prefix'];
            $prefixStats = addBonuses($row,"prefix",$enchantTier);
        }
    }
    elseif($prefix == 1 && $suffix != 1){
        $sql = "SELECT * FROM enchants WHERE id='$suffix' AND (type='$type' OR type='all')";
        $result = mysqli_query($conn,$sql);
        $row = mysqli_fetch_assoc($result);
        if($row['suffix'] != ""){
            $suffixS = "of " . $row['suffix'];
            $suffixStats = addBonuses($row,"suffix",$enchantTier);
        }
    }
    elseif($prefix != 1 && $suffix != 1){
        $sql = "SELECT * FROM enchants WHERE id='$prefix' AND (type='$type' OR type='all')";
        $result = mysqli_query($conn,$sql);
        $row = mysqli_fetch_assoc($result);
        if($row['prefix'] != ""){
            $prefixS = $row['prefix'];
            $prefixStats = addBonuses($row,"prefix",$enchantTier);
        }
        unset($row);
        $sql = "SELECT * FROM enchants WHERE id='$suffix' AND (type='$type' OR type='all')";
        $result = mysqli_query($conn,$sql);
        $row = mysqli_fetch_assoc($result);
        if($row['suffix'] != ""){
            $suffixS = "of " . $row['suffix'];
            $suffixStats = addBonuses($row,"suffix",$enchantTier);
        }
        
    }
        if($enchantress == 1){
            if ($prefixS == ""){
                $prefixS = "<a href='&#35;' class='enchants' id='prefix'>____ </a>";
            }
            else{
                $prefixS = "<a href='&#35;' class='enchants' id='prefix'>" . $prefixS . "</a>";
            }
            if ($suffixS == ""){
                $suffixS = "<a href='&#35;' class='enchants' id='suffix'> ____</a>";
            }
            else{
                $suffixS = "<a href='&#35;' class='enchants' id='suffix'>" . $suffixS . "</a>";
            }
            $nameS = "<a href='#' class='chosenItem' id='" . $itemStr . "'>" . $nameS . "</a>";
        }
        $name = $prefixS . " " . $nameS . " " . $suffixS;
        
        $enchants = $prefixStats . $suffixStats;
        return array("name"=>$name,"enchants"=>$enchants);
}
?>