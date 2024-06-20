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
 * File: jailbail.php
 * Signature: 76251b58f9059a3dd9d44800a94b23cd
 * Date: Fri, 20 Apr 12 08:50:30 +0000
 */

global $db, $ir, $userid, $h;
require_once('globals.php');

if ($ir['jail'])
{
    echo 'You cannot bail out people while in jail.';
    $h->endpage();
    exit;
}
$_GET['ID'] =
        (isset($_GET['ID']) && is_numeric($_GET['ID']))
                ? abs(intval($_GET['ID'])) : 0;
$jail_q =
        $db->query(
                "SELECT `userid`, `jail`, `level`, `username`
				 FROM `users`
				 WHERE `userid` = {$_GET['ID']}");
if ($db->num_rows($jail_q) == 0)
{
    $db->free_result($jail_q);
    echo 'Invalid user';
    $h->endpage();
    exit;
}
$r = $db->fetch_row($jail_q);
$db->free_result($jail_q);
if (!$r['jail'])
{
    echo 'That user is not in jail!';
    $h->endpage();
    exit;
}
$cost = $r['level'] * 2000;
$cf = money_formatter($cost);
if ($ir['money'] < $cost)
{
    echo "Sorry, you do not have enough money to bail out {$r['username']}."
            . " You need {$cf}.";
    $h->endpage();
    exit;
}

echo "You successfully bailed {$r['username']} out of jail for $cf.<br />
  &gt; <a href='jail.php'>Back</a>";
$db->query(
        "UPDATE `users`
		 SET `money` = `money` - {$cost}
		 WHERE `userid` = $userid");
$db->query(
        "UPDATE `users`
		 SET `jail` = 0
		 WHERE `userid` = {$r['userid']}");
event_add($r['userid'],
    "<a href='viewuser.php?u={$ir['userid']}'>{$ir['username']}</a> bailed you out of jail.");
$h->endpage();
