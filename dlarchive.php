<?php
declare(strict_types=1);
/**
 * MCCodes v2 by Dabomstew & ColdBlooded
 *
 * Repository: https://github.com/davemacaulay/mccodesv2
 * License: MIT License
 */

$nohdr = true;
global $db, $userid;
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
        $sent = date('F j, Y, g:i:s a', (int)$r['mail_time']);
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
        $sent = date('F j, Y, g:i:s a', (int)$r['mail_time']);
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
