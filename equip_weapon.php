<?php
/**
 * MCCodes Version 2.0.5b
 * Copyright (C) 2005-2012 Dabomstew
 * All rights reserved.
 *
 * Redistribution of this code in any form is prohibited, except in
 * the specific cases set out in the MCCodes Customer License.
 *
 * This code license may be used to run one (1) game.
 * A game is defined as the set of users and other game database data,
 * so you are permitted to create alternative clients for your game.
 *
 * If you did not obtain this code from MCCodes.com, you are in all likelihood
 * using it illegally. Please contact MCCodes to discuss licensing options
 * in this case.
 *
 * File: equip_weapon.php
 * Signature: 5fa6ce6f751fd7f09cde14b86ba287bf
 * Date: Fri, 20 Apr 12 08:50:30 +0000
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
