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
$_POST['location'] =
        (isset($_POST['location']) && is_numeric($_POST['location']))
                ? abs(intval($_POST['location'])) : '';
if (!$_POST['location'])
{
    echo 'Invalid use of file';
}
else
{
    $check_it =
            $db->query(
                    'SELECT `cityid`
                     FROM `cities`
                     WHERE `cityid` = ' . $_POST['location']);
    if ($db->num_rows($check_it) == 0)
    {
        $db->free_result($check_it);
        echo 'This location doesn\'t exist.<br />&gt; <a href="search.php">Go Back</a>';
        $h->endpage();
        exit;
    }
    $db->free_result($check_it);
    $q =
            $db->query(
                    "SELECT `userid`, `level`, `money`, `crystals`, `username`
                     FROM `users`
                     WHERE `location` = '{$_POST['location']}'
                     ORDER BY `username`
                     LIMIT 100");
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
