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
 * File: search.php
 * Signature: 1b274d6b9f74ecf54e67516602ac5d5d
 * Date: Fri, 20 Apr 12 08:50:30 +0000
 */

global $db, $ir, $h;
require_once('globals.php');
echo "<h3>Search</h3>
<b>Search by Name</b>
<form action='searchname.php' method='POST'>
	<input type='text' name='name' /><br />
	<input type='submit' value='Search' />
</form><hr />
<b>Search by ID</b>
<form action='viewuser.php' method='get'>
	<input type='text' name='u' /><br />
	<input type='submit' value='Search' />
</form>";
echo "<hr /><b>Search by Location</b>
<form action='searchlocation.php' method='POST'>
	<select name='location' type='dropdown'>";

$q =
        $db->query(
                "SELECT `cityid`, `cityname`
                 FROM `cities`
                 WHERE `cityminlevel` <= {$ir['level']}");
while ($r = $db->fetch_row($q))
{
    echo "<option value='{$r['cityid']}'>{$r['cityname']}</option>";
}
$db->free_result($q);
echo "</select><br />
	<input type='submit' value='Search' />
</form>";
$h->endpage();
