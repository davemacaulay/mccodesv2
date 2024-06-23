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
echo "
<h3>Jail</h3>
<table width='75%' class='table' cellspacing='1' cellpadding='1'>
		<tr>
			<th>Name</th>
			<th>Level</th>
			<th>Time</th>
			<th>Reason</th>
			<th>Actions</th>
		</tr>
   ";
$q =
        $db->query(
            'SELECT `jail_reason`, `jail`, `level`, `username`, `userid`,
                `gangPREF`
                FROM `users` AS `u`
                LEFT JOIN `gangs` AS `g`
                ON `u`.`gang` = `g`.`gangID`
                WHERE `u`.`jail` > 0
                ORDER BY `u`.`jail` DESC');
while ($r = $db->fetch_row($q))
{
    echo "
		<tr>
			<td>
                {$r['gangPREF']}
				<a href='viewuser.php?u={$r['userid']}'>{$r['username']}</a>
				[{$r['userid']}]
			</td>
			<td>{$r['level']}</td>
			<td>{$r['jail']} minutes</td>
			<td>{$r['jail_reason']}</td>
			<td>
				[<a href='jailbust.php?ID={$r['userid']}'>Bust</a>]
				[<a href='jailbail.php?ID={$r['userid']}'>Bail</a>]
			</td>
		</tr>
   ";
}
$db->free_result($q);
echo '</table>';
$h->endpage();
