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
 * File: jailbust.php
 * Signature: e804c111c46807add9a40f11ac376803
 * Date: Fri, 20 Apr 12 08:50:30 +0000
 */

require_once('globals.php');
if ($ir['energy'] < 10)
{
    echo "Sorry, it costs 10 energy to bust someone. "
            . "You only have {$ir['energy']} energy. " . "Come back later.";
    die($h->endpage());
}
if ($ir['jail'])
{
    echo "You cannot bust out people while in jail.";
    die($h->endpage());
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
    echo "Invalid user";
    die($h->endpage());
}
$r = $db->fetch_row($jail_q);
$db->free_result($jail_q);
if (!$r['jail'])
{
    echo "That user is not in jail!";
    die($h->endpage());
}
$mult = $r['level'] * $r['level'];
$chance = min(($ir['crimexp'] / $mult) * 50 + 1, 95);
if (rand(1, 100) < $chance)
{
    $gain = $r['level'] * 5;
    echo "You successfully busted {$r['username']} out of jail.<br />
  	&gt; <a href='jail.php'>Back</a>";
    $db->query(
            "UPDATE `users`
    		 SET `crimexp` = `crimexp` + {$gain}, `energy` = `energy` - 10
    		 WHERE `userid` = $userid");
    $db->query(
            "UPDATE `users`
    		 SET `jail` = 0
    		 WHERE `userid` = {$r['userid']}");
    event_add($r['userid'],
        "<a href='viewuser.php?u={$ir['userid']}'>{$ir['username']}</a> busted you out of jail.");
}
else
{
    echo "While trying to bust out your friend, a guard spotted you and dragged you into jail yourself. Unlucky!<br />
  	&gt; <a href='jail.php'>Back</a>";
    $time = min($mult, 100);
    $jail_reason = $db->escape("Caught trying to bust out {$r['username']}");
    $db->query(
            "UPDATE `users`
             SET `jail` = $time, `jail_reason` = '{$jail_reason}',
             `energy` = `energy` - 10
             WHERE `userid` = $userid");
    event_add($r['userid'],
        "<a href='viewuser.php?u={$ir['userid']}'>{$ir['username']}</a> was caught trying to bust you out of jail.");
}
$h->endpage();
