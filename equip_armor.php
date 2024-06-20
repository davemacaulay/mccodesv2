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
 * File: equip_armor.php
 * Signature: c5e0754d10032ab2a6bc8abd437cdd7a
 * Date: Fri, 20 Apr 12 08:50:30 +0000
 */

global $db, $ir, $userid, $h;
require_once('globals.php');
$_GET['ID'] =
        (isset($_GET['ID']) && is_numeric($_GET['ID']))
                ? abs((int) $_GET['ID']) : 0;
$id =
        $db->query(
                "SELECT `armor`, `itmid`, `itmname`
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
if ($r['armor'] <= 0)
{
    echo 'This item cannot be equipped to this slot.';
    $h->endpage();
    exit;
}
if (isset($_POST['type']))
{
    if ($_POST['type'] !== 'equip_armor')
    {
        echo 'This slot ID is not valid.';
        $h->endpage();
        exit;
    }
    if ($ir['equip_armor'] > 0)
    {
        item_add($userid, $ir['equip_armor'], 1);
    }
    item_remove($userid, $r['itmid'], 1);
    $db->query(
            "UPDATE `users`
             SET `equip_armor` = {$r['itmid']}
             WHERE `userid` = {$userid}");
    echo "Item {$r['itmname']} equipped successfully.";
}
else
{
    echo "<h3>Equip Armor</h3><hr />
<form action='equip_armor.php?ID={$_GET['ID']}' method='post'>
Click Equip Armor to equip {$r['itmname']} as your armor,
 if you currently have any armor equipped it will be removed back
 to your inventory.<br />
<input type='hidden' name='type' value='equip_armor'  />
<input type='submit' value='Equip Armor' />
</form>";
}
$h->endpage();
