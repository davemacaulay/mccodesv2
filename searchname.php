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
 * File: searchname.php
 * Signature: ba28b424080aac6e92b4259a10d17803
 * Date: Fri, 20 Apr 12 08:50:30 +0000
 */

global $db, $h;
require_once('globals.php');
$_POST['name'] =
        (isset($_POST['name']) && is_string($_POST['name']))
                ? stripslashes($_POST['name']) : '';
if (!$_POST['name'])
{
    echo 'Invalid use of file';
}
elseif (!preg_match("/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])+$/i",
        $_POST['name']))
{
    echo 'Usernames can only consist of Numbers, Letters, underscores and spaces.';
}
elseif (((strlen($_POST['name']) > 32) OR (strlen($_POST['name']) < 3)))
{
    echo 'Usernames can only be a max of 32 characters or a min of 3 characters.';
}
else
{
    $e_name_check = '%' . $db->escape($_POST['name']) . '%';
    $q =
            $db->query(
                    "SELECT `userid`, `username`, `level`, `money`, `crystals`
                     FROM `users`
                     WHERE `username` LIKE ('{$e_name_check}')");
    echo $db->num_rows($q)
            . ' players found. <br />
	<table width="70%" cellpadding="1" cellspacing="1" class="table">
		<tr style="background-color:gray;">
			<th>User</th>
			<th>Level</th>
			<th>Money</th>
			<th>Crystals</th>
		</tr>
   	';
    while ($r = $db->fetch_row($q))
    {
        echo '
		<tr>
			<td><a href="viewuser.php?u=' . $r['userid'] . '">'
                . $r['username'] . '</a></td>
			<td>' . $r['level'] . '</td>
			<td>' . money_formatter((int)$r['money']) . '</td>
			<td>' . number_format((int)$r['crystals']) . '</td>
		</tr>
   		';
    }
    $db->free_result($q);
    echo '</table>';
}
echo '<br />&gt; <a href="search.php">Go Back</a>';
$h->endpage();
