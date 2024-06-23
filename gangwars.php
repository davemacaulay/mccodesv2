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
