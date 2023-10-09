<tr>
    <td>
        <img src='frontend/design/images/character/icons/1h.png' class='skillIcon'>
        <input id="attributes" type="text" name="one_handed" disabled value='<?php echo $_SESSION['characterProperties']['one_handed']; if ($_SESSION['extraStats']['one'] != 0){echo " (+" . $_SESSION['extraStats']['one'] . ")";}?>'>
        <a title="Makes you better at fighting with One-Handed weapons" class="tooltipLeft"><span title=""> 
            <span class='tooltipHover'>1H Weapons</span>
        </span></a>
    </td>
</tr>
<tr>
    <td>
        <img src='frontend/design/images/character/icons/2h.png' class='skillIcon'>
        <input id="attributes" type="text" name="two_handed" disabled value='<?php echo $_SESSION['characterProperties']['two_handed']; if ($_SESSION['extraStats']['two'] != 0){echo " (+" . $_SESSION['extraStats']['two'] . ")";}?>'>
        <a title="Makes you better at fighting with Two-Handed weapons" class="tooltipLeft"><span title=""> 
            <span class='tooltipHover'>2H Weapons</span>
        </span></a>
    </td>
</tr>
<tr>
    <td>
        <img src='frontend/design/images/character/icons/bow.png' class='skillIcon'>
        <input id="attributes" type="text" name="bow" disabled value='<?php echo $_SESSION['characterProperties']['bow']; if ($_SESSION['extraStats']['bow'] != 0){echo " (+" . $_SESSION['extraStats']['bow'] . ")";}?>'>
        <a title="Makes you better at using a Bow" class="tooltipLeft"><span title=""> 
            <span class='tooltipHover'>Bow</span>
        </span></a>
    </td>
</tr>
<tr>
    <td>
        <img src='frontend/design/images/character/icons/crossbow.png' class='skillIcon'>
        <input id="attributes" type="text" name="crossbow" disabled value='<?php echo $_SESSION['characterProperties']['crossbow']; if ($_SESSION['extraStats']['xBow'] != 0){echo " (+" . $_SESSION['extraStats']['xBow'] . ")";}?>'>
        <a title="Makes you better at using a Crossbow" class="tooltipLeft"><span title=""> 
            <span class='tooltipHover'>Crossbow</span>
        </span></a>
    </td>
</tr>
<tr>
    <td>
        <img src='frontend/design/images/character/icons/initiative.png' class='skillIcon'>
        <input id="attributes" type="text" name="initiative" disabled value='<?php echo $_SESSION['characterProperties']['initiative']; if ($_SESSION['extraStats']['initiative'] != 0){echo " (+" . $_SESSION['extraStats']['initiative'] . ")";}?>'>
    <a title="Initiative makes you more likely to be the one to act first" class="tooltipLeft"><span title=""> 
            <span class='tooltipHover'>Initiative</span>
        </span></a>
    </td>
</tr>
<tr>
    <td>
        <img src='frontend/design/images/character/icons/finesse.png' class='skillIcon'>
        <input id="attributes" type="text" name="finesse" disabled value='<?php echo $_SESSION['characterProperties']['finesse']; if ($_SESSION['extraStats']['finesse'] != 0){echo " (+" . $_SESSION['extraStats']['finesse'] . ")";}?>'>
        <a title="Finesse slightly increases your chance to critically hit your opponent" class="tooltipLeft"><span title=""> 
            <span class='tooltipHover'>Finesse</span>
        </span></a>
    </td>
</tr>
<tr>
    <td>
        <img src='frontend/design/images/character/icons/light.png' class='skillIcon'>
        <input id="attributes" type="text" name="light_armour" disabled value='<?php echo $_SESSION['characterProperties']['light_armour']; if ($_SESSION['extraStats']['lightArmour'] != 0){echo " (+" . $_SESSION['extraStats']['lightArmour'] . ")";}?>'>
        <a title="Allows you to equip yourself with Light Armour" class="tooltipLeft"><span title=""> 
            <span class='tooltipHover'>Light Armour</span>
        </span></a>
    </td>
</tr>
<tr>
    <td>
        <img src='frontend/design/images/character/icons/heavy.png' class='skillIcon'>
        <input id="attributes" type="text" name="heavy_armour" disabled value='<?php echo $_SESSION['characterProperties']['heavy_armour'];if ($_SESSION['extraStats']['heavyArmour'] != 0){echo " (+" . $_SESSION['extraStats']['heavyArmour'] . ")";}?>'>
        <a title="Allows you to equip yourself with Heavy Armour" class="tooltipLeft"><span title=""> 
            <span class='tooltipHover'>Heavy Armour</span>
        </span></a>
    </td>
</tr>
<tr>
    <td>
        <img src='frontend/design/images/character/icons/shield.png' class='skillIcon'>
        <input id="attributes" type="text" name="shield" disabled value='<?php echo $_SESSION['characterProperties']['shield']; if ($_SESSION['extraStats']['shield'] != 0){echo " (+" . $_SESSION['extraStats']['shield'] . ")";}?>'>
        <a title="Makes you better at defending with a Shield" class="tooltipLeft"><span title=""> 
            <span class='tooltipHover'>Shield</span>
        </span></a>
    </td>
</tr>
<tr>
    <td>
        <img src='frontend/design/images/character/icons/parry.png' class='skillIcon'>
        <input id="attributes" type="text" name="parry" disabled value='<?php echo $_SESSION['characterProperties']['parry']; if ($_SESSION['extraStats']['parry'] != 0){echo " (+" . $_SESSION['extraStats']['parry'] . ")";}?>'>
        <a title="Increases your skill at blocking with your weapon" class="tooltipLeft"><span title=""> 
            <span class='tooltipHover'>Parry</span>
        </span></a>
    </td>
</tr>
<tr>
    <td>
        <img src='frontend/design/images/character/icons/foul.png' class='skillIcon'>
        <input id="attributes" type=text name="foul_play" disabled value='<?php echo $_SESSION['characterProperties']['foul_play']; if ($_SESSION['extraStats']['foul'] != 0){echo " (+" . $_SESSION['extraStats']['foul'] . ")";}?>'>
        <!--"Dignity and an empty sack is worth the sack - Rule of acquisition 109"--> 
        <a title="Allows you to do undignified attacks like throwing sand into your opponents eyes or a kick in the groin" class="tooltipLeft"><span title="">
            <span class='tooltipHover'>Foul Play</span>
        </span></a>
    </td>
</tr>
<tr>
    <td>
        <img src='frontend/design/images/character/icons/dodge.png' class='skillIcon'>
        <input id="attributes" type="text" name="dodge" value='<?php echo $_SESSION['characterProperties']['dodgeSkill']; if ($_SESSION['extraStats']['dodge'] != 0){echo " (+" . $_SESSION['extraStats']['dodge'] . ")";}?>'>
        <a title="A high dodge skill will help you avoid your opponents attacks" class="tooltipLeft"><span title=""> 
            <span class='tooltipHover'>Dodge</span>
        </span></a>
    </td>
</tr>
</tbody>
</table>

        
<?php unset($_SESSION['extraStats'])?>
	</tbody>
</table>