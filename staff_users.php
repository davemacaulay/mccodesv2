<?php
declare(strict_types=1);
/**
 * MCCodes v2 by Dabomstew & ColdBlooded
 *
 * Repository: https://github.com/davemacaulay/mccodesv2
 * License: MIT License
 */

global $h;
require_once('sglobals.php');
//This contains user stuffs
if (!isset($_GET['action']))
{
    $_GET['action'] = '';
}
switch ($_GET['action'])
{
case 'newuser':
    new_user_form();
    break;
case 'newusersub':
    new_user_submit();
    break;
case 'edituser':
    edit_user_begin();
    break;
case 'edituserform':
    edit_user_form();
    break;
case 'editusersub':
    edit_user_sub();
    break;
case 'invbeg':
    inv_user_begin();
    break;
case 'invuser':
    inv_user_view();
    break;
case 'deleinv':
    inv_delete();
    break;
case 'creditform':
    credit_user_form();
    break;
case 'creditsub':
    credit_user_submit();
    break;
case 'masscredit':
    mcredit_user_form();
    break;
case 'masscreditsub':
    mcredit_user_submit();
    break;
case 'reportsview':
    reports_view();
    break;
case 'repclear':
    report_clear();
    break;
case 'deluser':
    deluser();
    break;
case 'forcelogout':
    forcelogout();
    break;
default:
    echo 'Error: This script requires an action.';
    break;
}

/**
 * @return void
 */
function new_user_form(): void
{
    global $h;
    if (!check_access('manage_users')) {
        echo 'You cannot access this area.
    <br />&gt; <a href="index.php">Go Home</a>';
        $h->endpage();
        exit;
    }
    $csrf = request_csrf_html('staff_newuser');
    echo "
    Adding a new user.
    <br />
    <form action='staff_users.php?action=newusersub' method='post'>
    	Username: <input type='text' name='username' />
    	<br />
    	Login Name: <input type='text' name='login_name' />
    	<br />
    	Email: <input type='text' name='email' />
    	<br />
    	Password: <input type='text' name='userpass' />
    	<br />
    	Type:
    		<input type='radio' name='user_level' value='0' />NPC
    		<input type='radio' name='user_level' value='1' checked='checked' />Regular Member
    	<br />
    	Level: <input type='text' name='level' value='1' />
    	<br />
    	Money: <input type='text' name='money' value='100' />
    	<br />
    	Crystals: <input type='text' name='crystals' value='0' />
    	<br />
    	Donator Days: <input type='text' name='donatordays' value='0' />
    	<br />
    	Gender:
    		<select name='gender' type='dropdown'>
    			<option>Male</option>
    			<option>Female</option>
    		</select>
    	<br />
    	<br />
    	<b>Stats</b>
    	<br />
    	Strength: <input type='text' name='strength' value='10' />
    	<br />
    	Agility: <input type='text' name='agility' value='10' />
    	<br />
    	Guard: <input type='text' name='guard' value='10' />
    	<br />
    	Labour: <input type='text' name='labour' value='10' />
    	<br />
    	IQ: <input type='text' name='iq' value='10' />
    	<br />
    	<br />
    	{$csrf}
    	<input type='submit' value='Create User' />
    </form>
        ";
}

/**
 * @return void
 */
function new_user_submit(): void
{
    global $db, $h;
    if (!check_access('manage_users')) {
        echo 'You cannot access this area.
    <br />&gt; <a href="index.php">Go Home</a>';
        $h->endpage();
        exit;
    }
    staff_csrf_stdverify('staff_newuser', 'staff_users.php?action=newuser');
    $_POST['email'] =
            (isset($_POST['email'])
                    && filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL))
                    ? $db->escape(stripslashes($_POST['email'])) : '';
    $ulevel =
            (isset($_POST['user_level'])
                    && in_array($_POST['user_level'], ['1', '0'], true))
                    ? $_POST['user_level'] : FALSE;
    $level =
            (isset($_POST['level']) && is_numeric($_POST['level']))
                    ? abs(intval($_POST['level'])) : 1;
    $money =
            (isset($_POST['money']) && is_numeric($_POST['money']))
                    ? abs(intval($_POST['money'])) : 100;
    $crystals =
            (isset($_POST['crystals']) && is_numeric($_POST['crystals']))
                    ? abs(intval($_POST['crystals'])) : 0;
    $donator =
            (isset($_POST['donatordays']) && is_numeric($_POST['donatordays']))
                    ? abs(intval($_POST['donatordays'])) : 0;
    $_POST['gender'] =
            (isset($_POST['gender'])
                    && in_array($_POST['gender'], ['Male', 'Female'],
                            true)) ? $_POST['gender'] : 'Male';
    $strength =
            (isset($_POST['strength']) && is_numeric($_POST['strength']))
                    ? abs(intval($_POST['strength'])) : 10;
    $agility =
            (isset($_POST['agility']) && is_numeric($_POST['agility']))
                    ? abs(intval($_POST['agility'])) : 10;
    $guard =
            (isset($_POST['guard']) && is_numeric($_POST['guard']))
                    ? abs(intval($_POST['guard'])) : 10;
    $labour =
            (isset($_POST['labour']) && is_numeric($_POST['labour']))
                    ? abs(intval($_POST['labour'])) : 10;
    $iq =
            (isset($_POST['iq']) && is_numeric($_POST['iq']))
                    ? abs(intval($_POST['iq'])) : 10;
    $_POST['username'] =
            (isset($_POST['username'])
                    && preg_match(
                            "/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])+$/i",
                            $_POST['username'])
                    && ((strlen($_POST['username']) < 32)
                            && (strlen($_POST['username']) >= 3)))
                    ? $db->escape(strip_tags(stripslashes($_POST['username'])))
                    : '';
    $_POST['login_name'] =
            (isset($_POST['login_name'])
                    && preg_match(
                            "/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])+$/i",
                            $_POST['login_name'])
                    && ((strlen($_POST['login_name']) < 32)
                            && (strlen($_POST['login_name']) >= 3)))
                    ? $db->escape(
                            strip_tags(stripslashes($_POST['login_name'])))
                    : '';
    $_POST['userpass'] =
            (isset($_POST['userpass'])
                    && (strlen(stripslashes($_POST['userpass'])) <= 32))
                    ? stripslashes($_POST['userpass']) : '';
    if (empty($_POST['username']) || empty($_POST['login_name'])
            || empty($_POST['userpass']) || is_bool($ulevel)
            || empty($_POST['email']) || empty($level))
    {
        echo '
        You missed one or more of the required fields. Please go back and try again.
        <br />
        &gt; <a href="staff_users.php?action=newuser">Go Back</a>
           ';
        $h->endpage();
        exit;
    }
    $ucnt =
            $db->query(
                    'SELECT COUNT(`userid`)
                     FROM `users`
                     WHERE `username` = "' . $_POST['username']
                            . '"
                     OR `login_name` = "' . $_POST['login_name'] . '"');
    if ($db->fetch_single($ucnt) > 0)
    {
        $db->free_result($ucnt);
        echo '
        Username/Login name already in use.
        <br />
        &gt; <a href="staff_users.php?action=newuser">GoBack</a>
           ';
        $h->endpage();
        exit;
    }
    $db->free_result($ucnt);
    $energy = 10 + $level * 2;
    $brave = 3 + $level * 2;
    $hp = 50 + $level * 50;
    $salt = generate_pass_salt();
    $e_salt = $db->escape($salt);
    $encpsw = encode_password($_POST['userpass'], $salt);
    $e_encpsw = $db->escape($encpsw);
    $db->query(
            "INSERT INTO `users`
             (`username`, `login_name`, `userpass`, `level`, `money`,
             `crystals`, `donatordays`, `user_level`, `energy`, `maxenergy`,
             `will`, `maxwill`, `brave`, `maxbrave`, `hp`, `maxhp`, `location`,
             `gender`,`signedup`, `email`, `bankmoney`, `pass_salt`)
             VALUES( '{$_POST['username']}', '{$_POST['login_name']}',
             '{$e_encpsw}', $level, $money, $crystals, $donator, $ulevel,
             $energy, $energy, 100, 100, $brave, $brave, $hp, $hp, 1,
             '{$_POST['gender']}', " . time()
                    . ", '{$_POST['email']}', -1, '{$e_salt}')");
    $i = $db->insert_id();
    $db->query(
            "INSERT INTO `userstats`
             VALUES($i, $strength, $agility, $guard, $labour, $iq)");
    stafflog_add('Created user ' . $_POST['username'] . ' [' . $i . ']');
    echo '
    User (' . $_POST['username']
            . ') created.<br />
    &gt; <a href="staff_users.php?action=newuser">Go Back</a>
       ';

}

/**
 * @return void
 */
function edit_user_begin(): void
{
    global $h;
    if (!check_access('manage_users')) {
        echo 'You cannot access this area.
    <br />&gt; <a href="index.php">Go Home</a>';
        $h->endpage();
        exit;
    }
    $csrf = request_csrf_html('staff_edituser1');
    echo "
    <h3>Editing User</h3>
    You can edit any aspect of this user.
    <br />
    <form action='staff_users.php?action=edituserform' method='post'>
    	User: " . user_dropdown()
            . "
    	<br />
    	{$csrf}
    	<input type='submit' value='Edit User' />
    </form>
    OR enter a user ID to edit:
    <form action='staff_users.php?action=edituserform' method='post'>
    	User: <input type='text' name='user' value='0' />
    	<br />
    	{$csrf}
    	<input type='submit' value='Edit User' />
    </form>
       ";
}

/**
 * @return void
 */
function edit_user_form(): void
{
    global $db, $h;
    if (!check_access('manage_users')) {
        echo 'You cannot access this area.
    <br />&gt; <a href="index.php">Go Home</a>';
        $h->endpage();
        exit;
    }
    staff_csrf_stdverify('staff_edituser1', 'staff_users.php?action=edituser');
    $_POST['user'] =
            (isset($_POST['user']) && is_numeric($_POST['user']))
                    ? abs(intval($_POST['user'])) : 0;
    if (empty($_POST['user']))
    {
        echo '
        Invalid user, Please go back and try again.
        <br />
        &gt; <a href="staff_users.php?action=edituser">Go Back</a>
           ';
        $h->endpage();
        exit;
    }
    $d =
            $db->query(
                    "SELECT `hospreason`, `jail_reason`, `username`,
                     `login_name`, `duties`, `level`, `money`, `cybermoney`,
                     `crystals`, `mailban`, `mb_reason`, `forumban`,
                     `fb_reason`, `hospital`, `jail`, `maxwill`, `bankmoney`,
                     `strength`, `agility`, `guard`, `labour`, `IQ`,
                     `staffnotes`
                     FROM `users` AS `u`
                     INNER JOIN `userstats` AS `us`
                     ON `u`.`userid` = `us`.`userid`
                     WHERE `u`.`userid` = {$_POST['user']}");
    if ($db->num_rows($d) == 0)
    {
        $db->free_result($d);
        echo '
        User doesn\'t seem to exist, Please go back and try again.
        <br />
        &gt; <a href="staff_users.php?action=edituser">Go Back</a>
           ';
        $h->endpage();
        exit;
    }
    $itemi = $db->fetch_row($d);
    $db->free_result($d);
    $itemi['hospreason'] =
            htmlentities($itemi['hospreason'], ENT_QUOTES, 'ISO-8859-1');
    $itemi['jail_reason'] =
            htmlentities($itemi['jail_reason'], ENT_QUOTES, 'ISO-8859-1');
    $itemi['username'] =
            htmlentities($itemi['username'], ENT_QUOTES, 'ISO-8859-1');
    $itemi['login_name'] =
            htmlentities($itemi['login_name'], ENT_QUOTES, 'ISO-8859-1');
    $itemi['duties'] =
            htmlentities($itemi['duties'], ENT_QUOTES, 'ISO-8859-1');
    $itemi['staffnotes'] =
            htmlentities($itemi['staffnotes'], ENT_QUOTES, 'ISO-8859-1');
    $itemi['mb_reason'] =
            htmlentities($itemi['mb_reason'], ENT_QUOTES, 'ISO-8859-1');
    $itemi['fb_reason'] =
            htmlentities($itemi['fb_reason'], ENT_QUOTES, 'ISO-8859-1');
    $csrf = request_csrf_html('staff_edituser2');
    echo "
    <h3>Editing User</h3>
    <form action='staff_users.php?action=editusersub' method='post'>
    	<input type='hidden' name='userid' value='{$_POST['user']}' />
    	Username: <input type='text' name='username' value='{$itemi['username']}' />
    	<br />
    	Login Name: <input type='text' name='login_name' value='{$itemi['login_name']}' />
    	<br />
    	Duties: <input type='text' name='duties' value='{$itemi['duties']}' />
    	<br />
    	Staff Notes: <input type='text' name='staffnotes' value='{$itemi['staffnotes']}' />
    	<br />
    	Level: <input type='text' name='level' value='{$itemi['level']}' />
    	<br />
    	Money: \$<input type='text' name='money' value='{$itemi['money']}' />
    	<br />
    	Bank: \$<input type='text' name='bankmoney' value='{$itemi['bankmoney']}' />
    	<br />
    	Cyber Bank: \$<input type='text' name='cybermoney' value='{$itemi['cybermoney']}' />
    	<br />
    	Crystals: <input type='text' name='crystals' value='{$itemi['crystals']}' />
    	<br />
    	Mail Ban: <input type='text' name='mailban' value='{$itemi['mailban']}' />
   		<br />
    	Mail Ban Reason: <input type='text' name='mb_reason' value='{$itemi['mb_reason']}' />
    	<br />
    	Forum Ban: <input type='text' name='forumban' value='{$itemi['forumban']}' />
    	<br />
    	Forum Ban Reason: <input type='text' name='fb_reason' value='{$itemi['fb_reason']}' />
    	<br />
    	Hospital time: <input type='text' name='hospital' value='{$itemi['hospital']}' />
    	<br />
    	Hospital reason: <input type='text' name='hospreason' value='{$itemi['hospreason']}' />
    	<br />
    	Jail time: <input type='text' name='jail' value='{$itemi['jail']}' />
    	<br />
    	Jail reason: <input type='text' name='jail_reason' value='{$itemi['jail_reason']}' />
    	<br />
    	House: " . house2_dropdown('maxwill', $itemi['maxwill'])
            . "
    	<br />
    	<h4>Stats</h4>
    	Strength: <input type='text' name='strength' value='{$itemi['strength']}' />
    	<br />
    	Agility: <input type='text' name='agility' value='{$itemi['agility']}' />
    	<br />
    	Guard: <input type='text' name='guard' value='{$itemi['guard']}' />
    	<br />
    	Labour: <input type='text' name='labour' value='{$itemi['labour']}' />
    	<br />
    	IQ: <input type='text' name='IQ' value='{$itemi['IQ']}' />
    	<br />
    	{$csrf}
    	<input type='submit' value='Edit User' />
    </form>
       ";
}

/**
 * @return void
 */
function edit_user_sub(): void
{
    global $db, $h;
    if (!check_access('manage_users')) {
        echo 'You cannot access this area.
    <br />&gt; <a href="index.php">Go Home</a>';
        $h->endpage();
        exit;
    }
    staff_csrf_stdverify('staff_edituser2', 'staff_users.php?action=edituser');
    $_POST['userid'] =
            (isset($_POST['userid']) && is_numeric($_POST['userid']))
                    ? abs(intval($_POST['userid'])) : 0;
    $_POST['username'] =
            (isset($_POST['username'])
                    && preg_match(
                            "/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])+$/i",
                            $_POST['username'])
                    && ((strlen($_POST['username']) < 32)
                            && (strlen($_POST['username']) >= 3)))
                    ? $db->escape(strip_tags(stripslashes($_POST['username'])))
                    : '';
    $_POST['login_name'] =
            (isset($_POST['login_name'])
                    && preg_match(
                            "/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])+$/i",
                            $_POST['login_name'])
                    && ((strlen($_POST['login_name']) < 32)
                            && (strlen($_POST['login_name']) >= 3)))
                    ? $db->escape(
                            strip_tags(stripslashes($_POST['login_name'])))
                    : '';
    $_POST['duties'] =
            (isset($_POST['duties']) && (strlen($_POST['duties']) <= 500))
                    ? $db->escape(strip_tags(stripslashes($_POST['duties'])))
                    : '';
    $_POST['staffnotes'] =
            (isset($_POST['staffnotes'])
                    && (strlen($_POST['staffnotes']) <= 500))
                    ? $db->escape(
                            strip_tags(stripslashes($_POST['staffnotes'])))
                    : '';
    $_POST['level'] =
            (isset($_POST['level']) && is_numeric($_POST['level']))
                    ? abs(intval($_POST['level'])) : 1;
    $_POST['money'] =
            (isset($_POST['money']) && is_numeric($_POST['money']))
                    ? abs(intval($_POST['money'])) : 100;
    $_POST['bankmoney'] =
            (isset($_POST['bankmoney']) && is_numeric($_POST['bankmoney']))
                    ? abs(intval($_POST['bankmoney'])) : 0;
    $_POST['cybermoney'] =
            (isset($_POST['cybermoney']) && is_numeric($_POST['cybermoney']))
                    ? abs(intval($_POST['cybermoney'])) : 0;
    $_POST['crystals'] =
            (isset($_POST['crystals']) && is_numeric($_POST['crystals']))
                    ? abs(intval($_POST['crystals'])) : 0;
    $_POST['mailban'] =
            (isset($_POST['mailban']) && is_numeric($_POST['mailban']))
                    ? abs(intval($_POST['mailban'])) : 0;
    $_POST['mb_reason'] =
            (isset($_POST['mb_reason'])
                    && (strlen($_POST['mb_reason']) <= 500))
                    ? $db->escape(
                            strip_tags(stripslashes($_POST['mb_reason']))) : '';
    $_POST['forumban'] =
            (isset($_POST['forumban']) && is_numeric($_POST['forumban']))
                    ? abs(intval($_POST['forumban'])) : 0;
    $_POST['fb_reason'] =
            (isset($_POST['fb_reason'])
                    && (strlen($_POST['fb_reason']) <= 500))
                    ? $db->escape(
                            strip_tags(stripslashes($_POST['fb_reason']))) : '';
    $_POST['hospital'] =
            (isset($_POST['hospital']) && is_numeric($_POST['hospital']))
                    ? abs(intval($_POST['hospital'])) : 0;
    $_POST['hospreason'] =
            (isset($_POST['hospreason'])
                    && (strlen($_POST['hospreason']) <= 500))
                    ? $db->escape(
                            strip_tags(stripslashes($_POST['hospreason'])))
                    : '';
    $_POST['jail'] =
            (isset($_POST['jail']) && is_numeric($_POST['jail']))
                    ? abs(intval($_POST['jail'])) : 0;
    $_POST['jail_reason'] =
            (isset($_POST['jail_reason'])
                    && (strlen($_POST['jail_reason']) <= 500))
                    ? $db->escape(
                            strip_tags(stripslashes($_POST['jail_reason'])))
                    : '';
    $maxwill =
            (isset($_POST['maxwill']) && is_numeric($_POST['maxwill']))
                    ? abs(intval($_POST['maxwill'])) : 1;
    $_POST['strength'] =
            (isset($_POST['strength']) && is_numeric($_POST['strength']))
                    ? abs(intval($_POST['strength'])) : 10;
    $_POST['agility'] =
            (isset($_POST['agility']) && is_numeric($_POST['agility']))
                    ? abs(intval($_POST['agility'])) : 10;
    $_POST['guard'] =
            (isset($_POST['guard']) && is_numeric($_POST['guard']))
                    ? abs(intval($_POST['guard'])) : 10;
    $_POST['labour'] =
            (isset($_POST['labour']) && is_numeric($_POST['labour']))
                    ? abs(intval($_POST['labour'])) : 10;
    $_POST['IQ'] =
            (isset($_POST['IQ']) && is_numeric($_POST['IQ']))
                    ? abs(intval($_POST['IQ'])) : 10;
    if (empty($_POST['username']) || empty($_POST['login_name'])
            || empty($_POST['userid']) || empty($maxwill)
            || empty($_POST['level']))
    {
        echo '
        You missed one or more of the required fields, Please go back and try again.
        <br />
        &gt; <a href="staff_users.php?action=edituser">Go Back</a>
           ';
        $h->endpage();
        exit;
    }
    $u_exists =
            $db->query(
                    'SELECT `will`
                     FROM `users`
                     WHERE `userid` = ' . $_POST['userid']);
    if ($db->num_rows($u_exists) == 0)
    {
        $db->free_result($u_exists);
        echo '
        User doesn\'t seem to exist, Please go back and try again.
        <br />
        &gt; <a href="staff_users.php?action=edituser">Go Back</a>
           ';
        $h->endpage();
        exit;
    }
    $h_exists =
            $db->query(
                    'SELECT COUNT(`hID`)
                     FROM `houses`
                     WHERE `hWILL` = ' . $maxwill);
    if ($db->fetch_single($h_exists) == 0)
    {
        $db->free_result($h_exists);
        echo '
        House doesn\'t seem to exist, Please go back and try again.
        <br />
        &gt; <a href="staff_users.php?action=edituser">Go Back</a>
           ';
        $h->endpage();
        exit;
    }
    $db->free_result($h_exists);
    $u =
            $db->query(
                    "SELECT COUNT(`userid`)
                     FROM `users`
                     WHERE `username` = '{$_POST['username']}'
                     AND `userid` != {$_POST['userid']}");
    if ($db->fetch_single($u) != 0)
    {
        $db->free_result($u);
        echo '
        That username is in use, choose another.
        <br />
        &gt; <a href="staff_users.php?action=edituser">Go Back</a>
           ';
        $h->endpage();
        exit;
    }
    $db->free_result($u);
    $oldwill = $db->fetch_single($u_exists);
    $db->free_result($u_exists);
    $will = ($oldwill > $maxwill) ? $maxwill : $oldwill;
    $energy = 10 + $_POST['level'] * 2;
    $nerve = 3 + $_POST['level'] * 2;
    $hp = 50 + $_POST['level'] * 50;
    $db->query(
            "UPDATE `users`
             SET `username` = '{$_POST['username']}',
             `level` = {$_POST['level']}, `money` = {$_POST['money']},
             `crystals` = {$_POST['crystals']}, `energy` = $energy,
             `brave` = $nerve, `maxbrave` = $nerve, `maxenergy` = $energy,
             `hp` = $hp, `maxhp` = $hp, `hospital` = {$_POST['hospital']},
             `jail` = {$_POST['jail']}, `duties` = '{$_POST['duties']}',
             `staffnotes` = '{$_POST['staffnotes']}',
             `mailban` = {$_POST['mailban']},
             `mb_reason` = '{$_POST['mb_reason']}',
             `forumban` = {$_POST['forumban']},
             `fb_reason` = '{$_POST['fb_reason']}',
             `hospreason` = '{$_POST['hospreason']}',
             `jail_reason` = '{$_POST['jail_reason']}',
             `login_name` = '{$_POST['login_name']}',
             `will` = $will, `maxwill` = $maxwill
    		 WHERE `userid` = {$_POST['userid']}");
    $db->query(
            "UPDATE `userstats`
             SET `strength` = {$_POST['strength']},
             `agility` = {$_POST['agility']}, `guard` = {$_POST['guard']},
             `labour` = {$_POST['labour']}, `IQ` = {$_POST['IQ']}
             WHERE `userid` = {$_POST['userid']}");
    stafflog_add(
            'Edited user ' . $_POST['username'] . ' [' . $_POST['userid']
                    . ']');
    echo '
    User edited.
    <br />
    &gt; <a href="staff.php">Go Home</a>
       ';
    $h->endpage();
    exit;

}

/**
 * @return void
 */
function deluser(): void
{
    global $ir, $h, $db;
    if (!check_access('manage_users')) {
        echo 'You cannot access this area.
    <br />&gt; <a href="index.php">Go Home</a>';
        $h->endpage();
        exit;
    }
    if (!isset($_GET['step']))
    {
        $_GET['step'] = '0';
    }
    switch ($_GET['step'])
    {
    default:
        $csrf = request_csrf_html('staff_deluser1');
        echo '
        <h3>Deleting User</h3>
        Here you can delete a user.
        <br />
        <form action="staff_users.php?action=deluser&amp;step=2" method="post">
        	User: ' . user_dropdown()
                . '
        <br />
        ' . $csrf
                . '
        	<input type="submit" value="Delete User" />
        </form>
        OR enter a user ID to Delete:
        <form action="staff_users.php?action=deluser&amp;step=2" method="post">
        	User: <input type="text" name="user" value="0" />
        <br />
        ' . $csrf
                . '
        	<input type="submit" value="Delete User" />
        </form>
   		';
        break;
    case 2:
        $_POST['user'] =
                (isset($_POST['user']) && is_numeric($_POST['user']))
                        ? abs(intval($_POST['user'])) : 0;
        staff_csrf_stdverify('staff_deluser1',
                'staff_users.php?action=deluser');
        if (empty($_POST['user']) || $_POST['user'] == 1
                || $_POST['user'] == $ir['userid'])
        {
            echo '
            Invalid user, Please go back and try again.
            <br />
            &gt; <a href="staff_users.php?action=deluser">Go Back</a>
               ';
            $h->endpage();
            exit;
        }

        $d =
                $db->query(
                        'SELECT `username`
                         FROM `users`
                         WHERE `userid` = ' . $_POST['user']);
        if ($db->num_rows($d) == 0)
        {
            $db->free_result($d);
            echo '
            User doesn\'t seem to exist, Please go back and try again.
            <br />
            &gt; <a href="staff_users.php?action=deluser">Go Back</a>
               ';
            $h->endpage();
            exit;
        }
        $username =
                htmlentities($db->fetch_single($d), ENT_QUOTES, 'ISO-8859-1');
        $db->free_result($d);
        $csrf = request_csrf_html('staff_deluser2');
        echo "
        <h3>Confirm</h3>
        Delete user {$username}?
        <form action='staff_users.php?action=deluser&amp;step=3' method='post'>
        	<input type='hidden' name='userid' value='{$_POST['user']}' />
        	{$csrf}
        	<input type='submit' name='yesorno' value='Yes' />
        	<input type='submit' name='yesorno' value='No'
        	 onclick=\"window.location='staff_users.php?action=deluser';\" />
        </form>
           ";
        break;
    case 3:
        staff_csrf_stdverify('staff_deluser2',
                'staff_users.php?action=deluser');
        $_POST['userid'] =
                (isset($_POST['userid']) && is_numeric($_POST['userid']))
                        ? abs(intval($_POST['userid'])) : 0;
        $_POST['yesorno'] =
                (isset($_POST['yesorno'])
                        && in_array($_POST['yesorno'], ['Yes', 'No']))
                        ? $_POST['yesorno'] : '';
        if ((empty($_POST['userid']) || empty($_POST['yesorno']))
                || $_POST['userid'] == 1 || $_POST['userid'] == $ir['userid'])
        {
            echo '
            Invalid user/command, Please go back and try again.
            <br />
            &gt; <a href="staff_users.php?action=deluser">Go Back</a>
               ';
            $h->endpage();
            exit;
        }
        if ($_POST['yesorno'] == 'No')
        {
            echo '
            User not deleted.
            <br />
            &gt; <a href="staff_users.php?action=deluser">Go Back</a>
               ';
            $h->endpage();
            exit;
        }
        $d =
                $db->query(
                        'SELECT `username`
                         FROM `users`
                         WHERE `userid` = ' . $_POST['userid']);
        if ($db->num_rows($d) == 0)
        {
            echo '
            User doesn\'t seem to exist, Please go back and try again.
            <br />
            &gt; <a href="staff_users.php?action=deluser">Go Back</a>
               ';
            $h->endpage();
            exit;
        }
        $username =
                htmlentities($db->fetch_single($d), ENT_QUOTES, 'ISO-8859-1');
        $db->query(
                'DELETE FROM `users`
         		 WHERE `userid` = ' . $_POST['userid']);
        $db->query(
                'DELETE FROM `userstats`
                 WHERE `userid` = ' . $_POST['userid']);
        $db->query(
                'DELETE FROM `inventory`
                 WHERE `inv_userid` = ' . $_POST['userid']);
        $db->query(
                'DELETE FROM `fedjail`
                 WHERE `fed_userid` = ' . $_POST['userid']);
        stafflog_add(
                'Deleted User ' . $username . ' [' . $_POST['userid'] . ']');
        echo 'User ' . $username
                . ' Deleted.
		<br />
		&gt; <a href="staff.php">Go Home</a>
   		';
        $h->endpage();
        exit;
    }
}

/**
 * @return void
 */
function inv_user_begin(): void
{
    global $ir, $h;
    if (!check_access('manage_users')) {
        echo 'You cannot access this area.
    <br />&gt; <a href="index.php">Go Home</a>';
        $h->endpage();
        exit;
    }
    $csrf = request_csrf_html('staff_viewinv');
    echo "
    <h3>Viewing User Inventory</h3>
    You may browse this user's inventory.
    <br />
    <form action='staff_users.php?action=invuser' method='post'>
    	User: " . user_dropdown()
            . "
    	<br />
    	{$csrf}
    	<input type='submit' value='View Inventory' />
    </form>
       ";
}

/**
 * @return void
 */
function inv_user_view(): void
{
    global $db, $ir, $h;
    if (!check_access('view_user_inventory')) {
        echo 'You cannot access this area.
    <br />&gt; <a href="index.php">Go Home</a>';
        $h->endpage();
        exit;
    }
    staff_csrf_stdverify('staff_viewinv', 'staff_users.php?action=invbeg');
    $_POST['user'] =
            (isset($_POST['user']) && is_numeric($_POST['user']))
                    ? abs(intval($_POST['user'])) : 0;
    if (empty($_POST['user']))
    {
        echo '
        Invalid user, Please go back and try again.
        <br />
        &gt; <a href="staff_users.php?action=invbeg">Go Back</a>
           ';
        $h->endpage();
        exit;
    }
    $d =
            $db->query(
                    'SELECT `username`
                     FROM `users`
                     WHERE `userid` = ' . $_POST['user']);
    if ($db->num_rows($d) == 0)
    {
        echo '
        User doesn\'t seem to exist, Please go back and try again.
        <br />
        &gt; <a href="staff_users.php?action=invbeg">Go Back</a>
           ';
        $h->endpage();
        exit;
    }
    $inv =
            $db->query(
                    'SELECT `inv_qty`, `inv_id`, `itmname`, `itmsellprice`
                     FROM `inventory` AS `iv`
                     INNER JOIN `items` AS `i`
                     ON `iv`.`inv_itemid` = `i`.`itmid`
                     WHERE `iv`.`inv_userid` = ' . $_POST['user']);
    if ($db->num_rows($inv) == 0)
    {
        echo '<b>This person has no items!</b>';
    }
    else
    {
        echo '
        <b>Their items are listed below.</b><br />
        <table width="100%" class="table" cellpadding="1" cellspacing="1">
        		<tr>
        			<th>Item</th>
        			<th>Sell Value</th>
        			<th>Total Sell Value</th>
        			<th>Links</th>
        		</tr>
           ';
        $csrf = request_csrf_html('staff_deleinv');
        while ($i = $db->fetch_row($inv))
        {
            echo '
			<tr>
            	<td>' . $i['itmname'] . ' '
                    . (($i['inv_qty'] > 1) ? '&nbsp;x' . $i['inv_qty'] : '')
                    . '</td>
            	<td>' . money_formatter((int)$i['itmsellprice'])
                    . '</td>
            	<td>' . money_formatter((int)($i['itmsellprice'] * $i['inv_qty']))
                    . '</td>
            	<td>
            		<form action="staff_users.php?action=deleinv" method="post">
            			<input type="hidden" name="ID" value="' . $i['inv_id']
                    . '" />
                        ' . $csrf
                    . '
                        <input type="submit" value="Delete" />
                    </form>
                </td>
            </tr>
   			';
        }
        echo '</table>';
    }
    $db->free_result($inv);
    $un = htmlentities($db->fetch_single($d), ENT_QUOTES, 'ISO-8859-1');
    stafflog_add('.Viewed user ' . $un . ' [' . $_POST['user'] . '] inventory');
}

/**
 * @return void
 */
function inv_delete(): void
{
    global $db, $h;
    if (!check_access('manage_user_inventory'))
    {
        echo 'You cannot access this area.<br />
        &gt; <a href="staff.php">Go Back</a>';
        $h->endpage();
        exit;
    }
    staff_csrf_stdverify('staff_deleinv', 'staff_users.php?action=invbeg');
    $_POST['ID'] =
            (isset($_POST['ID']) && is_numeric($_POST['ID']))
                    ? abs(intval($_POST['ID'])) : 0;
    if (empty($_POST['ID']))
    {
        echo '
        Invalid item, Please go back and try again.
        <br />
        &gt; <a href="staff_users.php?action=invbeg">Go Back</a>
           ';
        $h->endpage();
        exit;
    }
    $d =
            $db->query(
                    'SELECT COUNT(`inv_id`)
                     FROM `inventory`
                     WHERE `inv_id` = ' . $_POST['ID']);
    if ($db->fetch_single($d) == 0)
    {
        $db->free_result($d);
        echo '
		Item doesn\'t seem to exist, Please go back and try again.
		<br />
		&gt; <a href="staff_users.php?action=invbeg">Go Back</a>
   		';
        $h->endpage();
        exit;
    }
    $db->free_result($d);
    $db->query(
            'DELETE FROM `inventory`
    		 WHERE `inv_id` = ' . $_POST['ID']);
    stafflog_add('Deleted inventory ID ' . $_POST['ID']);
    echo '
	Item deleted from inventory.
	<br />
	&gt; <a href="staff.php">Go Home</a>
  	 ';
}

/**
 * @return void
 */
function credit_user_form(): void
{
    global $h;
    if (!check_access('credit_user'))
    {
        echo 'You cannot access this area.<br />&gt; <a href="staff.php">Go Back</a>';
        $h->endpage();
        exit;
    }
    $csrf = request_csrf_html('staff_credituser');
    echo "
    <h3>Crediting User</h3>
    You can give a user money/crystals.
    <br />
    <form action='staff_users.php?action=creditsub' method='post'>
    	User: " . user_dropdown()
            . "
    	<br />
    	Money: <input type='text' name='money' value='0' />
    	<br />
    	Crystals: <input type='text' name='crystals' value='0' />
    	<br />
    	{$csrf}
    	<input type='submit' value='Credit User' />
    </form>
   ";
}

/**
 * @return void
 */
function credit_user_submit(): void
{
    global $db, $h;
    if (!check_access('credit_user'))
    {
        echo 'You cannot access this area.<br />
        &gt; <a href="staff.php">Go Back</a>';
        $h->endpage();
        exit;
    }
    staff_csrf_stdverify('staff_credituser',
            'staff_users.php?action=creditform');
    $_POST['user'] =
            (isset($_POST['user']) && is_numeric($_POST['user']))
                    ? abs(intval($_POST['user'])) : 0;
    $_POST['money'] =
            (isset($_POST['money']) && is_numeric($_POST['money']))
                    ? abs(intval($_POST['money'])) : 0;
    $_POST['crystals'] =
            (isset($_POST['crystals']) && is_numeric($_POST['crystals']))
                    ? abs(intval($_POST['crystals'])) : 0;
    if ((empty($_POST['money']) && empty($_POST['crystals']))
            || empty($_POST['user']))
    {
        echo '
        Something went horribly wrong, please go back and try again.
        <br />
        &gt; <a href="staff_users.php?action=creditform">Go Back</a>
           ';
        $h->endpage();
        exit;
    }
    $d =
            $db->query(
                    'SELECT `username`
                     FROM `users`
                     WHERE `userid` = ' . $_POST['user']);
    if ($db->num_rows($d) == 0)
    {
        echo '
        User doesn\'t seem to exist, Please go back and try again.
        <br />
        &gt; <a href="staff_users.php?action=creditform">Go Back</a>
           ';
        $h->endpage();
        exit;
    }
    $db->query(
            "UPDATE `users`
             SET `money` = `money` + {$_POST['money']},
             `crystals` = `crystals` + {$_POST['crystals']}
             WHERE `userid` = {$_POST['user']}");
    $un = htmlentities($db->fetch_single($d), ENT_QUOTES, 'ISO-8859-1');
    stafflog_add(
            'Credited ' . $un . ' [' . $_POST['user'] . '] '
                    . money_formatter($_POST['money']) . ' and/or '
                    . number_format($_POST['crystals']) . ' crystals.');
    echo $un . ' [' . $_POST['user'] . '] was credited with '
            . money_formatter($_POST['money']) . ' and/or '
            . number_format($_POST['crystals'])
            . ' crystals.
	<br />
	&gt; <a href="staff.php">Go Back</a>
   	';
}

/**
 * @return void
 */
function mcredit_user_form(): void
{
    global $h;
    if (!check_access('credit_all_users'))
    {
        echo 'You cannot access this area.<br />&gt; <a href="staff.php">Go Back</a>';
        $h->endpage();
        exit;
    }
    $csrf = request_csrf_html('staff_masscredit');
    echo "
    <h3>Mass Payment</h3>
    You can give all users money/crystals.
    <br />
    <form action='staff_users.php?action=masscreditsub' method='post'>
    	Money: <input type='text' name='money' value='0' />
    	<br />
    	Crystals: <input type='text' name='crystals' value='0' />
    	<br />
    	{$csrf}
    	<input type='submit' value='Credit User' />
    </form>
       ";
}

/**
 * @return void
 */
function mcredit_user_submit(): void
{
    global $db, $h;
    if (!check_access('credit_all_users'))
    {
        echo 'You cannot access this area.<br />&gt; <a href="staff.php">Go Back</a>';
        $h->endpage();
        exit;
    }
    staff_csrf_stdverify('staff_masscredit',
            'staff_users.php?action=masscredit');
    $_POST['money'] =
            (isset($_POST['money']) && is_numeric($_POST['money']))
                    ? abs(intval($_POST['money'])) : 0;
    $_POST['crystals'] =
            (isset($_POST['crystals']) && is_numeric($_POST['crystals']))
                    ? abs(intval($_POST['crystals'])) : 0;
    if (empty($_POST['money']) && empty($_POST['crystals']))
    {
        echo '
        Something went horribly wrong, please go back and try again.
        <br />
        &gt; <a href="staff_users.php?action=masscredit">Go Back</a>
           ';
        $h->endpage();
        exit;
    }
    $db->query(
            "UPDATE `users`
             SET `money` = `money` + {$_POST['money']},
             `crystals` = `crystals` + {$_POST['crystals']}");
    stafflog_add(
            'Credited all users ' . money_formatter($_POST['money'])
                    . ' and/or ' . number_format($_POST['crystals'])
                    . ' crystals.');
    echo "
	All Users credited.
	Click <a href='staff.php?action=announce'>here to add an announcement</a> or
	<a href='staff_special.php?action=massmailer'>here to send a mass mail</a>
	explaining why.
   	";
    $h->endpage();
    exit;
}

/**
 * @return void
 */
function reports_view(): void
{
    global $db, $h;
    if (!check_access('manage_player_reports'))
    {
        echo 'You cannot access this area.<br />
        &gt; <a href="staff.php">Go Back</a>';
        $h->endpage();
        exit;
    }
    echo "
    <h3>Player Reports</h3>
    <table width='80%' cellspacing='1' cellpadding='1' class='table'>
    		<tr>
    			<th>Reporter</th>
    			<th>Offender</th>
    			<th>What they did</th>
    			<th>&nbsp;</th>
    		</tr>
   	";
    $csrf = request_csrf_html('staff_clear_preport');
    $q =
            $db->query(
                'SELECT `prID`, `prTEXT`, `prREPORTED`, `prREPORTER`,
                    `u1`.`username` AS `reporter`,
                    `u2`.`username` AS `offender`
                     FROM `preports` AS `pr`
                     INNER JOIN `users` AS `u1`
                     ON `u1`.`userid` = `pr`.`prREPORTER`
                     INNER JOIN `users` AS `u2`
                     ON `u2`.`userid` = `pr`.`prREPORTED`
                     ORDER BY `pr`.`prID` DESC');
    while ($r = $db->fetch_row($q))
    {
        echo "
		<tr>
			<td>
				<a href='viewuser.php?u={$r['prREPORTER']}'>{$r['reporter']}</a>
				[{$r['prREPORTER']}]
			</td>
			<td>
				<a href='viewuser.php?u={$r['prREPORTED']}'>{$r['offender']}</a>
				[{$r['prREPORTED']}]
			</td>
			<td>" . htmlentities($r['prTEXT'], ENT_QUOTES, 'ISO-8859-1')
                . "</td>
			<td>
				<form action='staff_users.php?action=repclear'>
					<input type='hidden' name='ID' value='{$r['prID']}' />
	                {$csrf}
					<input type='submit' value='Clear' />
				</form>
			</td>
		</tr>
   		";
    }
    $db->free_result($q);
    echo '</table>';
}

/**
 * @return void
 */
function forcelogout(): void
{
    global $db, $ir, $h;
    if ($ir['user_level'] != 2)
    {
        echo 'You cannot access this area.<br />
        &gt; <a href="staff.php">Go Back</a>';
        $h->endpage();
        exit;
    }
    $_POST['userid'] =
            (isset($_POST['userid']) && is_numeric($_POST['userid']))
                    ? abs(intval($_POST['userid'])) : 0;
    if (!empty($_POST['userid']))
    {
        staff_csrf_stdverify('staff_forcelogout',
                'staff_users.php?action=forcelogout');
        $d =
                $db->query(
                        'SELECT COUNT(`userid`)
                         FROM `users`
                         WHERE `userid` = ' . $_POST['userid']);
        if ($db->fetch_single($d) == 0)
        {
            $db->free_result($d);
            echo '
            User doesn\'t seem to exist, Please go back and try again.
            <br />
            &gt; <a href="staff_users.php?action=forcelogout">Go Back</a>
               ';
            $h->endpage();
            exit;
        }
        $db->free_result($d);
        $db->query(
                'UPDATE `users`
                 SET `force_logout` = 1
                 WHERE `userid` = ' . $_POST['userid']);
        stafflog_add('Forced User ID ' . $_POST['userid'] . ' to logout');
        echo '
        User ID ' . $_POST['userid']
                . ' successfully forced to logout.
        <br />
        &gt; <a href="staff.php">Go Home</a>
           ';
    }
    else
    {
        $csrf = request_csrf_html('staff_forcelogout');
        echo "
        <h3>Force User Logout</h3>
        <hr />
        The user will be automatically logged out next time they make a hit to the site.
        <form action='staff_users.php?action=forcelogout' method='post'>
        	User: " . user_dropdown('userid')
                . "
        	<br />
        	{$csrf}
        	<input type='submit' value='Force User to Logout' />
        </form>
           ";
    }
}

/**
 * @return void
 */
function report_clear(): void
{
    global $db, $ir, $h;
    if (!in_array($ir['user_level'], [2, 3]))
    {
        echo 'You cannot access this area.<br />
        &gt; <a href="staff.php">Go Back</a>';
        $h->endpage();
        exit;
    }
    staff_csrf_stdverify('staff_clear_preport',
            'staff_users.php?action=reportsview');
    $_POST['ID'] =
            (isset($_POST['ID']) && is_numeric($_POST['ID']))
                    ? abs(intval($_POST['ID'])) : 0;
    if (empty($_POST['ID']))
    {
        echo '
        Invalid ID, please go back and try again.
        <br />
        &gt; <a href="staff_users.php?action=reportsview">Go Back</a>
           ';
        $h->endpage();
        exit;
    }
    $d =
            $db->query(
                    'SELECT COUNT(`prID`)
                     FROM `preports`
                     WHERE `prID` = ' . $_POST['ID']);
    if ($db->fetch_single($d) == 0)
    {
        $db->free_result($d);
        echo '
		Report doesn\'t seem to exist, Please go back and try again.
		<br />
		&gt; <a href="staff_users.php?action=reportsview">Go Back</a>
   		';
        $h->endpage();
        exit;
    }
    $db->free_result($d);
    $db->query(
            'DELETE FROM `preports`
    		    WHERE `prID` = ' . $_POST['ID']);
    stafflog_add('Cleared player report ID ' . $_POST['ID']);
    echo '
	Report deleted.
	<br />
	&gt; <a href="staff_users.php?action=reportsview">Go Back</a>
   	';
    $h->endpage();
    exit;
}
$h->endpage();
