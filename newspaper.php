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
echo '<h3>The MonoPaper</h3>';
$paperQ = $db->query('SELECT `content`
					  FROM `papercontent`');
$paper = $db->fetch_single($paperQ);
$db->free_result($paperQ);
echo '
<table width="75%" cellspacing="1" class="table">
		<tr style="text-align: center; font-weight: bold;">
			<td width="34%"><a href="job.php">YOUR JOB</a></td>
			<td width="34%"><a href="gym.php">LOCAL GYM</a></td>
			<td width="34%"><a href="halloffame.php">HALL OF FAME</a></td>
		</tr>
		<tr>
			<td width="34%"><img src="ad_filler.png" alt="Ad" title="Ad" /></td>
			<td colspan="2">' . nl2br($paper)
        . '</td>
		</tr>
</table>
   ';
$h->endpage();
