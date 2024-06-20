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
 * File: staff_special.php
 * Signature: 3adb819832a38f3972bd3195eabc2917
 * Date: Fri, 20 Apr 12 08:50:30 +0000
 */

require_once('sglobals.php');
if ($ir['user_level'] != 2)
{
    echo 'You cannot access this area.<br />
    &gt; <a href="staff.php">Go Back</a>';
    die($h->endpage());
}
if (!isset($_GET['action']))
{
    $_GET['action'] = '';
}
switch ($_GET['action'])
{
case 'editnews':
    newspaper_form();
    break;
case 'subnews':
    newspaper_submit();
    break;
case 'givedpform':
    give_dp_form();
    break;
case 'givedpsub':
    give_dp_submit();
    break;
case 'stafflist':
    staff_list();
    break;
case 'userlevel':
    userlevel();
    break;
case 'userlevelform':
    userlevelform();
    break;
case 'massmailer':
    massmailer();
    break;
default:
    echo 'Error: This script requires an action.';
    break;
}

function newspaper_form()
{
    global $db;
    $q = $db->query("SELECT `content` FROM `papercontent`");
    $news = $db->fetch_row($q);
    $csrf = request_csrf_html('staff_editnews');
    echo "
    <h3>Editing Newspaper</h3>
    <form action='staff_special.php?action=subnews' method='post'>
    	<textarea rows='7' cols='35' name='newspaper'>" . $news['content']
            . "</textarea>
    	<br />
    	{$csrf}
    	<input type='submit' value='Change' />
    </form>
   ";
}

function newspaper_submit()
{
    global $db;
    staff_csrf_stdverify('staff_editnews', 'staff_special.php?action=editnews');
    $news = $db->escape(strip_tags(stripslashes($_POST['newspaper'])));
    $db->query("UPDATE `papercontent`
    			SET `content` = '$news'");
    echo 'Newspaper updated!';
    stafflog_add("Updated game newspaper");
}

function give_dp_form()
{
    $csrf = request_csrf_html('staff_givedp');
    echo "
    <h3>Giving User DP</h3>
    The user will receive the benefits of one 30-day donator pack.
    <br />
    <form action='staff_special.php?action=givedpsub' method='post'>
    	User: " . user_dropdown()
            . "
    	<br />
    	<input type='radio' name='type' value='1' /> Pack 1 (Standard)
    	<br />
    	<input type='radio' name='type' value='2' /> Pack 2 (Crystals)
    	<br />
    	<input type='radio' name='type' value='3' /> Pack 3 (IQ)
    	<br />
    	<input type='radio' name='type' value='4' /> Pack 4 (5.00)
    	<br />
    	<input type='radio' name='type' value='5' /> Pack 5 (10.00)
    	<br />
    	{$csrf}
    	<input type='submit' value='Give User DP' />
    </form>
       ";
}

function give_dp_submit()
{
    global $db, $c, $h;
    staff_csrf_stdverify('staff_givedp', 'staff_special.php?action=givedpform');
    $_POST['user'] =
            (isset($_POST['user']) && is_numeric($_POST['user']))
                    ? abs(intval($_POST['user'])) : '';
    $_POST['type'] =
            (isset($_POST['type'])
                    && in_array($_POST['type'], array(1, 2, 3, 4, 5)))
                    ? abs((int) $_POST['type']) : '';
    if (empty($_POST['user']) || empty($_POST['type']))
    {
        echo 'Something went wrong.<br />
        &gt; <a href="staff_special.php?action=givedpform">Go Back</a>';
        die($h->endpage());
    }
    if ($_POST['type'] == 1)
    {
        $don =
                "`u`.`money` = `u`.`money` + 5000,
                 `u`.`crystals` = `u`.`crystals` + 50,
                 `us`.`IQ` = `us`.`IQ` + 50,
                 `u`.`donatordays` = `u`.`donatordays` + 30";
        $d = 30;
    }
    else if ($_POST['type'] == 2)
    {
        $don =
                "`u`.`crystals` = `u`.`crystals` + 100,
                 `u`.`donatordays` = `u`.`donatordays` + 30";
        $d = 30;
    }
    else if ($_POST['type'] == 3)
    {
        $don =
                "`us`.`IQ` = `us`.`IQ` + 120,
                 `u`.`donatordays` = `u`.`donatordays` + 30";
        $d = 30;
    }
    else if ($_POST['type'] == 4)
    {
        $don =
                "`u`.`money` = `u`.`money` + 15000,
                 `u`.`crystals` = `u`.`crystals` + 75,
                 `us`.`IQ` = `us`.`IQ` + 80,
                 `u`.`donatordays` = `u`.`donatordays` + 55";
        $d = 55;
    }
    else if ($_POST['type'] == 5)
    {
        $don =
                "`u`.`money` = `u`.`money` + 35000,
                 `u`.`crystals` = `u`.`crystals` + 160,
                 `us`.`IQ` = `us`.`IQ` + 180,
                 `u`.`donatordays` = `u`.`donatordays` + 115";
        $d = 115;
    }
    $db->query(
            "UPDATE `users` AS `u`
             INNER JOIN `userstats` AS `us`
             ON `u`.`userid` = `us`.`userid`
             SET {$don}
             WHERE `u`.`userid` = {$_POST['user']}");
    event_add($_POST['user'],
        "You were given one {$d}-day donator pack (Pack {$_POST['type']}) from the administration.");
    stafflog_add(
            "Gave ID {$_POST['user']} a {$d}-day donator pack (Pack {$_POST['type']})");
    echo 'User given a DP.<br />
    &gt; <a href="staff.php">Go Home</a>';
    die($h->endpage());
}

function staff_userlevel_innerform($userid, $level, $desc, $csrf)
{
    return "
<form action='staff_special.php?action=userlevel' method='post'>
<input type='hidden' name='ID' value='{$userid}' />
<input type='hidden' name='level' value='{$level}' />
{$csrf}
<input type='submit' value='{$desc}' />
</form>
";
}

function staff_list()
{
    global $db;
    echo "
    <h3>Staff Management</h3>
    <b>Admins</b>
    <br />
    <table width='80%' cellpadding='1' cellspacing='1' class='table'>
    		<tr>
    			<th>User</th>
    			<th>Status</th>
    			<th>Links</th>
    		</tr>
       ";
    $csrf = request_csrf_html('staff_userlevel');
    $staff = array();
    $q =
            $db->query(
                    "SELECT `userid`, `laston`, `username`, `level`, `money`,
 				 	 `user_level`
 				 	 FROM `users`
 				 	 WHERE `user_level` IN(2, 3, 5)
 				 	 ORDER BY `userid` ASC");
    while ($r = $db->fetch_row($q))
    {
        $staff[$r['userid']] = $r;
    }
    $db->free_result($q);
    foreach ($staff as $r)
    {
        if ($r['user_level'] == 2)
        {
            $on =
                    (($r['laston'] >= $_SERVER['REQUEST_TIME'] - 15) * 60)
                            ? '<span style="color: green;">Online</span>'
                            : '<span style="color: red;">Offline</span>';
            echo "
    		<tr>
    			<td>
    				<a href='viewuser.php?u={$r['userid']}'>{$r['username']}</a>
    				[{$r['userid']}]
    			</td>
    			<td>$on</td>
    			<td>
    				"
                    . staff_userlevel_innerform($r['userid'], 3, 'Secretary',
                            $csrf) . "
    				&middot; "
                    . staff_userlevel_innerform($r['userid'], 5, 'Assistant',
                            $csrf) . "
    				&middot; "
                    . staff_userlevel_innerform($r['userid'], 1, 'Member',
                            $csrf) . "
    			</td>
    		</tr>
       		";
        }
    }
    echo "
    </table>
    <b>Secretaries</b>
    <br />
    <table width='80%' cellpadding='1' cellspacing='1' class='table'>
    		<tr>
    			<th>User</th>
    			<th>Status</th>
    			<th>Links</th>
    		</tr>
       ";
    foreach ($staff as $r)
    {
        if ($r['user_level'] == 3)
        {
            $on =
                    (($r['laston'] >= $_SERVER['REQUEST_TIME'] - 15) * 60)
                            ? '<span style="color: green;">Online</span>'
                            : '<span style="color: red;">Offline</span>';
            echo "
    		<tr>
    			<td>
    				<a href='viewuser.php?u={$r['userid']}'>{$r['username']}</a>
    				[{$r['userid']}]
    			</td>
    			<td>$on</td>
    			<td>
    				"
                    . staff_userlevel_innerform($r['userid'], 2, 'Admin',
                            $csrf) . "
    				&middot; "
                    . staff_userlevel_innerform($r['userid'], 5, 'Assistant',
                            $csrf) . "
    				&middot; "
                    . staff_userlevel_innerform($r['userid'], 1, 'Member',
                            $csrf) . "
    			</td>
    		</tr>
       		";
        }
    }
    echo "
    </table>
    <b>Assistants</b>
    <br />
    <table width='80%' cellpadding='1' cellspacing='1' class='table'>
    		<tr>
    			<th>User</th>
    			<th>Status</th>
    			<th>Links</th>
    		</tr>
       ";
    foreach ($staff as $r)
    {
        if ($r['user_level'] == 5)
        {
            $on =
                    (($r['laston'] >= $_SERVER['REQUEST_TIME'] - 15) * 60)
                            ? '<span style="color: green;">Online</span>'
                            : '<span style="color: red;">Offline</span>';
            echo "
    		<tr>
    			<td>
    				<a href='viewuser.php?u={$r['userid']}'>{$r['username']}</a>
    				[{$r['userid']}]
    			</td>
    			<td>$on</td>
    			<td>
    				"
                    . staff_userlevel_innerform($r['userid'], 2, 'Admin',
                            $csrf) . "
    				&middot; "
                    . staff_userlevel_innerform($r['userid'], 3, 'Secretary',
                            $csrf) . "
    				&middot; "
                    . staff_userlevel_innerform($r['userid'], 1, 'Member',
                            $csrf) . "
    			</td>
    		</tr>
       		";
        }
    }
    echo '</table>';
}

function userlevel()
{
    global $db, $h;
    staff_csrf_stdverify('staff_userlevel',
            'staff_special.php?action=userlevelform');
    $_POST['level'] =
            (isset($_POST['level'])
                    && in_array($_POST['level'], array(1, 2, 3, 4, 5)))
                    ? abs(intval($_POST['level'])) : 0;
    $_POST['ID'] =
            (isset($_POST['ID']) && is_numeric($_POST['ID']))
                    ? abs(intval($_POST['ID'])) : 0;
    if ($_POST['ID'] == 0 || $_POST['level'] == 0)
    {
        echo 'Invalid input.<br />
        &gt; <a href="staff_special.php?action=userlevelform">Go Home</a>';
        die($h->endpage());
    }
    $d =
            $db->query(
                    'SELECT COUNT(`userid`)
                     FROM `users`
                     WHERE `userid` = ' . $_POST['ID']);
    if ($db->fetch_single($d) == 0)
    {
        $db->free_result($d);
        echo 'Invalid user.<br />
        &gt; <a href="staff_special.php?action=userlevelform">Go Home</a>';
        die($h->endpage());
    }
    $db->free_result($d);
    $db->query(
            "UPDATE `users`
             SET `user_level` = {$_POST['level']}
             WHERE `userid` = {$_POST['ID']}");
    stafflog_add('Adjusted user ID ' . $_POST['ID'] . '\'s staff status.');
    echo 'User\'s level adjusted.<br />
    &gt; <a href="staff.php">Go Home</a>';
    die($h->endpage());
}

function userlevelform()
{
    $csrf = request_csrf_html('staff_userlevel');
    echo "
    <h3>User Level Adjust</h3>
    <form action='staff_special.php?action=userlevel' method='post'>
    	User: " . user_dropdown('ID')
            . "
    	<br />
    	User Level:
    	<br />
    	<input type='radio' name='level' value='1' /> Member
    	<br />
    	<input type='radio' name='level' value='2' /> Admin
    	<br />
    	<input type='radio' name='level' value='3' /> Secretary
    	<br />
    	<input type='radio' name='level' value='4' /> IRC Op
    	<br />
    	<input type='radio' name='level' value='5' /> Assistant
    	<br />
    	{$csrf}
    	<input type='submit' value='Adjust' />
    </form>
    ";
}

function massmailer()
{
    global $db;
    $_POST['text'] =
            (isset($_POST['text']))
                    ? $db->escape(strip_tags(stripslashes($_POST['text'])))
                    : '';
    $_POST['cat'] =
            (isset($_POST['cat']) && in_array($_POST['cat'], array(1, 2, 3)))
                    ? $_POST['cat'] : '';
    $_POST['level'] =
            (isset($_POST['level'])
                    && in_array($_POST['level'], array(1, 2, 3, 5)))
                    ? abs((int) $_POST['level']) : '';
    if (!empty($_POST['text'])
            && (!empty($_POST['cat']) || empty($_POST['level'])))
    {
        if (!empty($_POST['cat']) && !empty($_POST['level']))
        {
            echo 'Please select one of the sending options, not both.<br />
            &gt; <a href="staff_special.php?action=massmailer">Try again</a>';
            die($h->endpage());
        }
        staff_csrf_stdverify('staff_massmailer',
                'staff_special.php?action=massmailer');
        $subj = 'Mass mail from Administrator';
        if ($_POST['cat'] == 1)
        {
            $q =
                    $db->query(
                            "SELECT `userid`
                             FROM `users`
                             WHERE `user_level` != 0");
        }
        else if ($_POST['cat'] == 2)
        {
            $q =
                    $db->query(
                            "SELECT `userid`
                             FROM `users`
                             WHERE `user_level` > 1");
        }
        else if ($_POST['cat'] == 3)
        {
            $q =
                    $db->query(
                            "SELECT `userid`
                             FROM users
                             WHERE `user_level` = 2");
        }
        else
        {
            $q =
                    $db->query(
                            "SELECT `userid`
                             FROM `users`
                             WHERE `user_level` = {$_POST['level']}");
        }
        $uc = array();
        $send_time = time();
        while ($r = $db->fetch_row($q))
        {
            $db->query(
                    "INSERT INTO `mail`
                     VALUES(NULL, 0, 0, {$r['userid']}, {$send_time},
                     '$subj', '{$_POST['text']}')");
            $uc[] = $r['userid'];
        }

        $us_im = implode(',', $uc);
        $db->query(
                'UPDATE `users`
                 SET `new_mail` = `new_mail` + 1
                 WHERE `userid` IN(' . $us_im . ')');
        echo '
        Sent ' . count($uc)
                . ' Mails.
        <br />
        &gt; <a href="staff.php">Go Home</a>
           ';
    }
    else
    {
        $csrf = request_csrf_html('staff_massmailer');
        echo "
        <b>Mass Mailer</b>
        <br />
        <form action='staff_special.php?action=massmailer' method='post'>
        	Text: <br />
        	<textarea name='text' rows='7' cols='40'></textarea>
        	<br />
        	<input type='radio' name='cat' value='1' /> Send to all members
        	<input type='radio' name='cat' value='2' /> Send to staff only
        	<input type='radio' name='cat' value='3' /> Send to admins only
        	<br />
        	OR Send to user level:
        	<br />
        	<input type='radio' name='level' value='1' /> Member
        	<br />
        	<input type='radio' name='level' value='2' /> Admin
        	<br />
        	<input type='radio' name='level' value='3' /> Secretary
        	<br />
        	<input type='radio' name='level' value='5' /> Assistant
        	<br />
        	{$csrf}
        	<input type='submit' value='Send' />
        </form>
           ";
    }
}
$h->endpage();
