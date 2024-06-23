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
echo '<h3>Users Online</h3>';
$cn = 0;
$expiry_time = time() - 900;
$q =
        $db->query(
                'SELECT `userid`, `username`, `laston`
                 FROM `users`
                 WHERE `laston` > ' . $expiry_time
                        . '
                 ORDER BY `laston` DESC');
while ($r = $db->fetch_row($q))
{
    $cn++;
    echo $cn . '. <a href="viewuser.php?u=' . $r['userid'] . '">'
            . $r['username'] . '</a> (' . datetime_parse($r['laston'])
            . ')
	<br />
   	';
}
$db->free_result($q);
$h->endpage();
