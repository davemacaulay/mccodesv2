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
 * File: mailban.php
 * Signature: 858dfef7b695c68b698509c2f09b0f5e
 * Date: Fri, 20 Apr 12 08:50:30 +0000
 */

require_once('globals.php');
if (!in_array($ir['user_level'], array(2, 3, 5)))
{
    echo 'You cannot access this area.
    <br />&gt; <a href="index.php">Go Home</a>';
    die($h->endpage());
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
        die($h->endpage());
    }
    $q =
            $db->query(
                    'SELECT `user_level`
                     FROM `users`
                     WHERE `userid` = ' . $_POST['user']);
    if ($db->num_rows($q) == 0)
    {
        $db->free_result($q);
        echo 'Invalid user.<br />
        &gt; <a href="mailban.php">Go Back</a>';
        die($h->endpage());
    }
    $f_q = $db->fetch_row($q);
    $db->free_result($q);
    if ($f_q['user_level'] == 2)
    {
        echo 'You cannot mailban admins, please destaff them first.
        <br />&gt; <a href="mailban.php">Go Back</a>';
        die($h->endpage());
    }
    $e_reason = $db->escape($_POST['reason']);
    $re =
            $db->query(
                    "UPDATE `users`
                     SET `mailban` = {$_POST['days']},
                     `mb_reason` = '{$e_reason}'
                     WHERE `userid` = {$_POST['user']}");
    event_add($_POST['user'],
            "You were banned from mail for {$_POST['days']} day(s) for the following reason: {$_POST['reason']}",
            $c);
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
		User: " . user_dropdown(NULL, 'user', $_GET['userid'])
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
