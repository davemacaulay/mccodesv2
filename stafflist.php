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
 * File: stafflist.php
 * Signature: fbca6a2d04d5db023e507f742ab7f0fd
 * Date: Fri, 20 Apr 12 08:50:30 +0000
 * @noinspection SpellCheckingInspection
 */

global $db, $h;
require_once('globals.php');
$staff = [];
$q     = $db->query(
    'SELECT u.userid, u.laston, u.username, u.level, u.money, GROUP_CONCAT(sr.name ORDER BY sr.id) AS roles
    FROM users AS u
    INNER JOIN users_roles AS ur ON ur.userid = u.userid
    INNER JOIN staff_roles AS sr ON sr.id = ur.staff_role
    WHERE ur.staff_role > 0
    GROUP BY u.userid
    ORDER BY u.userid'
);
while ($r = $db->fetch_row($q)) {
    $staff[$r['userid']] = $r;
}
$db->free_result($q);
echo '
<b>Staff</b>
<br />
<table style="width: 75%; padding: 1px;" class="table">
		<tr>
			<th>User</th>
			<th>Roles</th>
			<th>Level</th>
			<th>Money</th>
			<th>Last Seen</th>
			<th>Status</th>
		</tr>
   ';

foreach ($staff as $r) {
    $on = ($r['laston'] >= ($_SERVER['REQUEST_TIME'] - 900))
        ? '<span style="color: green;">Online</span>'
        : '<span style="color: green;">Offline</span>';
    echo '
        <tr>
            <td><a href="viewuser.php?u=' . $r['userid'] . '">' . $r['username'] . '</a> [' . $r['userid'] . ']</td>
            <td>' . str_replace(',', ', ', $r['roles']) . '</td>
            <td>' . $r['level'] . '</td>
            <td>' . money_formatter((int)$r['money']) . '</td>
            <td>' . date('F j, Y, g:i:s a', (int)$r['laston']) . '</td>
            <td>' . $on . '</td>
        </tr>
    ';
}
echo '</table>';
$h->endpage();
