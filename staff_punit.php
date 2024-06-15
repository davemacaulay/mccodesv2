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
 * File: staff_punit.php
 * Signature: f0ed16545d89e246f9cdb0eb9d56dc6a
 * Date: Fri, 20 Apr 12 08:50:30 +0000
 */

require_once('sglobals.php');
if (!in_array($ir['user_level'], array(2, 3, 5)))
{
    echo 'You cannot access this area.<br />&gt; <a href="staff.php">Go Home</a>';
    die($h->endpage());
}
//This contains punishment stuffs
if (!isset($_GET['action']))
{
    $_GET['action'] = '';
}
switch ($_GET['action'])
{
case 'fedform':
    fed_user_form();
    break;
case 'fedsub':
    fed_user_submit();
    break;
case 'fedeform':
    fed_edit_form();
    break;
case 'fedesub':
    fed_edit_submit();
    break;
case 'mailform':
    mail_user_form();
    break;
case 'mailsub':
    mail_user_submit();
    break;
case 'forumform':
    forum_user_form();
    break;
case 'forumsub':
    forum_user_submit();
    break;
case 'unfedform':
    unfed_user_form();
    break;
case 'unfedsub':
    unfed_user_submit();
    break;
case 'unmailform':
    unmail_user_form();
    break;
case 'unmailsub':
    unmail_user_submit();
    break;
case 'unforumform':
    unforum_user_form();
    break;
case 'unforumsub':
    unforum_user_submit();
    break;
case 'ipform':
    ip_search_form();
    break;
case 'ipsub':
    ip_search_submit();
    break;
case 'massjailip':
    mass_jail();
    break;
default:
    echo 'Error: This script requires an action.';
    break;
}

function fed_user_form()
{
    global $c;
    $_GET['XID'] =
            (isset($_GET['XID']) && is_numeric($_GET['XID']))
                    ? abs(intval($_GET['XID'])) : 0;
    $csrf = request_csrf_html('staff_feduser');
    echo "
    <h3>Jailing User</h3>
    The user will be put in fed jail and will be unable to do anything in the game.
    <br />
    <form action='staff_punit.php?action=fedsub' method='post'>
    	User: " . user_dropdown(NULL, 'user', $_GET['XID'])
            . "
    	<br />
    	Days: <input type='text' name='days' />
    	<br />
    	Reason: <input type='text' name='reason' />
    	<br />
    	{$csrf}
    	<input type='submit' value='Jail User' />
    </form>
       ";
}

function fed_user_submit()
{
    global $db, $ir, $c, $h, $userid;
    staff_csrf_stdverify('staff_feduser', 'staff_punit.php?action=fedform');
    $_POST['user'] =
            (isset($_POST['user']) && is_numeric($_POST['user']))
                    ? abs(intval($_POST['user'])) : '';
    $_POST['reason'] =
            (isset($_POST['reason']))
                    ? $db->escape(strip_tags(stripslashes($_POST['reason'])))
                    : '';
    $_POST['days'] =
            (isset($_POST['days']) && is_numeric($_POST['days']))
                    ? abs(intval($_POST['days'])) : '';
    if (empty($_POST['user']) || empty($_POST['reason'])
            || empty($_POST['days']))
    {
        echo 'You need to fill in all the fields.<br />
        &gt; <a href="staff_punit.php?action=fedform">Go Back</a>';
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
        &gt; <a href="staff_punit.php?action=fedform">Go Back</a>';
        die($h->endpage());
    }
    $f_userlevel = $db->fetch_single($q);
    $db->free_result($q);
    if ($f_userlevel == 2)
    {
        echo 'You cannot fed admins, please destaff them first.<br />
        &gt; <a href="staff_punit.php?action=fedform">Go Back</a>';
        die($h->endpage());
    }
    $re =
            $db->query(
                    "UPDATE `users`
                     SET `fedjail` = 1
                     WHERE `userid` = {$_POST['user']}");
    if ($db->affected_rows() > 0)
    {
        $db->query(
                "INSERT INTO `fedjail`
                 VALUES(NULL, {$_POST['user']}, {$_POST['days']}, $userid, '"
                        . $_POST['reason'] . "')");
    }
    $db->query(
            "INSERT INTO `jaillogs`
             VALUES(NULL, $userid, {$_POST['user']}, {$_POST['days']},
             '{$_POST['reason']}', " . time() . ")");
    stafflog_add(
            'Fedded ID ' . $_POST['user'] . ' for ' . $_POST['days']
                    . ', reason: ' . $_POST['reason']);
    echo 'User jailed.<br />
    &gt; <a href="staff.php">Go Home</a>';
    die($h->endpage());
}

function fed_edit_form()
{
    global $db, $ir, $c, $h, $userid;
    $csrf = request_csrf_html('staff_fededit');
    echo "
    <h3>Editing Fedjail Reason</h3>
    You are editing a player's sentence in fed jail.
    <br />
    <form action='staff_punit.php?action=fedesub' method='post'>
    	User: " . fed_user_dropdown(NULL, 'user')
            . "
    	<br />
    	Days: <input type='text' name='days' />
    	<br />
    	Reason: <input type='text' name='reason' />
    	<br />
    	{$csrf}
    	<input type='submit' value='Jail User' />
    </form>
       ";
}

function fed_edit_submit()
{
    global $db, $ir, $c, $h, $userid;
    staff_csrf_stdverify('staff_fededit', 'staff_punit.php?action=fedeform');
    $_POST['user'] =
            (isset($_POST['user']) && is_numeric($_POST['user']))
                    ? abs(intval($_POST['user'])) : '';
    $_POST['reason'] =
            (isset($_POST['reason']))
                    ? $db->escape(strip_tags(stripslashes($_POST['reason'])))
                    : '';
    $_POST['days'] =
            (isset($_POST['days']) && is_numeric($_POST['days']))
                    ? abs(intval($_POST['days'])) : '';
    if (empty($_POST['user']) || empty($_POST['reason'])
            || empty($_POST['days']))
    {
        echo 'You need to fill in all the fields.<br />
        &gt; <a href="staff_punit.php?action=fedeform">Go Back</a>';
        die($h->endpage());
    }
    $q =
            $db->query(
                    'SELECT `user_level`
                     FROM `users`
                     WHERE `userid` = ' . $_POST['user']
                            . '
                     AND `fedjail` > 0');
    if ($db->num_rows($q) == 0)
    {
        $db->free_result($q);
        echo 'Invalid user.<br />
        &gt; <a href="staff_punit.php?action=fedeform">Go Back</a>';
        die($h->endpage());
    }
    $f_userlevel = $db->fetch_single($q);
    $db->free_result($q);
    if ($f_userlevel == 2)
    {
        echo 'You cannot fed admins please destaff them first.<br />
        &gt; <a href="staff_punit.php?action=fedeform">Go Back</a>';
        die($h->endpage());
    }
    $db->query(
            "DELETE FROM `fedjail`
     		 WHERE `fed_userid` = {$_POST['user']}");
    $db->query(
            "INSERT INTO `fedjail`
             VALUES(NULL, {$_POST['user']}, {$_POST['days']}, $userid,'"
                    . $_POST['reason'] . "')");
    $db->query(
            "INSERT INTO `jaillogs`
             VALUES(NULL, $userid, {$_POST['user']}, {$_POST['days']},
             '{$_POST['reason']}', " . time() . ")");
    stafflog_add('Edited user ID ' . $_POST['user'] . '\'s fedjail sentence');
    echo 'User\'s sentence edited.<br />
    &gt; <a href="staff.php">Go Home</a>';
    die($h->endpage());
}

function mail_user_form()
{
    global $db, $ir, $c, $h, $userid;
    $_GET['XID'] =
            (isset($_GET['XID']) && is_numeric($_GET['XID']))
                    ? abs(intval($_GET['XID'])) : 0;
    $csrf = request_csrf_html('staff_mailbanuser');
    echo "
    <h3>Mail Banning User</h3>
    The user will be banned from the mail system.
    <br />
    <form action='staff_punit.php?action=mailsub' method='post'>
    	User: " . user_dropdown(NULL, 'user', $_GET['XID'])
            . "
    	<br />
    	Days: <input type='text' name='days' />
    	<br />
    	Reason: <input type='text' name='reason' />
    	<br />
    	{$csrf}
    	<input type='submit' value='Mailban User' />
    </form>
       ";
}

function mail_user_submit()
{
    global $db, $ir, $c, $h, $userid;
    staff_csrf_stdverify('staff_mailbanuser',
            'staff_punit.php?action=mailform');
    $_POST['user'] =
            (isset($_POST['user']) && is_numeric($_POST['user']))
                    ? abs(intval($_POST['user'])) : '';
    $_POST['reason'] =
            (isset($_POST['reason']))
                    ? $db->escape(strip_tags(stripslashes($_POST['reason'])))
                    : '';
    $_POST['days'] =
            (isset($_POST['days']) && is_numeric($_POST['days']))
                    ? abs(intval($_POST['days'])) : '';
    if (empty($_POST['user']) || empty($_POST['reason'])
            || empty($_POST['days']))
    {
        echo 'You need to fill in all the fields.<br />
        &gt; <a href="staff_punit.php?action=mailform">Go Back</a>';
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
        &gt; <a href="staff_punit.php?action=mailform">Go Back</a>';
        die($h->endpage());
    }
    $f_userlevel = $db->fetch_single($q);
    $db->free_result($q);
    if ($f_userlevel == 2)
    {
        echo 'You cannot mail ban admins please destaff them first.<br />
        &gt; <a href="staff_punit.php?action=mailform">Go Back</a>';
        die($h->endpage());
    }
    $re =
            $db->query(
                    "UPDATE `users`
                     SET `mailban` = {$_POST['days']},
                     `mb_reason` = '{$_POST['reason']}'
                     WHERE `userid` = {$_POST['user']}");
    event_add($_POST['user'],
            "You were banned from mail for {$_POST['days']} day(s) for the following reason: {$_POST['reason']}",
            $c);
    stafflog_add(
            "Mail banned User ID {$_POST['user']} for {$_POST['days']} days");
    echo 'User mail banned.<br />
    &gt; <a href="staff.php">Go Home</a>';
    die($h->endpage());
}

function forum_user_form()
{
    global $db, $ir, $c, $h, $userid;
    $_GET['XID'] =
            (isset($_GET['XID']) && is_numeric($_GET['XID']))
                    ? abs(intval($_GET['XID'])) : 0;
    $csrf = request_csrf_html('staff_forumbanuser');
    echo "
    <h3>Forum Banning User</h3>
    The user will be banned from the forums.
    <br />
    <form action='staff_punit.php?action=forumsub' method='post'>
    	User: " . user_dropdown(NULL, 'user', $_GET['XID'])
            . "
    	<br />
    	Days: <input type='text' name='days' />
    	<br />
    	Reason: <input type='text' name='reason' />
    	<br />
    	{$csrf}
    	<input type='submit' value='Forumban User' />
    </form>
       ";
}

function forum_user_submit()
{
    global $db, $ir, $c, $h, $userid;
    staff_csrf_stdverify('staff_forumbanuser',
            'staff_punit.php?action=forumform');
    $_POST['user'] =
            (isset($_POST['user']) && is_numeric($_POST['user']))
                    ? abs(intval($_POST['user'])) : '';
    $_POST['reason'] =
            (isset($_POST['reason']))
                    ? $db->escape(strip_tags(stripslashes($_POST['reason'])))
                    : '';
    $_POST['days'] =
            (isset($_POST['days']) && is_numeric($_POST['days']))
                    ? abs(intval($_POST['days'])) : '';
    if (empty($_POST['user']) || empty($_POST['reason'])
            || empty($_POST['days']))
    {
        echo 'You need to fill in all the fields.<br />
        &gt; <a href="staff_punit.php?action=forumform">Go Back</a>';
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
        &gt; <a href="staff_punit.php?action=forumform">Go Back</a>';
        die($h->endpage());
    }
    $f_userlevel = $db->fetch_single($q);
    $db->free_result($q);
    if ($f_userlevel == 2)
    {
        echo 'You cannot forum ban admins please destaff them first.<br />
        &gt; <a href="staff_punit.php?action=forumform">Go Back</a>';
        die($h->endpage());
    }
    $re =
            $db->query(
                    "UPDATE `users`
                     SET `forumban` = {$_POST['days']},
                     `fb_reason` = '{$_POST['reason']}'
                     WHERE `userid` = {$_POST['user']}");
    event_add($_POST['user'],
            "You were banned from the forums for {$_POST['days']} day(s) for the following reason: {$_POST['reason']}",
            $c);
    stafflog_add(
            'Forum banned User ID ' . $_POST['user'] . ' for '
                    . $_POST['days'] . ' days');
    echo 'User forum banned.<br />
    &gt; <a href="staff.php">Go Home</a>';
    die($h->endpage());
}

function unfed_user_form()
{
    global $db, $ir, $c, $h, $userid;
    $csrf = request_csrf_html('staff_unfeduser');
    echo "
    <h3>Unjailing User</h3>
    The user will be taken out of fed jail.
    <br />
    <form action='staff_punit.php?action=unfedsub' method='post'>
    	User: " . fed_user_dropdown(NULL, 'user')
            . "
    	<br />
    	{$csrf}
    	<input type='submit' value='Unjail User' />
    </form>
       ";
}

function unfed_user_submit()
{
    global $db, $ir, $c, $h, $userid;
    staff_csrf_stdverify('staff_unfeduser', 'staff_punit.php?action=unfedform');
    $_POST['user'] =
            (isset($_POST['user']) && is_numeric($_POST['user']))
                    ? abs(intval($_POST['user'])) : '';
    if (empty($_POST['user']))
    {
        echo 'You need to fill in all the fields.<br />
        &gt; <a href="staff_punit.php?action=unfedform">Go Back</a>';
        die($h->endpage());
    }
    $q =
            $db->query(
                    'SELECT COUNT(`userid`)
                     FROM `users`
                     WHERE `userid` = ' . $_POST['user']);
    if ($db->fetch_single($q) == 0)
    {
        $db->free_result($q);
        echo 'Invalid user.<br />
        &gt; <a href="staff_punit.php?action=unfedform">Go Back</a>';
        die($h->endpage());
    }
    $db->free_result($q);
    $db->query(
            "UPDATE `users`
             SET `fedjail` = 0
             WHERE `userid` = {$_POST['user']}");
    $db->query(
            "DELETE FROM `fedjail`
     		 WHERE `fed_userid` = {$_POST['user']}");
    $db->query(
            "INSERT INTO `unjaillogs`
             VALUES(NULL, $userid, {$_POST['user']}, " . time() . ")");
    stafflog_add("Unfedded user ID {$_POST['user']}");
    echo 'User unjailed.<br />
    &gt; <a href="staff.php">Go Home</a>';
    die($h->endpage());
}

function unmail_user_form()
{
    global $db, $ir, $c, $h, $userid;
    $csrf = request_csrf_html('staff_unmailbanuser');
    echo "
    <h3>Un-mailbanning User</h3>
    The user will be taken out of mail ban.
    <br />
    <form action='staff_punit.php?action=unmailsub' method='post'>
    	User: " . mailb_user_dropdown(NULL, 'user')
            . "<br />
        {$csrf}
    	<input type='submit' value='Un-mailban User' />
    </form>
       ";
}

function unmail_user_submit()
{
    global $db, $ir, $c, $h, $userid;
    staff_csrf_stdverify('staff_unmailbanuser',
            'staff_punit.php?action=unmailform');
    $_POST['user'] =
            (isset($_POST['user']) && is_numeric($_POST['user']))
                    ? abs(intval($_POST['user'])) : '';
    if (empty($_POST['user']))
    {
        echo 'You need to fill in all the fields.<br />
        &gt; <a href="staff_punit.php?action=unmailform">Go Back</a>';
        die($h->endpage());
    }
    $q =
            $db->query(
                    'SELECT COUNT(`userid`)
                     FROM `users`
                     WHERE `userid` = ' . $_POST['user']);
    if ($db->fetch_single($q) == 0)
    {
        $db->free_result($q);
        echo 'Invalid user.<br />
        &gt; <a href="staff_punit.php?action=unmailform">Go Back</a>';
        die($h->endpage());
    }
    $db->free_result($q);
    $db->query(
            "UPDATE `users`
             SET `mailban` = 0
             WHERE `userid` = {$_POST['user']}");
    event_add($_POST['user'],
            "You were unbanned from mail. You can now use it again.", $c);
    stafflog_add('Un-mailbanned user ID ' . $_POST['user']);
    echo 'User un-mailbanned.<br />
    &gt; <a href="staff.php">Go Home</a>';
    die($h->endpage());
}

function unforum_user_form()
{
    global $db, $ir, $c, $h, $userid;
    $csrf = request_csrf_html('staff_unforumbanuser');
    echo "
    <h3>Un-forumbanning User</h3>
    The user will be taken out of forum ban.
    <br />
    <form action='staff_punit.php?action=unforumsub' method='post'>
    	User: " . forumb_user_dropdown(NULL, 'user')
            . "
    	<br />
        {$csrf}
    	<input type='submit' value='Un-forumban User' />
    </form>
       ";
}

function unforum_user_submit()
{
    global $db, $ir, $c, $h, $userid;
    staff_csrf_stdverify('staff_unforumbanuser',
            'staff_punit.php?action=unforumform');
    $_POST['user'] =
            (isset($_POST['user']) && is_numeric($_POST['user']))
                    ? abs(intval($_POST['user'])) : '';
    if (empty($_POST['user']))
    {
        echo 'You need to fill in all the fields.<br />
        &gt; <a href="staff_punit.php?action=unforumform">Go Back</a>';
        die($h->endpage());
    }
    $q =
            $db->query(
                    'SELECT COUNT(`userid`)
                     FROM `users`
                     WHERE `userid` = ' . $_POST['user']);
    if ($db->fetch_single($q) == 0)
    {
        $db->free_result($q);
        echo 'Invalid user.<br />
        &gt; <a href="staff_punit.php?action=unforumform">Go Back</a>';
        die($h->endpage());
    }
    $db->free_result($q);
    $db->query(
            "UPDATE `users`
             SET `forumban` = 0
             WHERE `userid` = {$_POST['user']}");
    event_add($_POST['user'],
            "You were unbanned from the forums. You can now use them again.",
            $c);
    stafflog_add("Un-forumbanned user ID {$_POST['user']}");
    echo 'User un-forumbanned.<br />
    &gt; <a href="staff.php">Go Home</a>';
    die($h->endpage());
}

function ip_search_form()
{
    global $db, $ir, $c, $h, $userid;
    $csrf = request_csrf_html('staff_ipsearch');
    echo "
    <h3>IP Search</h3>
    <form action='staff_punit.php?action=ipsub' method='post'>
    	IP: <input type='text' name='ip' value='...' />
    	<br />
    	{$csrf}
    	<input type='submit' value='Search' />
    </form>
       ";
}

function ip_search_submit()
{
    global $db, $ir, $c, $h, $userid, $domain;
    staff_csrf_stdverify('staff_ipsearch', 'staff_punit.php?action=ipform');
    $_POST['ip'] =
            (filter_input(INPUT_POST, 'ip', FILTER_VALIDATE_IP)) ? $_POST['ip']
                    : '';
    if (empty($_POST['ip']))
    {
        echo 'Invalid ip.<br />
        &gt; <a href="staff_punit.php?action=ipform">Go Back</a>';
        die($h->endpage());
    }
    $echoip =
            htmlentities(stripslashes($_POST['ip']), ENT_QUOTES, 'ISO-8859-1');
    echo "
    Searching for users with the IP: <b>{$echoip}</b>
    <br />
    <table width='75%' class='table' cellpadding='1' cellspacing='1'>
    		<tr>
    			<th>User</th>
    			<th>Level</th>
    			<th>Money</th>
    		</tr>
       ";
    $q =
            $db->query(
                    "SELECT `userid`, `username`, `level`, `money`
                     FROM `users`
                     WHERE `lastip` = '"
                            . $db->escape(stripslashes($_POST['ip'])) . "'");
    $ids = array();
    while ($r = $db->fetch_row($q))
    {
        $ids[] = $r['userid'];
        echo "
		<tr>
        	<td>
        		<a href='viewuser.php?u={$r['userid']}'>{$r['username']}</a>
        	</td>
        	<td>{$r['level']}</td>
        	<td>{$r['money']}</td>
        </tr>
           ";
    }
    $csrf = request_csrf_html('staff_massjail');
    echo "
    </table>
    <br />
    <b>Mass Jail</b>
    <br />
    <form action='staff_punit.php?action=massjailip' method='post'>
    	<input type='hidden' name='ids' value='" . implode(",", $ids)
            . "' />
    	Days: <input type='text' name='days' value='300' />
    	<br />
    	Reason: <input type='text' name='reason'
    		value='Same IP users, Mail fedjail@{$domain} with your case.' />
    	<br />
    	{$csrf}
    	<input type='submit' value='Mass Jail' />
    </form>
       ";
}

function mass_jail()
{
    global $db, $ir, $c, $h, $userid;
    staff_csrf_stdverify('staff_massjail', 'staff_punit.php?action=ipform');
    if (!isset($_POST['ids']))
    {
        $_POST['ids'] = '';
    }
    $ids = explode(",", $_POST['ids']);
    $ju = array();
    $_POST['reason'] =
            (isset($_POST['reason']))
                    ? $db->escape(strip_tags(stripslashes($_POST['reason'])))
                    : '';
    $_POST['days'] =
            (isset($_POST['days']) && is_numeric($_POST['days']))
                    ? abs(intval($_POST['days'])) : '';
    if ((count($ids) == 1 && empty($ids[0])) || empty($_POST['reason'])
            || empty($_POST['days']))
    {
        echo 'You need to fill in all the fields.<br />
        &gt; <a href="staff_punit.php?action=ipform">Go Back</a>';
        die($h->endpage());
    }
    foreach ($ids as $id)
    {
        if (is_numeric($id) && abs((int) $id) > 0)
        {
            $safe_id = abs((int) $id);
            $db->query(
                    "INSERT INTO `fedjail`
                     VALUES(NULL, {$safe_id}, {$_POST['days']}, $userid,
                     '{$_POST['reason']}')");
            $db->query(
                    "INSERT INTO `jaillogs`
                     VALUES(NULL, $userid, {$safe_id}, {$_POST['days']},
                     '{$_POST['reason']}', " . time() . ")");
            echo 'User jailed : ' . $id . '<br />';
            $ju[] = $id;
        }
    }
    if (count($ju) > 0)
    {
        $juv = implode(',', $ju);
        $re =
                $db->query(
                        "UPDATE `users`
                         SET `fedjail` = 1
                         WHERE `userid` IN($juv)");
        stafflog_add('Mass jailed IDs ' . $juv);
        echo '&gt; <a href="staff.php">Go Home</a>';
        die($h->endpage());
    }
    else
    {
        echo 'No users jailed...<br />';
        echo '&gt; <a href="staff.php">Go Home</a>';
        die($h->endpage());
    }
}
$h->endpage();
