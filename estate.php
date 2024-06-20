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
 * File: estate.php
 * Signature: b80d19463d6a82f1ba97a2adb776f3f3
 * Date: Fri, 20 Apr 12 08:50:30 +0000
 */

require_once('globals.php');
$mpq =
        $db->query(
                "SELECT *
				 FROM `houses`
				 WHERE `hWILL` = {$ir['maxwill']}
				 LIMIT 1");
$mp = $db->fetch_row($mpq);
$db->free_result($mpq);
if (isset($_GET['property']) && is_numeric($_GET['property']))
{
    $_GET['property'] = abs((int) $_GET['property']);
    $npq =
            $db->query(
                    "SELECT `hWILL`, `hPRICE`, `hNAME`
    				 FROM `houses`
    				 WHERE `hID` = {$_GET['property']}");
    if ($db->num_rows($npq) == 0)
    {
        $db->free_result($npq);
        echo "That house doesn't exist.";
        $h->endpage();
        exit;
    }
    $np = $db->fetch_row($npq);
    $db->free_result($npq);
    if ($np['hWILL'] < $mp['hWILL'])
    {
        echo 'You cannot go backwards in houses!';
    }
    else if ($np['hPRICE'] > $ir['money'])
    {
        echo "You do not have enough money to buy the {$np['hNAME']}.";
    }
    else
    {
        $db->query(
                "UPDATE `users`
                 SET `money` = `money` - {$np['hPRICE']},
                 `will` = 0, `maxwill` = {$np['hWILL']}
                 WHERE `userid` = $userid");
        echo "Congrats, you bought the {$np['hNAME']} for "
                . money_formatter($np['hPRICE']) . '!';
    }
}
else if (isset($_GET['sellhouse']))
{
    if ($ir['maxwill'] == 100)
    {
        echo 'You already live in the lowest property!';
    }
    else
    {
        $db->query(
                "UPDATE `users`
                SET `money` = `money` + {$mp['hPRICE']},
                `will` = 0, `maxwill` = 100
                WHERE `userid` = $userid");
        echo "You sold your {$mp['hNAME']} and went back to your shed.";
    }
}
else
{
    echo "Your current property: <b>{$mp['hNAME']}</b><br />
The houses you can buy are listed below. Click a house to buy it.<br />";
    if ($ir['maxwill'] > 100)
    {
        echo "<a href='estate.php?sellhouse'>Sell Your House</a><br />";
    }
    $hq =
            $db->query(
                    "SELECT *
                     FROM `houses`
                     WHERE `hWILL` > {$ir['maxwill']}
                     ORDER BY `hWILL` ASC");
    while ($r = $db->fetch_row($hq))
    {
        echo "<a href='estate.php?property={$r['hID']}'>{$r['hNAME']}</a>"
                . '&nbsp;&nbsp - Cost: ' . money_formatter($r['hPRICE'])
                . "&nbsp;&nbsp - Will Bar: {$r['hWILL']}<br />";
    }
    $db->free_result($hq);
}
$h->endpage();
