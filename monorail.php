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
 * File: monorail.php
 * Signature: c9f70e6e1a81857fb1588bed4a693203
 * Date: Fri, 20 Apr 12 08:50:30 +0000
 */

global $db, $ir, $userid, $h;
require_once('globals.php');
# Basic config setting
$cost_of_travel = 1000;
# end
$_GET['to'] =
        (isset($_GET['to']) && is_numeric($_GET['to']))
                ? abs(intval($_GET['to'])) : '';
if (empty($_GET['to']))
{
    echo '
	Welcome to the Monorail Station. It costs '
            . money_formatter($cost_of_travel)
            . ' for a ticket.
	<br />
	Where would you like to travel today?
	<br />
   	';
    $q =
            $db->query(
                    "SELECT `cityid`, `cityname`, `citydesc`, `cityminlevel`
                     FROM `cities`
                     WHERE `cityid` != {$ir['location']}
                     AND `cityminlevel` <= {$ir['level']}");
    echo "
	<table width='75%' cellspacing='1' cellpadding='1' class='table'>
		<tr style='background:gray'>
			<th>Name</th>
			<th>Description</th>
			<th>Min Level</th>
			<th>&nbsp;</th>
		</tr>
   	";
    while ($r = $db->fetch_row($q))
    {
        echo "
		<tr>
			<td>{$r['cityname']}</td>
			<td>{$r['citydesc']}</td>
			<td>{$r['cityminlevel']}</td>
			<td><a href='monorail.php?to={$r['cityid']}'>Go</a></td>
		</tr>
   		";
    }
    echo '</table>';
    $db->free_result($q);
}
else
{
    if ($ir['money'] < $cost_of_travel)
    {
        echo 'You don\'t have enough money.';
    }
    elseif ($ir['location'] == $_GET['to'])
    {
        echo 'You are already here.';
    }
    else
    {
        $q =
                $db->query(
                        "SELECT `cityname`
                         FROM `cities`
                         WHERE `cityid` = {$_GET['to']}
                         AND `cityminlevel` <= {$ir['level']}");
        if (!$db->num_rows($q))
        {
            echo 'Error, this city either does not exist or you cannot go there.';
        }
        else
        {
            $db->query(
                    "UPDATE `users`
                     SET `money` = `money` - $cost_of_travel,
                     `location` = {$_GET['to']}
                     WHERE `userid` = $userid");
            $cityName = $db->fetch_single($q);
            echo 'Congratulations, you paid '
                    . money_formatter($cost_of_travel) . ' and travelled to '
                    . $cityName . ' on the monorail!';
        }
        $db->free_result($q);
    }
    echo '<br />&gt; <a href="index.php">Go back</a> to index.';
}
$h->endpage();
