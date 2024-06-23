<?php
declare(strict_types=1);
/**
 * MCCodes v2 by Dabomstew & ColdBlooded
 *
 * Repository: https://github.com/davemacaulay/mccodesv2
 * License: MIT License
 */

global $ir, $h;
require_once('globals.php');
if ($ir['mailban'])
{
    die(
            "<font color=red><h3>! ERROR</h3>
You have been mail banned for {$ir['mailban']} days.<br />
<br />
<b>Reason: {$ir['mb_reason']}</font></b>");
}
$_GET['ID'] =
        (isset($_GET['ID']) && is_numeric($_GET['ID']))
                ? abs(intval($_GET['ID'])) : '';
echo "<table width=85% class='table' cellspacing='1'>
		<tr>
			<td><a href='mailbox.php?action=inbox'>Inbox</a></td>
			<td><a href='mailbox.php?action=outbox'>Sent Messages</a></td>
			<td><a href='mailbox.php?action=compose'>Compose Message</a></td>
			<td><a href='mailbox.php?action=delall'>Delete All Messages</a></td>
			<td><a href='mailbox.php?action=archive'>Archive Messages</a></td>
			<td><a href='contactlist.php'>My Contacts</a></td>
		</tr>
	  </table><br />";
if (!isset($_GET['action']))
{
    $_GET['action'] = 'inbox';
}
switch ($_GET['action'])
{
case 'outbox':
    mail_outbox();
    break;
case 'compose':
    mail_compose();
    break;
case 'delete':
    mail_delete();
    break;
case 'send':
    mail_send();
    break;
case 'delall':
    mail_delall();
    break;
case 'delall2':
    mail_delall2();
    break;
case 'archive':
    mail_archive();
    break;
default:
    mail_inbox();
    break;
}

/**
 * @return void
 */
function mail_inbox(): void
{
    global $db, $ir, $userid;
    print
            <<<OUT
Only the last 25 messages sent to you are visible.<br />
<table width="75%" class="table" cellspacing="1">
	<tr>
		<td class="h" width="30%">From</td>
		<td class="h" width="70%">Subject/Message</td>
	</tr>
OUT;
    $q =
            $db->query(
                    "SELECT `m`.*, `userid`, `username`
                     FROM `mail` AS `m`
                     LEFT JOIN `users` AS `u`
                     ON `m`.`mail_from` = `u`.`userid`
                     WHERE `m`.`mail_to` = $userid
                     ORDER BY `mail_time` DESC
                     LIMIT 25");
    while ($r = $db->fetch_row($q))
    {
        $sent = date('F j, Y, g:i:s a', (int)$r['mail_time']);
        echo '<tr>
        		<td>';
        if ($r['userid'])
        {
            echo "<a href='viewuser.php?u={$r['userid']}'>{$r['username']}</a> [{$r['userid']}]";
        }
        else
        {
            echo 'SYSTEM';
        }
        $fm = urlencode($r['mail_text']);
        print
                <<<EOF
				</td>
				<td>{$r['mail_subject']}</td>
			</tr>
			<tr>
				<td>
					Sent at: {$sent}<br />
					<a href='mailbox.php?action=compose&ID={$r['userid']}'>Reply</a>
					<br />
					<a href='mailbox.php?action=delete&ID={$r['mail_id']}'>Delete</a>
					<br />
					<a href='preport.php?ID={$r['userid']}&amp;report=Fradulent mail: {$fm}'>Report</a>
				</td>
				<td>{$r['mail_text']}</td>
			</tr>
EOF;
    }
    $db->free_result($q);
    if ($ir['new_mail'] > 0)
    {
        $db->query(
                "UPDATE `mail`
         		 SET `mail_read` = 1
         		 WHERE `mail_to` = $userid");
        $db->query(
                "UPDATE `users`
         		 SET `new_mail` = 0
         		 WHERE `userid` = $userid");
    }
    echo '</table>';
}

/**
 * @return void
 */
function mail_outbox(): void
{
    global $db, $userid;
    echo "Only the last 25 messages you have sent are visible.<br />
	<table width='75%' cellspacing=1 class='table'>
		<tr>
			<th>To</th>
			<th>Subject/Message</th>
		</tr>";
    $q =
            $db->query(
                    "SELECT `m`.*, `userid`, `username`
                     FROM `mail` AS `m`
                     LEFT JOIN `users` AS `u`
                     ON `m`.`mail_to` = `u`.`userid`
                     WHERE `m`.`mail_from` = $userid
                     ORDER BY `mail_time` DESC
                     LIMIT 25");
    while ($r = $db->fetch_row($q))
    {
        $sent = date('F j, Y, g:i:s a', (int)$r['mail_time']);
        echo "<tr>
        		<td>
        			<a href='viewuser.php?u={$r['userid']}'>{$r['username']}</a>
        			[{$r['userid']}]
        		</td>
        		<td>{$r['mail_subject']}</td>
        	  </tr>
        	  <tr>
        	  	<td>Sent at: $sent</td>
        	  	<td>{$r['mail_text']}</td>
        	  </tr>";
    }
    $db->free_result($q);
}

/**
 * @return void
 */
function mail_compose(): void
{
    global $db, $ir, $userid;
    echo "
	<form action='mailbox.php?action=send' method='post'>
	<table width=75% cellspacing=1 class='table'>
		<tr>
			<td>Contact to send to:</td>
			<td>";
    $q =
            $db->query(
                    "SELECT `c`.*, `username`
                     FROM `contactlist` AS `c`
                     INNER JOIN `users` AS `u`
                     ON `c`.`cl_ADDED` = `u`.`userid`
                     WHERE `c`.`cl_ADDER` = {$userid}
                     ORDER BY u.`username` ASC");
    if ($db->num_rows($q) == 0)
    {
        echo 'You have no contacts!';
    }
    else
    {
        echo "<select name='user1' type='dropdown'><option value=''>&lt;select a contact...&gt;</option>";
        while ($r = $db->fetch_row($q))
        {
            $esc_part = addslashes($r['username']);
            echo "<option value='{$esc_part}'>{$r['username']}</option>";
        }
        echo '</select>';
    }
    $db->free_result($q);
    $_GET['ID'] =
            (isset($_GET['ID']) && is_numeric($_GET['ID']))
                    ? abs(intval($_GET['ID'])) : '';
    $user_exists = false;
    if ($_GET['ID'])
    {
        $un_query =
                $db->query(
                        "SELECT `username`
        				 FROM `users`
        				 WHERE `userid` = {$_GET['ID']}");
        if ($db->num_rows($un_query) > 0)
        {
            $user_exists = true;
            $user = $db->fetch_single($un_query);
        }
        else
        {
            $user = '';
        }
        $db->free_result($un_query);
    }
    else
    {
        $user = '';
    }
    $esc_user = addslashes($user);
    echo "		</td>
    		</tr>
    		<tr>
				<td>
					<b>OR</b>
					Enter a username to send to:
    			</td>
				<td><input type='text' name='user2' value='{$esc_user}' /></td>
			</tr>
			<tr>
				<td>Subject:</td>
				<td><input type='text' name='subject' /></td>
			</tr>
			<tr>
				<td>Message:</td>
				<td><textarea rows=5 cols=40 name='message'></textarea></td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' value='Send' />
				</td>
			</tr>
		</table></form>";
    if ($user_exists)
    {
        echo "<br />
        <table width='75%' border='2' class='table'>
        	<tr>
        		<td colspan='2'><b>Your last 5 mails to/from this person:</b></td>
        	</tr>";
        $q =
                $db->query(
                        "SELECT `mail_time`, `mail_text`, `mail_from`
                         FROM `mail`
                         WHERE (`mail_from` = $userid
                         	AND `mail_to` = {$_GET['ID']})
                         OR (`mail_to` = $userid
                         	AND `mail_from` = {$_GET['ID']})
                         ORDER BY `mail_time` DESC
                         LIMIT 5");
        while ($r = $db->fetch_row($q))
        {
            $sender =
                    ($_GET['ID'] == $r['mail_from']) ? $user : $ir['username'];
            $sent = date('F j, Y, g:i:s a', (int)$r['mail_time']);
            echo "<tr>
            		<td>$sent</td>
            		<td><b>{$sender} wrote:</b> {$r['mail_text']}</td>
            	  </tr>";
        }
        $db->free_result($q);
        echo '</table>';
    }
}

/**
 * @return void
 */
function mail_send(): void
{
    global $db, $userid, $h;
    $subj =
            $db->escape(
                    str_replace("\n", '<br />',
                            strip_tags(stripslashes($_POST['subject']))));
    $msg =
            $db->escape(
                    str_replace("\n", '<br />',
                            strip_tags(stripslashes($_POST['message']))));
    if (empty($subj) || empty($msg))
    {
        echo '
		You must enter a message and subject.<br />
		&gt; <a href="mailbox.php">Go Back</a>
   		';
        $h->endpage();
        exit;
    }
    elseif ((strlen($msg) > 250) || (strlen($subj) > 50))
    {
        echo '
		Messages/Subjects are limited to 250/50 characters per time.<br />
		&gt; <a href="mailbox.php">Go Back</a>
   		';
        $h->endpage();
        exit;
    }
    $_POST['user1'] =
            (isset($_POST['user1'])
                    && preg_match(
                            "/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])+$/i",
                            $_POST['user1'])
                    && ((strlen($_POST['user1']) < 32)
                            && (strlen($_POST['user1']) >= 3)))
                    ? $_POST['user1'] : '';
    $_POST['user2'] =
            (isset($_POST['user2'])
                    && preg_match(
                            "/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])+$/i",
                            $_POST['user2'])
                    && ((strlen($_POST['user2']) < 32)
                            && (strlen($_POST['user2']) >= 3)))
                    ? $_POST['user2'] : '';
    if ($_POST['user1'] && $_POST['user2'])
    {
        echo "
		Please do not select a contact AND enter a username, only do one.
		<br />
		<a href='mailbox.php'>&gt; Back</a>
   ";
        $h->endpage();
        exit;
    }
    if (empty($_POST['user1']) && empty($_POST['user2']))
    {
        echo "You must select a contact or enter a username.<br />
		<a href='mailbox.php'>&gt; Back</a>";
        $h->endpage();
        exit;
    }
    $sendto = $_POST['user1'] ?: $_POST['user2'];
    $q =
            $db->query(
                    "SELECT `userid`
                     FROM `users`
                     WHERE `username` = '{$sendto}'");
    if ($db->num_rows($q) == 0)
    {
        $db->free_result($q);
        echo "You cannot send mail to nonexistant users.<br />
<a href='mailbox.php'>&gt; Back</a>";
        $h->endpage();
        exit;
    }
    $to = $db->fetch_single($q);
    $db->free_result($q);
    $db->query(
            "INSERT INTO `mail`
             VALUES (NULL, 0, $userid, $to, " . time() . ", '$subj', '$msg')");
    $db->query(
            "UPDATE `users`
             SET `new_mail` = `new_mail` + 1
             WHERE `userid` = {$to}");
    echo "Message sent.<br />
	<a href='mailbox.php'>&gt; Back</a>";
}

/**
 * @return void
 */
function mail_delete(): void
{
    global $db, $userid, $h;
    $_GET['ID'] =
            (isset($_GET['ID']) && is_numeric($_GET['ID']))
                    ? abs(intval($_GET['ID'])) : '';
    if (empty($_GET['ID']))
    {
        echo 'Invalid ID.<br />&gt; <a href="mailbox.php">Go Back</a>';
        $h->endpage();
        exit;
    }
    $q =
            $db->query(
                    "SELECT COUNT(`mail_id`)
                     FROM `mail`
                     WHERE `mail_id` = {$_GET['ID']}
                     AND `mail_to` = {$userid}");
    if ($db->fetch_single($q) == 0)
    {
        $db->free_result($q);
        echo 'Invalid ID.
        <br />&gt; <a href="mailbox.php">Go Back</a>';
        $h->endpage();
        exit;
    }
    $db->free_result($q);
    $db->query(
            "DELETE FROM `mail`
             WHERE `mail_id` = {$_GET['ID']}
             AND `mail_to` = $userid");
    echo "Message deleted.<br />
	<a href='mailbox.php'>&gt; Back</a>";
}

/**
 * @return void
 */
function mail_delall(): void
{
    $delall_verf = request_csrf_code('mailbox_delall');
    echo "
	This will delete all the messages in your inbox.
	<br />
	There is <b>NO</b> undo, so be sure.
	<br />
	&gt; <a href='mailbox.php?action=delall2&amp;verf={$delall_verf}'>Yes, delete all messages</a>
	<br />
	&gt; <a href='mailbox.php'>No, go back</a>
   	";
}

/**
 * @return void
 */
function mail_delall2(): void
{
    global $db, $userid, $h;
    if (!isset($_GET['verf'])
            || !verify_csrf_code('mailbox_delall', stripslashes($_GET['verf'])))
    {
        echo '<h3>Error</h3><hr />
    	This action has been blocked for your security.<br />
    	You should submit this action fast,
    	to ensure that it is really you doing it.<br />
    	&gt; <a href="mailbox.php?action=delall">Try Again</a>';
        $h->endpage();
        exit;
    }
    $m_c =
            $db->query(
                    "SELECT COUNT(`mail_id`)
                     FROM `mail`
                     WHERE `mail_to` = {$userid}");
    if ($db->fetch_single($m_c) == 0)
    {
        echo 'You have no mails to delete.
        <br />&gt; <a href="mailbox.php">Go Back</a>';
    }
    else
    {
        $db->query(
                "DELETE FROM `mail`
                 WHERE `mail_to` = $userid");
        echo '
		All ' . $db->affected_rows()
                . " mails in your inbox were deleted.<br />
		&gt; <a href='mailbox.php'>Go Back</a>
   		";
    }
    $db->free_result($m_c);
}

/**
 * @return void
 */
function mail_archive(): void
{
    echo "This tool will download an archive of all your messages.<br />
	&gt; <a href='dlarchive.php?a=inbox'>Download Inbox</a><br />
	&gt; <a href='dlarchive.php?a=outbox'>Download Outbox</a>";
}
$h->endpage();
