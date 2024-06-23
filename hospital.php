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
<h3>Hospital</h3>
<table width='75%' class='table' border='0' cellspacing='1' cellpadding='1'>
		<tr bgcolor='gray'>
			<th>Name</th>
			<th>Level</th>
			<th>Time</th>
			<th>Reason</th>
		</tr>
   ";
$q =
        $db->query(
                'SELECT `userid`, `username`, `hospital`, `level`,
                 `hospreason`, `gangPREF`
                 FROM `users` AS `u`
                 LEFT JOIN `gangs` AS `g`
                 ON `u`.`gang` = `g`.`gangID`
                 WHERE `u`.`hospital` > 0
                 ORDER BY `u`.`hospital` DESC');
while ($r = $db->fetch_row($q))
{
    echo "
		<tr>
			<td>{$r['gangPREF']} <a href='viewuser.php?u={$r['userid']}'>{$r['username']}</a> [{$r['userid']}]</td>
			<td>{$r['level']}</td>
			<td>{$r['hospital']} minutes</td>
			<td>{$r['hospreason']}</td>
		</tr>
   ";
}
$db->free_result($q);
echo '</table>';
$h->endpage();
