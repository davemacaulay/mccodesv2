<?php
declare(strict_types=1);
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
 * @noinspection SpellCheckingInspection
 */

global $ir, $h;
require_once('sglobals.php');
if (!isset($_GET['action']))
{
    $_GET['action'] = '';
}
switch ($_GET['action'])
{
case 'editnews':
    check_access('edit_newspaper');
    newspaper_form();
    break;
case 'subnews':
    check_access('edit_newspaper');
    newspaper_submit();
    break;
case 'givedpform':
    check_access('manage_donator_packs');
    give_dp_form();
    break;
case 'givedpsub':
    check_access('manage_donator_packs');
    give_dp_submit();
    break;
case 'massmailer':
    check_access('mass_mail');
    massmailer();
    break;
default:
    echo 'Error: This script requires an action.';
    break;
}

/**
 * @return void
 */
function newspaper_form(): void
{
    global $db;
    $q = $db->query('SELECT `content` FROM `papercontent`');
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

/**
 * @return void
 */
function newspaper_submit(): void
{
    global $db;
    staff_csrf_stdverify('staff_editnews', 'staff_special.php?action=editnews');
    $news = $db->escape(strip_tags(stripslashes($_POST['newspaper'])));
    $db->query("UPDATE `papercontent`
    			SET `content` = '$news'");
    echo 'Newspaper updated!';
    stafflog_add('Updated game newspaper');
}

/**
 * @return void
 */
function give_dp_form(): void
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

/**
 * @return void
 */
function give_dp_submit(): void
{
    global $db, $h;
    staff_csrf_stdverify('staff_givedp', 'staff_special.php?action=givedpform');
    $_POST['user'] =
            (isset($_POST['user']) && is_numeric($_POST['user']))
                    ? abs(intval($_POST['user'])) : '';
    $_POST['type'] =
            (isset($_POST['type'])
                    && in_array($_POST['type'], [1, 2, 3, 4, 5]))
                    ? abs((int) $_POST['type']) : '';
    if (empty($_POST['user']) || empty($_POST['type']))
    {
        echo 'Something went wrong.<br />
        &gt; <a href="staff_special.php?action=givedpform">Go Back</a>';
        $h->endpage();
        exit;
    }
    $don = 'u.userid = u.userid';
    $d = 0;
    if ($_POST['type'] == 1)
    {
        $don =
            '`u`.`money` = `u`.`money` + 5000,
                 `u`.`crystals` = `u`.`crystals` + 50,
                 `us`.`IQ` = `us`.`IQ` + 50,
                 `u`.`donatordays` = `u`.`donatordays` + 30';
        $d = 30;
    } elseif ($_POST['type'] == 2) {
        $don =
            '`u`.`crystals` = `u`.`crystals` + 100,
                 `u`.`donatordays` = `u`.`donatordays` + 30';
        $d   = 30;
    } elseif ($_POST['type'] == 3) {
        $don =
            '`us`.`IQ` = `us`.`IQ` + 120,
                 `u`.`donatordays` = `u`.`donatordays` + 30';
        $d   = 30;
    } elseif ($_POST['type'] == 4) {
        $don =
            '`u`.`money` = `u`.`money` + 15000,
                 `u`.`crystals` = `u`.`crystals` + 75,
                 `us`.`IQ` = `us`.`IQ` + 80,
                 `u`.`donatordays` = `u`.`donatordays` + 55';
        $d   = 55;
    } elseif ($_POST['type'] == 5) {
        $don =
            '`u`.`money` = `u`.`money` + 35000,
                 `u`.`crystals` = `u`.`crystals` + 160,
                 `us`.`IQ` = `us`.`IQ` + 180,
                 `u`.`donatordays` = `u`.`donatordays` + 115';
        $d   = 115;
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
    $h->endpage();
    exit;
}

/**
 * @return void
 */
function massmailer(): void
{
    global $db, $h;
    $_POST['text'] =
            (isset($_POST['text']))
                    ? $db->escape(strip_tags(stripslashes($_POST['text'])))
                    : '';
    $_POST['cat'] =
            (isset($_POST['cat']) && in_array($_POST['cat'], [1, 2, 3]))
                    ? $_POST['cat'] : '';
    $_POST['level'] =
            (isset($_POST['level'])
                    && in_array($_POST['level'], [1, 2, 3, 5]))
                    ? abs((int) $_POST['level']) : '';
    if (!empty($_POST['text'])
            && (!empty($_POST['cat']) || empty($_POST['level'])))
    {
        if (!empty($_POST['cat']) && !empty($_POST['level']))
        {
            echo 'Please select one of the sending options, not both.<br />
            &gt; <a href="staff_special.php?action=massmailer">Try again</a>';
            $h->endpage();
            exit;
        }
        staff_csrf_stdverify('staff_massmailer',
                'staff_special.php?action=massmailer');
        $subj = 'Mass mail from Administrator';
        $get_roles = $db->query(
            'SELECT * FROM staff_roles',
        );
        $roles = [];
        while ($role = $db->fetch_row($get_roles)) {
            $roles[] = $role;
        }
        $administrators = array_map(function ($role) {
            if ($role['administrator']) {
                return $role['id'];
            }
            return null;
        }, $roles);
        if ($_POST['cat'] == 1)
        {
            $q =
                    $db->query(
                        'SELECT `userid`
                             FROM `users`
                             WHERE `user_level` != 0');
        } elseif ($_POST['cat'] == 2) {
            $q =
                $db->query(
                    'SELECT `userid`
                             FROM `users`
                             WHERE `user_level` > 1');
        } elseif ($_POST['cat'] == 3) {
            $q =
                $db->query(
                    'SELECT `userid`
                             FROM users
                             WHERE `user_level` = 2');
        } else {
            $q =
                $db->query(
                    "SELECT `userid`
                             FROM `users`
                             WHERE `user_level` = {$_POST['level']}");
        }
        $uc = [];
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
