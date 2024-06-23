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
$_GET['ID'] =
        (isset($_GET['ID']) && is_numeric($_GET['ID']))
                ? abs((int) $_GET['ID']) : 0;
$id =
        $db->query(
                "SELECT `weapon`, `itmid`, `itmname`
                FROM `inventory` AS `iv`
                LEFT JOIN `items` AS `it`
                ON `iv`.`inv_itemid` = `it`.`itmid`
                WHERE `iv`.`inv_id` = {$_GET['ID']}
                AND `iv`.`inv_userid` = $userid
        		LIMIT 1");
if ($db->num_rows($id) == 0)
{
    $db->free_result($id);
    echo 'Invalid item ID';
    $h->endpage();
    exit;
}
else
{
    $r = $db->fetch_row($id);
    $db->free_result($id);
}
if (!$r['weapon'])
{
    echo 'This item cannot be equipped to this slot.';
    $h->endpage();
    exit;
}
if (isset($_POST['type']))
{
    if (!in_array($_POST['type'], ['equip_primary', 'equip_secondary'],
            true))
    {
        echo 'This slot ID is not valid.';
        $h->endpage();
        exit;
    }
    if ($ir[$_POST['type']] > 0)
    {
        item_add($userid, $ir[$_POST['type']], 1);
    }
    item_remove($userid, $r['itmid'], 1);
    $db->query(
            "UPDATE `users`
            SET `{$_POST['type']}` = {$r['itmid']}
            WHERE `userid` = {$userid}");
    echo "Item {$r['itmname']} equipped successfully.";
}
else
{
    echo "<h3>Equip Weapon</h3><hr />
<form action='equip_weapon.php?ID={$_GET['ID']}' method='post'>
Please choose the slot to equip {$r['itmname']} to,
 if there is already a weapon in that slot,
 it will be removed back to your inventory.<br />
<input type='radio' name='type' value='equip_primary' checked='checked' />
	Primary<br />
<input type='radio' name='type' value='equip_secondary' />
	Secondary<br />
<input type='submit' value='Equip Weapon' />
</form>";
}
$h->endpage();
