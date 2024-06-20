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
 * File: announcements.php
 * Signature: 3fef08ec3e124e63c1c6655b8aa50d18
 * Date: Fri, 20 Apr 12 08:50:30 +0000
 */

require_once('globals.php');
$ac = $ir['new_announcements'];
$q =
        $db->query(
                'SELECT `a_text`, `a_time` FROM `announcements` '
                        . 'ORDER BY `a_time` DESC');
echo '
<table width="80%" cellspacing="1" cellpadding="1" class="table">
		<tr>
	<th width="30%">Time</th>
	<th width="70%">Announcement</th>
		</tr>
   ';
while ($r = $db->fetch_row($q))
{
    if ($ac > 0)
    {
        $ac--;
        $new = '<br /><b>New!</b>';
    }
    else
    {
        $new = '';
    }
    $r['a_text'] = nl2br($r['a_text']);
    echo '
		<tr>
	<td valign=top>' . date('F j Y, g:i:s a', $r['a_time']) . $new
            . '</td>
	<td valign=top>' . $r['a_text'] . '</td>
		</tr>
   ';
}
$db->free_result($q);
echo '</table>';
if ($ir['new_announcements'] > 0)
{
    $db->query(
            'UPDATE `users` ' . 'SET `new_announcements` = 0 '
                    . "WHERE `userid` = '{$userid}'");
}
$h->endpage();
