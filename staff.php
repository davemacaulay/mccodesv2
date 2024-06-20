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
 * File: staff.php
 * Signature: 740ee1855cec0570ee80fddac656162b
 * Date: Fri, 20 Apr 12 08:50:30 +0000
 */

global $h;
require_once('sglobals.php');
if (!isset($_GET['action']))
{
    $_GET['action'] = 'index';
}
switch ($_GET['action'])
{
case 'basicset':
    basicsettings();
    break;
case 'announce':
    announcements();
    break;
default:
    index();
    break;
}

function basicsettings(): void
{
    global $db, $ir, $h, $set;
    if ($ir['user_level'] != 2)
    {
        echo 'You cannot access this area.<br />
        &gt; <a href="staff.php">Go Back</a>';
        die($h->endpage());
    }
    $_POST['game_name'] =
            (isset($_POST['game_name'])
                    && preg_match(
                            "/^[a-z0-9_.]+([\\s]{1}[a-z0-9_.]|[a-z0-9_.])+$/i",
                            $_POST['game_name']))
                    ? $db->escape(
                            strip_tags(stripslashes($_POST['game_name']))) : '';
    $_POST['game_owner'] =
            (isset($_POST['game_owner'])
                    && preg_match(
                            "/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])+$/i",
                            $_POST['game_owner']))
                    ? $db->escape(
                            strip_tags(stripslashes($_POST['game_owner'])))
                    : '';
    $_POST['game_description'] =
            (isset($_POST['game_description']))
                    ? $db->escape(
                            strip_tags(
                                    stripslashes($_POST['game_description'])))
                    : '';
    $_POST['paypal'] =
            (isset($_POST['paypal'])
                    && filter_input(INPUT_POST, 'paypal',
                            FILTER_VALIDATE_EMAIL))
                    ? $db->escape(stripslashes($_POST['paypal'])) : '';
    $_POST['ct_refillprice'] =
            (isset($_POST['ct_refillprice'])
                    && is_numeric($_POST['ct_refillprice']))
                    ? abs(intval($_POST['ct_refillprice'])) : '';
    $_POST['ct_iqpercrys'] =
            (isset($_POST['ct_iqpercrys'])
                    && is_numeric($_POST['ct_iqpercrys']))
                    ? abs(intval($_POST['ct_iqpercrys'])) : '';
    $_POST['ct_moneypercrys'] =
            (isset($_POST['ct_moneypercrys'])
                    && is_numeric($_POST['ct_moneypercrys']))
                    ? abs(intval($_POST['ct_moneypercrys'])) : '';
    $_POST['willp_item'] =
            (isset($_POST['willp_item']) && is_numeric($_POST['willp_item']))
                    ? abs(intval($_POST['willp_item'])) : '';
    $_POST['validate_on'] =
            (isset($_POST['validate_on'])
                    && in_array($_POST['validate_on'], ['1', '0'], true))
                    ? $_POST['validate_on'] : FALSE;
    $_POST['validate_period'] =
            (isset($_POST['validate_period'])
                    && in_array($_POST['validate_period'],
                            ['5', '15', '60', 'login'], true))
                    ? $_POST['validate_period'] : FALSE;
    $_POST['regcap_on'] =
            (isset($_POST['regcap_on'])
                    && in_array($_POST['regcap_on'], ['1', '0'], true))
                    ? $_POST['regcap_on'] : FALSE;
    $_POST['sendcrys_on'] =
            (isset($_POST['sendcrys_on'])
                    && in_array($_POST['sendcrys_on'], ['1', '0'], true))
                    ? $_POST['sendcrys_on'] : FALSE;
    $_POST['sendbank_on'] =
            (isset($_POST['sendbank_on'])
                    && in_array($_POST['sendbank_on'], ['1', '0'], true))
                    ? $_POST['sendbank_on'] : FALSE;
    if (empty($_POST['game_name']) || empty($_POST['game_owner'])
            || empty($_POST['game_description']) || empty($_POST['paypal'])
            || empty($_POST['ct_refillprice'])
            || empty($_POST['ct_iqpercrys'])
            || empty($_POST['ct_moneypercrys'])
            || is_bool($_POST['validate_on'])
            || is_bool($_POST['validate_period'])
            || is_bool($_POST['regcap_on']) || is_bool($_POST['sendcrys_on'])
            || is_bool($_POST['sendbank_on']))
    {
        $csrf = request_csrf_html('staff_basicset');
        echo "
        <h3>Basic Settings</h3>
        <hr />
        <form action='staff.php?action=basicset' method='post'>
        	Game Name: <input type='text' name='game_name' value='{$set['game_name']}' /><br />
        	Game Owner: <input type='text' name='game_owner' value='{$set['game_owner']}' /><br />
        	Game Description:<br />
        	<textarea rows='7' cols='50' name='game_description'>{$set['game_description']}</textarea><br />
        	Paypal Address: <input type='text' name='paypal' value='{$set['paypal']}' /><br />
        	Gym/Crimes Validation: <select name='validate_on' type='dropdown'>
           ";
        $opt = ['1' => 'On', '0' => 'Off'];
        foreach ($opt as $k => $v)
        {
            echo ($k == $set['validate_on'])
                    ? '<option value="' . $k . '" selected="selected">' . $v
                            . '</option>'
                    : '<option value="' . $k . '">' . $v . '</option>';
        }
        echo "
        </select>
        <br />
        	Validation Period: <select name='validate_period' type='dropdown'>";
        $opt =
                ['5' => 'Every 5 Minutes', '15' => 'Every 15 Minutes',
                        '60' => 'Every Hour', 'login' => 'Every Login'];
        foreach ($opt as $k => $v)
        {
            echo ($k == $set['validate_period'])
                    ? '<option value="' . $k . '" selected="selected">' . $v
                            . '</option>'
                    : '<option value="' . $k . '">' . $v . '</option>';
        }
        echo "
        </select>
        <br />
        	Registration CAPTCHA: <select name='regcap_on' type='dropdown'>";
        $opt = ['1' => 'On', '0' => 'Off'];
        foreach ($opt as $k => $v)
        {
            echo ($k == $set['regcap_on'])
                    ? '<option value="' . $k . '" selected="selected">' . $v
                            . '</option>'
                    : '<option value="' . $k . '">' . $v . '</option>';
        }
        echo "
        </select>
        <br />
        	Send Crystals: <select name='sendcrys_on' type='dropdown'>";
        $opt = ['1' => 'On', '0' => 'Off'];
        foreach ($opt as $k => $v)
        {
            echo ($k == $set['sendcrys_on'])
                    ? '<option value="' . $k . '" selected="selected">' . $v
                            . '</option>'
                    : '<option value="' . $k . '">' . $v . '</option>';
        }
        echo "
        </select>
        <br />
        	Bank Transfers: <select name='sendbank_on' type='dropdown'>";
        $opt = ['1' => 'On', '0' => 'Off'];
        foreach ($opt as $k => $v)
        {
            echo ($k == $set['sendbank_on'])
                    ? '<option value="' . $k . '" selected="selected">' . $v
                            . '</option>'
                    : '<option value="' . $k . '">' . $v . '</option>';
        }
        echo "
        </select>
        <br />
        	Energy Refill Price (crystals):
        		<input type='text' name='ct_refillprice' value='{$set['ct_refillprice']}' />
        	<br />
        	IQ per crystal:
        		<input type='text' name='ct_iqpercrys' value='{$set['ct_iqpercrys']}' />
        	<br />
        	Money per crystal:
        		<input type='text' name='ct_moneypercrys' value='{$set['ct_moneypercrys']}' />
        	<br />
        	Will Potion Item: "
                . item_dropdown('willp_item', $set['willp_item'])
                . "<br />
            {$csrf}
        	<input type='submit' value='Update Settings' />
        </form>
           ";
    }
    else
    {
        staff_csrf_stdverify('staff_basicset', 'staff.php?action=basicset');
        unset($_POST['verf']);
        if (!empty($_POST['willp_item']))
        {
            $qi =
                    $db->query(
                            'SELECT `itmid`
                             FROM `items`
                             WHERE `itmid` = ' . $_POST['willp_item']);
            if ($db->num_rows($qi) == 0)
            {
                echo '
				The item you tried to input doesn\'t seem to be a real item.<br />
				&gt; <a href="staff.php?action=basicset">Go Back</a>
   				';
                die($h->endpage());
            }
        }
        else
        {
            $_POST['willp_item'] = 0;
            echo 'Please remember to make a will potion item and set it<br />';
        }
        foreach ($_POST as $k => $v)
        {
            $db->query(
                    "UPDATE `settings`
                     SET `conf_value` = '$v'
                     WHERE `conf_name` = '$k'");
        }
        echo '
        Settings updated!<br />
        &gt; <a href="staff.php?action=basicset">Go Back</a>
           ';
        stafflog_add('Updated the basic game settings');
    }
}

function announcements(): void
{
    global $db, $ir, $h;
    if ($ir['user_level'] != 2)
    {
        echo 'You cannot access this area.<br />
        &gt; <a href="staff.php">Go Back</a>';
        die($h->endpage());
    }
    if (!empty($_POST['text']))
    {
        staff_csrf_stdverify('staff_announcement', 'staff.php?action=announce');
        $_POST['text'] =
                $db->escape(
                        htmlentities(stripslashes($_POST['text']), ENT_QUOTES,
                                'ISO-8859-1'));
        $db->query(
                "INSERT INTO `announcements`
                 VALUES('{$_POST['text']}', " . time() . ')');
        $db->query(
            'UPDATE `users`
                 SET `new_announcements` = `new_announcements` + 1');
        echo '
		Announcement added!<br />
		&gt; <a href="staff.php">Back</a>
   		';
        stafflog_add('Added a new announcement');
    }
    else
    {
        $csrf = request_csrf_html('staff_announcement');
        echo '
        Adding an announcement...
        <br />
        Please try to make sure the announcement is concise and covers everything you want it to.
        <form action="staff.php?action=announce" method="post">
        	Announcement text:<br />
        	<textarea name="text" rows="10" cols="60"></textarea>
        	<br />
        	' . $csrf
                . '
        	<input type="submit" value="Add Announcement" />
        </form>
           ';
    }
}

function index(): void
{
    global $db, $ir, $set, $_CONFIG;
    if ($ir['user_level'] == 2)
    {
        $versq = $db->query('SELECT VERSION()');
        $mv = $db->fetch_single($versq);
        $db->free_result($versq);
        $versionno = intval('20503');
        $version = '2.0.5b';
        echo "
        <h3>System Info</h3>
        <hr />
        <table width='75%' cellspacing='1' class='table'>
        		<tr>
        			<th>PHP Version:</th>
        			<td>" . phpversion()
                . "</td>
        		</tr>
        		<tr>
        			<th>MySQL Version:</th>
        			<td>$mv</td>
        		</tr>
        		<tr>
        			<th>MySQL Driver:</th>
        			<td>" . $_CONFIG['driver']
                . "</td>
        		</tr>
        		<tr>
        			<th>Codes Version:</th>
        			<td>$version (Build: $versionno)</td>
        		</tr>
        		<tr>
        			<th>Update Status:</th>
        			<td>
        				<iframe
        					src='https://www.mccodes.com/update_check.php?version={$versionno}'
        					width='250' height='30'></iframe>
        			</td>
        		</tr>
        </table>
        <hr />
        <h3>Last 20 Staff Actions</h3><hr />
        <table width='100%' cellspacing='1' class='table'>
        		<tr>
        			<th>Staff</th>
        			<th>Action</th>
        			<th>Time</th>
        			<th>IP</th>
        		</tr>
           ";
        $q =
                $db->query(
                    'SELECT `user`, `action`, `time`, `ip`, `username`
                         FROM `stafflog` AS `s`
                         INNER JOIN `users` AS `u`
                         ON `s`.`user` = `u`.`userid`
                         ORDER BY `s`.`time` DESC
                         LIMIT 20');
        while ($r = $db->fetch_row($q))
        {
            echo "
        	<tr>
        		<td>{$r['username']} [{$r['user']}]</td>
        		<td>{$r['action']}</td>
        		<td>" . date('F j Y g:i:s a', $r['time'])
                    . "</td>
        		<td>{$r['ip']}</td>
        	</tr>
           	";
        }
        $db->free_result($q);
        echo '</table><hr />';
    }
    echo '<h3>Staff Notepad</h3><hr />';
    if (isset($_POST['pad']))
    {
        staff_csrf_stdverify('staff_notepad', 'staff.php');
        $pad = $db->escape(stripslashes($_POST['pad']));
        $db->query(
                "UPDATE `settings`
                 SET `conf_value` = '{$pad}'
                 WHERE `conf_name` = 'staff_pad'");
        $set['staff_pad'] = stripslashes($_POST['pad']);
        echo '<b>Staff Notepad Updated!</b><hr />';
    }
    $csrf = request_csrf_html('staff_notepad');
    echo "
	<form action='staff.php' method='post'>
		<textarea rows='10' cols='60' name='pad'>"
            . htmlentities($set['staff_pad'], ENT_QUOTES, 'ISO-8859-1')
            . "</textarea>
		<br />
		{$csrf}
		<input type='submit' value='Update Notepad' />
	</form>
   	";
}
$h->endpage();
