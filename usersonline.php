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
 * File: usersonline.php
 * Signature: 8b411fbec53e644bcab7f34abfe66df3
 * Date: Fri, 20 Apr 12 08:50:30 +0000
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
