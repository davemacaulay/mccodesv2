<?php
declare(strict_types=1);
/**
 * MCCodes v2 by Dabomstew & ColdBlooded
 * 
 * Repository: https://github.com/davemacaulay/mccodesv2
 * License: MIT License
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
