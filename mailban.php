<?php
declare(strict_types=1);
/**
 * MCCodes v2 by Dabomstew & ColdBlooded
 *
 * Repository: https://github.com/davemacaulay/mccodesv2
 * License: MIT License
 */

global $db, $ir, $h;
require_once('globals.php');
if (!check_access('manage_punishments'))
{
    echo 'You cannot access this area.
    <br />&gt; <a href="index.php">Go Home</a>';
    $h->endpage();
    exit;
}
$_POST['user'] =
        (isset($_POST['user']) && is_numeric($_POST['user']))
                ? abs(intval($_POST['user'])) : '';
$_POST['reason'] =
        (isset($_POST['reason'])
                && ((strlen($_POST['reason']) > 3)
                        && (strlen($_POST['reason']) < 50)))
                ? strip_tags(stripslashes($_POST['reason'])) : '';
$_POST['days'] =
        (isset($_POST['days']) && is_numeric($_POST['days']))
                ? abs(intval($_POST['days'])) : '';
if (!empty($_POST['user']) && !empty($_POST['reason'])
        && !empty($_POST['days']))
{
    if (!isset($_POST['verf'])
            || !verify_csrf_code('mailban', stripslashes($_POST['verf'])))
    {
        echo '<h3>Error</h3><hr />
   			This operation has been blocked for your security.<br />
    		Please try again.<br />
    		&gt; <a href="mailban.php?userid=' . $_POST['user']
                . '">Try Again</a>';
        $h->endpage();
        exit;
    }
    if (check_access('administrator', $_POST['user']))
    {
        echo 'You cannot mailban admins, please destaff them first.
        <br />&gt; <a href="mailban.php">Go Back</a>';
        $h->endpage();
        exit;
    }
    $e_reason = $db->escape($_POST['reason']);
    $re =
            $db->query(
                    "UPDATE `users`
                     SET `mailban` = {$_POST['days']},
                     `mb_reason` = '{$e_reason}'
                     WHERE `userid` = {$_POST['user']}");
    event_add($_POST['user'],
        "You were banned from mail for {$_POST['days']} day(s) for the following reason: {$_POST['reason']}");
    echo 'User was mail banned.<br />
    &gt; <a href="index.php">Go Home</a>';
}
else
{
    $mb_csrf = request_csrf_code('mailban');
    $_GET['userid'] =
            (isset($_GET['userid']) && is_numeric($_GET['userid']))
                    ? abs(intval($_GET['userid'])) : -1;
    echo "
	<h3>Mail Banning User</h3>
	The user will not be able to use the mail system for a set period of days.
	<br />
	<form action='mailban.php' method='post'>
		User: " . user_dropdown('user', $_GET['userid'])
            . "
		<br />
		Days: <input type='text' name='days' />
		<br />
		Reason: <input type='text' name='reason' />
		<br />
		<input type='hidden' name='verf' value='{$mb_csrf}' />
		<input type='submit' value='MailBan User' />
	</form>
   	";
}
$h->endpage();
