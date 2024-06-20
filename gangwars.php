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
 * File: gangwars.php
 * Signature: 0cbb9a818e3f1deb065aa21a93e9d932
 * Date: Fri, 20 Apr 12 08:50:30 +0000
 */

require_once('globals.php');
echo "<h3>Gang Wars</h3>
<table width='75%' cellspacing='1' class='table'>";
$q =
        $db->query(
                "SELECT `w`.*, `g1`.`gangNAME` AS `declarer`,
                 `g1`.`gangRESPECT` AS `drespect`,
                 `g2`.`gangNAME` AS `defender`,
                 `g2`.`gangRESPECT` AS `frespect`
                 FROM `gangwars` AS `w`
                 INNER JOIN `gangs` AS `g1`
                 ON `w`.`warDECLARER` = `g1`.`gangID`
                 INNER JOIN `gangs` AS `g2`
                 ON `w`.`warDECLARED` = `g2`.`gangID`
                 WHERE `g1`.`gangNAME` != ''
                 AND `g2`.`gangNAME` != ''");
if ($db->num_rows($q) > 0)
{
    while ($r = $db->fetch_row($q))
    {
        echo "<tr>
        		<td width='45%'>
        			<a href='gangs.php?action=view&amp;ID={$r['warDECLARER']}'>
                    {$r['declarer']}
                    </a> [{$r['drespect']} respect]
                </td>
                <td width='10%'>vs.</td>
                <td width='45%'>
                	<a href='gangs.php?action=view&amp;ID={$r['warDECLARED']}'>
                    {$r['defender']}
                    </a> [{$r['frespect']} respect]
                </td>
              </tr>";
    }
    echo '</table>';
}
else
{
    echo '</table>There are currently no gang wars in progress.';
}
$db->free_result($q);
$h->endpage();
