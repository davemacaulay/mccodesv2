<?php
declare(strict_types=1);
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
 * File: unequip.php
 * Signature: 85c38b62c752779b15f7c9aa934c566b
 * Date: Fri, 20 Apr 12 08:50:30 +0000
 */

global $db, $ir, $userid, $h;
require_once('globals.php');
if (!isset($_GET['type'])
        || !in_array($_GET['type'],
                ['equip_primary', 'equip_secondary', 'equip_armor'],
                true))
{
    echo 'This slot ID is not valid.';
    die($h->endpage());
}
if ($ir[$_GET['type']] == 0)
{
    echo 'You do not have anything equipped in this slot.';
    die($h->endpage());
}
item_add($userid, $ir[$_GET['type']], 1);
$db->query(
        "UPDATE `users`
        SET `{$_GET['type']}` = 0
        WHERE `userid` = {$ir['userid']}");
$names =
        ['equip_primary' => 'Primary Weapon',
                'equip_secondary' => 'Secondary Weapon',
                'equip_armor' => 'Armor'];
echo 'The item in your ' . $names[$_GET['type']]
        . ' slot was successfully unequiped.';
$h->endpage();
