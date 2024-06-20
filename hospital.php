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
 * File: hospital.php
 * Signature: 57d80a7174cdc4370092d74b6550351d
 * Date: Fri, 20 Apr 12 08:50:30 +0000
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
