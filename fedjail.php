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
 * File: fedjail.php
 * Signature: a1e32ec05c1563fb829cce6859022551
 * Date: Fri, 20 Apr 12 08:50:30 +0000
 */

require_once('globals.php');
$q =
        $db->query(
                "SELECT `fed_userid`, `fed_days`, `fed_reason`, `fed_jailedby`,
                `u`.`username`, `u2`.`username` AS `jailer`
                FROM `fedjail` AS `f`
                LEFT JOIN `users` AS `u`
                ON `f`.`fed_userid` = `u`.`userid`
                LEFT JOIN `users` AS `u2`
                ON `f`.`fed_jailedby` = `u2`.`userid`
                ORDER BY `f`.`fed_days` ASC");
echo "<b>Federal Jail</b><br />
If you ever cheat the game your name will become a permanent
	part of this list...<br />
<table cellspacing='1' class='table'>
	<tr style='background:gray;'>
		<th>Who</th>
		<th>Days</th>
		<th>Reason</th>
		<th>Jailer</th>
	</tr>";
while ($r = $db->fetch_row($q))
{
    echo "<tr>
    	<td>
    		<a href='viewuser.php?u={$r['fed_userid']}'>{$r['username']}</a>
    	</td>
    	<td>{$r['fed_days']}</td>
    	<td>{$r['fed_reason']}</td>
    	<td>
    		<a href='viewuser.php?u={$r['fed_jailedby']}'>{$r['jailer']}</a>
    	</td>
    	</tr>";
}
$db->free_result($q);
echo "</table>";
$q =
        $db->query(
                "SELECT `userid`, `username`, `mailban`, `mb_reason`
				 FROM `users`
				 WHERE `mailban` > 0
				 ORDER BY `mailban` ASC");
echo "<b>Mail Ban</b><br />
If you ever swear or do other bad things with mail,
	your name will become a permanent part of this list...<br />
<table width='100%' cellspacing='1' class='table'>
	<tr style='background:gray;'>
		<th>Who</th>
		<th>Days</th>
		<th>Reason</th>
	</tr>";
while ($r = $db->fetch_row($q))
{
    echo "
    <tr>
    	<td><a href='viewuser.php?u={$r['userid']}'>{$r['username']}</a></td>
    	<td>{$r['mailban']}</td>
    	<td>{$r['mb_reason']}</td>
    </tr>";
}
$db->free_result($q);
echo "</table>";
$h->endpage();
