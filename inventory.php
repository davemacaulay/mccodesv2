<?php
declare(strict_types=1);
/**
 * MCCodes v2 by Dabomstew & ColdBlooded
 * 
 * Repository: https://github.com/davemacaulay/mccodesv2
 * License: MIT License
 */

global $db, $ir, $userid, $h;
require_once('globals.php');
$q =
        $db->query(
                "SELECT `itmid`, `itmname`
                 FROM `items`
                 WHERE `itmid`
                  IN({$ir['equip_primary']}, {$ir['equip_secondary']},
                     {$ir['equip_armor']})");
echo '<h3>Equipped Items</h3><hr />';
$equip = [];
while ($r = $db->fetch_row($q))
{
    $equip[$r['itmid']] = $r;
}
$db->free_result($q);
echo "<table width='75%' cellspacing='1' class='table'>
<tr>
<th>Primary Weapon</th>
<td>";
if (isset($equip[$ir['equip_primary']]))
{
    print
            $equip[$ir['equip_primary']]['itmname']
                    . "</td><td><a href='unequip.php?type=equip_primary'>Unequip Item</a></td>";
}
else
{
    echo 'None equipped.</td><td>&nbsp;</td>';
}
echo '</tr>
<tr>
<th>Secondary Weapon</th>
<td>';
if (isset($equip[$ir['equip_secondary']]))
{
    print
            $equip[$ir['equip_secondary']]['itmname']
                    . "</td><td><a href='unequip.php?type=equip_secondary'>Unequip Item</a></td>";
}
else
{
    echo 'None equipped.</td><td>&nbsp;</td>';
}
echo '</tr>
<tr>
<th>Armor</th>
<td>';
if (isset($equip[$ir['equip_armor']]))
{
    print
            $equip[$ir['equip_armor']]['itmname']
                    . "</td><td><a href='unequip.php?type=equip_armor'>Unequip Item</a></td>";
}
else
{
    echo 'None equipped.</td><td>&nbsp;</td>';
}
echo '</tr>
</table><hr />
<h3>Inventory</h3><hr />';
$inv =
        $db->query(
                "SELECT `inv_qty`, `itmsellprice`, `itmid`, `inv_id`,
                 `effect1_on`, `effect2_on`, `effect3_on`, `itmname`,
                 `weapon`, `armor`, `itmtypename`
                 FROM `inventory` AS `iv`
                 INNER JOIN `items` AS `i`
                 ON `iv`.`inv_itemid` = `i`.`itmid`
                 INNER JOIN `itemtypes` AS `it`
                 ON `i`.`itmtype` = `it`.`itmtypeid`
                 WHERE `iv`.`inv_userid` = {$userid}
                 ORDER BY `i`.`itmtype` ASC, `i`.`itmname` ASC");
if ($db->num_rows($inv) == 0)
{
    echo '<b>You have no items!</b>';
}
else
{
    echo "<b>Your items are listed below.</b><br />
<table width=100% class=\"table\" border=\"0\" cellspacing=\"1\">
	<tr>
		<td class=\"h\">Item</td>
		<td class=\"h\">Sell Value</td>
		<td class=\"h\">Total Sell Value</td>
		<td class=\"h\">Links</td>
	</tr>";
    $lt = '';
    while ($i = $db->fetch_row($inv))
    {
        if ($lt != $i['itmtypename'])
        {
            $lt = $i['itmtypename'];
            echo "\n<tr>
            			<td colspan='4'>
            				<b>{$lt}</b>
            			</td>
            		</tr>";
        }
        if ($i['weapon'])
        {
            $i['itmname'] =
                    "<span style='color: red;'>*</span>" . $i['itmname'];
        }
        if ($i['armor'])
        {
            $i['itmname'] =
                    "<span style='color: green;'>*</span>" . $i['itmname'];
        }
        echo "<tr>
        		<td>{$i['itmname']}";
        if ($i['inv_qty'] > 1)
        {
            echo "&nbsp;x{$i['inv_qty']}";
        }
        echo '</td>
        	  <td>' . money_formatter((int)$i['itmsellprice'])
                . '</td>
        	  <td>';
        echo money_formatter((int)($i['itmsellprice'] * $i['inv_qty']));
        echo "</td>
        	  <td>
        	  	[<a href='iteminfo.php?ID={$i['itmid']}'>Info</a>]
        	  	[<a href='itemsend.php?ID={$i['inv_id']}'>Send</a>]
        	  	[<a href='itemsell.php?ID={$i['inv_id']}'>Sell</a>]
        	  	[<a href='imadd.php?ID={$i['inv_id']}'>Add To Market</a>]";
        if ($i['effect1_on'] || $i['effect2_on'] || $i['effect3_on'])
        {
            echo " [<a href='itemuse.php?ID={$i['inv_id']}'>Use</a>]";
        }
        if ($i['weapon'] > 0)
        {
            echo " [<a href='equip_weapon.php?ID={$i['inv_id']}'>Equip as Weapon</a>]";
        }
        if ($i['armor'] > 0)
        {
            echo " [<a href='equip_armor.php?ID={$i['inv_id']}'>Equip as Armor</a>]";
        }
        echo '</td>
        </tr>';
    }
    echo '</table>';
    $db->free_result($inv);
    echo "<small><b>NB:</b> Items with a small red </small><span style='color: red;'>*</span><small> next to their name can be used as weapons in combat.<br />
Items with a small green </small><span style='color: green;'>*</span><small> next to their name can be used as armor in combat.</small>";
}
$h->endpage();
