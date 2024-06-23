<?php
declare(strict_types=1);
/**
 * MCCodes v2 by Dabomstew & ColdBlooded
 * 
 * Repository: https://github.com/davemacaulay/mccodesv2
 * License: MIT License
 */

global $db, $h;
require_once('globals.php');
$q =
        $db->query(
            'SELECT `fed_userid`, `fed_days`, `fed_reason`, `fed_jailedby`,
                `u`.`username`, `u2`.`username` AS `jailer`
                FROM `fedjail` AS `f`
                LEFT JOIN `users` AS `u`
                ON `f`.`fed_userid` = `u`.`userid`
                LEFT JOIN `users` AS `u2`
                ON `f`.`fed_jailedby` = `u2`.`userid`
                ORDER BY `f`.`fed_days` ASC');
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
echo '</table>';
$q =
        $db->query(
            'SELECT `userid`, `username`, `mailban`, `mb_reason`
				 FROM `users`
				 WHERE `mailban` > 0
				 ORDER BY `mailban` ASC');
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
echo '</table>';
$h->endpage();
