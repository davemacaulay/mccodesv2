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
$q =
        $db->query(
            'SELECT `userid`, `laston`, `username`, `level`, `money`,
 				 `user_level`
 				 FROM `users`
 				 WHERE `user_level` IN(2, 3, 5)
 				 ORDER BY `userid` ASC');
while ($r = $db->fetch_row($q))
{
    $staff[$r['userid']] = $r;
}
$db->free_result($q);
echo '
<b>Admins</b>
<br />
<table width="75%" cellspacing="1" cellpadding="1" class="table">
		<tr>
			<th>User</th>
			<th>Level</th>
			<th>Money</th>
			<th>Last Seen</th>
			<th>Status</th>
		</tr>
   ';

foreach ($staff as $r)
{
    if ($r['user_level'] == 2)
    {
        $on =
                ($r['laston'] >= ($_SERVER['REQUEST_TIME'] - 900))
                        ? '<span style="color: green;">Online</span>'
                        : '<span style="color: green;">Offline</span>';
        echo '
		<tr>
			<td><a href="viewuser.php?u=' . $r['userid'] . '">'
                . $r['username'] . '</a> [' . $r['userid'] . ']</td>
			<td>' . $r['level'] . '</td>
			<td>' . money_formatter((int)$r['money']) . '</td>
			<td>' . date('F j, Y, g:i:s a', (int)$r['laston']) . '</td>
			<td>' . $on . '</td>
		</tr>
   		';
    }
}
echo '</table>

<b>Secretaries</b>
<br />
<table width="75%" cellspacing="1" cellpadding="1" class="table">
		<tr>
			<th>User</th>
			<th>Level</th>
			<th>Money</th>
			<th>Last Seen</th>
			<th>Status</th>
		</tr>
   ';
foreach ($staff as $r)
{
    if ($r['user_level'] == 3)
    {
        $on =
                ($r['laston'] >= ($_SERVER['REQUEST_TIME'] - 900))
                        ? '<span style="color: green;">Online</span>'
                        : '<span style="color: green;">Offline</span>';
        echo '
		<tr>
			<td><a href="viewuser.php?u=' . $r['userid'] . '">'
                . $r['username'] . '</a> [' . $r['userid'] . ']</td>
			<td>' . $r['level'] . '</td>
			<td>' . money_formatter((int)$r['money']) . '</td>
			<td>' . date('F j, Y, g:i:s a', (int)$r['laston']) . '</td>
			<td>' . $on . '</td>
		</tr>
   		';
    }
}
echo '</table>

<b>Assistants</b>
<br />
<table width="75%" cellspacing="1" cellpadding="1" class="table">
		<tr>
			<th>User</th>
			<th>Level</th>
			<th>Money</th>
			<th>Last Seen</th>
			<th>Status</th>
		</tr>
   ';
foreach ($staff as $r)
{
    if ($r['user_level'] == 5)
    {
        $on =
                ($r['laston'] >= ($_SERVER['REQUEST_TIME'] - 900))
                        ? '<span style="color: green;">Online</span>'
                        : '<span style="color: green;">Offline</span>';
        echo '
		<tr>
			<td><a href="viewuser.php?u=' . $r['userid'] . '">'
                . $r['username'] . '</a> [' . $r['userid'] . ']</td>
			<td>' . $r['level'] . '</td>
			<td>' . money_formatter((int)$r['money']) . '</td>
			<td>' . date('F j, Y, g:i:s a', (int)$r['laston']) . '</td>
			<td>' . $on . '</td>
		</tr>
   		';
    }
}
echo '</table>';
$h->endpage();
