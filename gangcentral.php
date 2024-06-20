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
 * File: gangcentral.php
 * Signature: 41477db76f7da24587a941de1c059572
 * Date: Fri, 20 Apr 12 08:50:30 +0000
 */

require_once('globals.php');
echo "<h3>Gang Central</h3>
<a href='creategang.php'>&gt; Create A Gang Here</a><br />
<hr /><u>Gang Listings</u><br />
<table cellspacing=1 class='table'>
	<tr style='background:gray;'>
		<th>Gang</th>
		<th>Members</th>
		<th>President</th>
		<th>Respect Level</th>
	</tr>";
$gq =
        $db->query(
            'SELECT `gangID`, `gangNAME`, `gangRESPECT`,
                 `userid`, `username`
                 FROM `gangs` AS `g`
                 LEFT JOIN `users` AS `u` ON `g`.`gangPRESIDENT` = `u`.`userid`
                 ORDER BY `g`.`gangID` ASC');
while ($gangdata = $db->fetch_row($gq))
{
    echo "<tr>
    		<td><a href='gangs.php?action=view&ID={$gangdata['gangID']}'>{$gangdata['gangNAME']}</a></td>
    		<td>";
    $cnt =
            $db->query(
                    "SELECT COUNT(`userid`)
                     FROM `users`
                     WHERE `gang` = {$gangdata['gangID']}");
    print
            $db->fetch_single($cnt)
                    . "</td>
            <td><a href='viewuser.php?u={$gangdata['userid']}'>{$gangdata['username']}</a></td>
			<td>{$gangdata['gangRESPECT']}</td>
		</tr>";
    $db->free_result($cnt);
}
$db->free_result($gq);
echo '</table>';
$h->endpage();
