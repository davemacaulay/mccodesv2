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
 * File: newspaper.php
 * Signature: 29b72881af2e93d213b875e18928398b
 * Date: Fri, 20 Apr 12 08:50:30 +0000
 */

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
