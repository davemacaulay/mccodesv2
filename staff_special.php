<?php
declare(strict_types=1);
/**
 * MCCodes v2 by Dabomstew & ColdBlooded
 *
 * Repository: https://github.com/davemacaulay/mccodesv2
 * License: MIT License
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
    if (!check_access('edit_newspaper')) {
        echo 'You cannot access this area.
        <br />&gt; <a href="index.php">Go Home</a>';
        $h->endpage();
        exit;
    }
    newspaper_form();
    break;
case 'subnews':
    if (!check_access('edit_newspaper')) {
        echo 'You cannot access this area.
        <br />&gt; <a href="index.php">Go Home</a>';
        $h->endpage();
        exit;
    }
    newspaper_submit();
    break;
case 'givedpform':
    if (!check_access('manage_donator_packs')) {
        echo 'You cannot access this area.
        <br />&gt; <a href="index.php">Go Home</a>';
        $h->endpage();
        exit;
    }
    give_dp_form();
    break;
case 'givedpsub':
    if (!check_access('manage_donator_packs')) {
        echo 'You cannot access this area.
        <br />&gt; <a href="index.php">Go Home</a>';
        $h->endpage();
        exit;
    }
    give_dp_submit();
    break;
case 'massmailer':
    if (!check_access('mass_mail')) {
        echo 'You cannot access this area.
        <br />&gt; <a href="index.php">Go Home</a>';
        $h->endpage();
        exit;
    }
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

function populate_recipients(array &$recipients, mysqli_result|false $get_users): void
{
    global $db;
    while ($row = $db->fetch_row($get_users)) {
        $recipients[] = $row['userid'];
    }
}

/**
 * @return void
 */
function massmailer(): void
{
    global $db, $h;
    $_POST['text']  =
        (isset($_POST['text']))
            ? $db->escape(strip_tags(stripslashes($_POST['text'])))
            : '';
    $_POST['recipients'] = array_key_exists('recipients', $_POST) ? $_POST['recipients'] : null;
    $_POST['recipient-roles'] = array_key_exists('recipient-roles', $_POST) && is_array($_POST['recipient-roles']) ? $_POST['recipient-roles'] : null;
    if (!empty($_POST['text'])) {
        staff_csrf_stdverify('staff_massmailer',
                'staff_special.php?action=massmailer');
        $recipients = [];
        $subj       = 'Mass mail from Administrator';
        if ($_POST['recipients'] === 'all') {
            $get_users = $db->query(
                'SELECT userid FROM users WHERE user_level > 0',
            );
            populate_recipients($recipients, $get_users);
        } elseif ($_POST['recipients'] === 'staff') {
            $get_users = $db->query(
                'SELECT userid FROM users_roles WHERE staff_role > 0',
            );
            while ($row = $db->fetch_row($get_users)) {
                $recipients[] = $row['userid'];
            }
        } elseif ($_POST['recipients'] === 'admin') {
            $get_roles = $db->query(
                'SELECT id FROM staff_roles WHERE administrator = true',
            );
            $roles     = [];
            while ($role = $db->fetch_row($get_roles)) {
                $roles[] = $role['id'];
            }
            $get_users = $db->query(
                'SELECT userid FROM users_roles WHERE staff_role IN (' . implode(',', $roles) . ')',
            );
            populate_recipients($recipients, $get_users);
        } elseif (!empty($_POST['recipient-roles'])) {
            $recipient_roles = implode(',', array_unique(array_filter(array_map(function ($role) {
                return abs(intval($role));
            }, $_POST['recipient-roles']))));
            if (empty($recipient_roles)) {
                echo 'Invalid role(s) selected';
                $h->endpage();
                exit;
            }
            $check_roles = $db->query(
                'SELECT COUNT(id) FROM staff_roles WHERE id IN (' . $recipient_roles . ')'
            );
            if ((int)$db->fetch_single($check_roles) !== count($_POST['recipient-roles'])) {
                echo 'Invalid role(s) selected';
                $h->endpage();
                exit;
            }
            $get_users = $db->query(
                'SELECT userid FROM users_roles WHERE staff_role IN (' . $recipient_roles . ') GROUP BY userid'
            );
            populate_recipients($recipients, $get_users);
        }
        if (empty($recipients)) {
            echo 'No recipients found';
            $h->endpage();
            exit;
        }
        $uc        = [];
        $send_time = time();
        foreach ($recipients as $recipient) {
            $db->query(
                "INSERT INTO `mail`
                     VALUES(NULL, 0, 0, {$recipient}, {$send_time},
                     '$subj', '{$_POST['text']}')");
            $uc[] = $recipient;
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
        $get_roles = $db->query(
            'SELECT id, name FROM staff_roles ORDER BY id',
        );
        echo "
        <b>Mass Mailer</b>
        <br />
        <form action='staff_special.php?action=massmailer' method='post'>
            Text: <br />
            <textarea name='text' rows='7' cols='40'></textarea>
            <br />
            <input type='radio' name='recipients' value='all' /> Send to all members
            <input type='radio' name='recipients' value='staff' /> Send to staff only
            <input type='radio' name='recipients' value='admin' /> Send to admins only
            <br />
            OR Send to specific staff role(s):
            <br />
            ";
        while ($role = $db->fetch_row($get_roles)) {
            echo '
                    <label for="role-' . $role['id'] . '">
                        <input type="checkbox" name="recipient-roles[]" id="role-' . $role['id'] . '" value="' . $role['id'] . '" />
                        ' . $role['name'] . '
                    </label><br>
                ';
        }
        echo "
            <br />
            {$csrf}
            <input type='submit' value='Send' />
        </form>
           ";
    }
}
$h->endpage();
