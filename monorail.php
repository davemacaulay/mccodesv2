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
