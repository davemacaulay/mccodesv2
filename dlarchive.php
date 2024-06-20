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
 * File: dlarchive.php
 * Signature: 32076c140c8f37511d0589d0b5c3f096
 * Date: Fri, 20 Apr 12 08:50:30 +0000
 */

$nohdr = true;
require_once('globals.php');
if (!isset($_GET['a']))
{
    $_GET['a'] = '';
}
if ($_GET['a'] == 'inbox')
{
    header('Content-type: text/html');
    header(
            'Content-Disposition: attachment; ' . 'filename="inbox_archive_'
                    . $userid . '_' . time() . '.htm"');
    echo "<table width='75%' border='2'>
    		<tr style='background:gray;'>
    			<th>From</th>
    			<th>Subject/Message</th>
    		</tr>";
    $q =
            $db->query(
                    "SELECT `mail_time`, `mail_subject`, `mail_text`,
                    `userid`, `username`
                    FROM `mail` AS `m`
                    LEFT JOIN `users` AS `u` ON `m`.`mail_from` = `u`.`userid`
                    WHERE `m`.`mail_to` = $userid
                    ORDER BY `mail_time` DESC");
    while ($r = $db->fetch_row($q))
    {
        $sent = date('F j, Y, g:i:s a', $r['mail_time']);
        echo '<tr>
        		<td>';
        if ($r['userid'])
        {
            echo "{$r['username']} [{$r['userid']}]";
        }
        else
        {
            echo 'SYSTEM';
        }
        echo "</td>
        	<td>{$r['mail_subject']}</td>
        </tr>
        <tr>
        	<td>Sent at: $sent</td>
        	<td>{$r['mail_text']}</td>
        </tr>";
    }
    $db->free_result($q);
    echo '</table>';
}
elseif ($_GET['a'] == 'outbox')
{
    header('Content-type: text/html');
    header(
            'Content-Disposition: attachment; ' . 'filename="outbox_archive_'
                    . $userid . '_' . time() . '.htm"');
    echo "<table width='75%' border='2'>
    		<tr style='background:gray;'>
    			<th>To</th>
    			<th>Subject/Message</th>
    		</tr>";
    $q =
            $db->query(
                    "SELECT `mail_time`, `mail_subject`, `mail_text`,
                    `userid`, `username`
                    FROM `mail` AS `m`
                    LEFT JOIN `users` AS `u` ON `m`.`mail_to` = `u`.`userid`
                    WHERE `m`.`mail_from` = $userid
                    ORDER BY `mail_time` DESC");
    while ($r = $db->fetch_row($q))
    {
        $sent = date('F j, Y, g:i:s a', $r['mail_time']);
        echo "<tr>
        	  	<td>{$r['username']} [{$r['userid']}]</td>
        	  	<td>{$r['mail_subject']}</td>
        	  </tr>
        	  <tr>
        	  	<td>Sent at: $sent</td>
        	  	<td>{$r['mail_text']}</td>
        	  </tr>";
    }
    $db->free_result($q);
    echo '</table>';
}
else
{
    header('HTTP/1.1 400 Bad Request');
    exit;
}
