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
 * File: stats.php
 * Signature: 762bb7219351721312321e4dd0bf47c3
 * Date: Fri, 20 Apr 12 08:50:30 +0000
 */

global $db, $h, $set;
require_once('globals.php');
// Basic Stats (all users)
$q =
        $db->query(
                "SELECT COUNT(`userid`) AS `c_users`,
				 SUM(`money`) AS `s_money`,
				 SUM(`crystals`) AS `s_crystals`,
                 SUM(IF(`bankmoney` > -1, 1, 0)) AS `c_users_bank`,
                 SUM(IF(`bankmoney` > -1, `bankmoney`, 0)) AS `s_bank`,
                 SUM(IF(`gender` = 'Male', 1, 0)) AS `c_male`,
                 SUM(IF(`gender` = 'Female', 1, 0)) AS `c_female`
                 FROM `users`");
$mem_info = $db->fetch_row($q);
$membs = $mem_info['c_users'];
$total = $mem_info['s_money'];
$avg = (int) ($total / (max($membs, 1)));
$totalc = $mem_info['s_crystals'];
$avgc = (int) ($totalc / (max($membs, 1)));
$banks = $mem_info['c_users_bank'];
$totalb = $mem_info['s_bank'];
$avgb = (int) ($totalb / ($banks > 0 ? $banks : 1));
$male = $mem_info['c_male'];
$fem = $mem_info['c_female'];
$db->free_result($q);
$q = $db->query('SELECT SUM(`inv_qty`)
				 FROM `inventory`');
$totali = $db->fetch_single($q);
$db->free_result($q);
$q = $db->query('SELECT COUNT(`mail_id`)
				 FROM `mail`');
$mail = $db->fetch_single($q);
$db->free_result($q);
$q = $db->query('SELECT COUNT(`evID`)
				 FROM `events`');
$events = $db->fetch_single($q);
$db->free_result($q);
echo "<h3>{$set['game_name']} Statistics</h3>
You step into the Statistics Department and login to the service. You see some stats that interest you.<br />
<table width='75%' cellspacing='1' class='table'>
	<tr>
		<th>Users</th>
		<th>Money and Crystals</th>
	</tr>
	<tr>
		<td>
			There are currently $membs {$set['game_name']} players,
                $male males and $fem females.
        </td>
        <td>
			Amount of cash in circulation: " . money_formatter($total)
        . '. <br />
			The average player has: ' . money_formatter($avg)
        . '. <br />
			Amount of cash in banks: ' . money_formatter($totalb)
        . ". <br />
			Amount of players with bank accounts: $banks<br />
			The average player has in their bank accnt: "
        . money_formatter($avgb)
        . '. <br />
			Amount of crystals in circulation: '
        . money_formatter($totalc, '')
        . '. <br />
			The average player has: ' . money_formatter($avgc, '')
        . ' crystals.
        </td>
    </tr>
	<tr>
		<th>Mails/Events</th>
		<th>Items</th>
	</tr>
	<tr>
		<td>
			' . money_formatter($mail, '') . ' mails and '
        . money_formatter($events, '')
        . ' events have been sent.
        </td>
        <td>
			There are currently ' . money_formatter($totali, '')
        . ' items in circulation.
        </td>
    </tr>
 </table>';
$h->endpage();
