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
 * File: polls_view.php
 * Signature: e97b41468f1ee92f437951b5ff1909bf
 * Date: Fri, 20 Apr 12 08:50:30 +0000
 */

global $db, $h;
require_once('globals.php');
$q =
        $db->query(
                "SELECT *
				 FROM `polls`
				 WHERE `active` = '0'
				 ORDER BY `id` DESC");
if (!$db->num_rows($q))
{
    echo '<b>There are no finished polls right now</b>';
}
else
{
    while ($r = $db->fetch_row($q))
    {
        echo "<table cellspacing='1' width='75%' class='table'>
        		<tr>
        			<th>Choice</th>
        			<th>Votes</th>
        			<th width='100'>Bar</th>
        			<th>Percentage</th>
        		</tr>
        		<tr>
        			<th colspan='4'>{$r['question']}</th>
        		</tr>";
        for ($i = 1; $i <= 10; $i++)
        {
            if ($r['choice' . $i])
            {
                $k = 'choice' . $i;
                $ke = 'voted' . $i;
                if ($r['votes'] != 0)
                {
                    $perc = $r[$ke] / $r['votes'] * 100;
                }
                else
                {
                    $perc = 0;
                }
                echo "<tr>
                		<td>{$r[$k]}</td>
                		<td>{$r[$ke]}</td>
                		<td>
                			<img src='bargreen.gif' alt='Bar' width='$perc' height='10' />
                		</td>
                		<td>$perc%</td>
                	  </tr>";
            }
        }
        echo "<tr>
        		<th colspan='4'>Total Votes: {$r['votes']}</th>
        	  </tr>
			  <tr>
			  	<th colspan='4'>Winner: " . $r['choice' . $r['winner']]
                . '</th>
              </tr>
		</table><br />';
    }
}
$db->free_result($q);
$h->endpage();
