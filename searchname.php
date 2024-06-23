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
