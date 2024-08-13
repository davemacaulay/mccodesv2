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
if (!check_access('manage_challenge_bots')) {
    echo 'You cannot access this area.
    <br />&gt; <a href="index.php">Go Home</a>';
    $h->endpage();
    exit;
}
if (!isset($_GET['action']))
{
    $_GET['action'] = '';
}
switch ($_GET['action'])
{
case 'addbot':
    addbot();
    break;
case 'editbot':
    editbot();
    break;
case 'delbot':
    delbot();
    break;
default:
    echo 'Error: This script requires an action.';
    break;
}

/**
 * @return void
 */
function addbot(): void
{
    global $db, $h;
    $_POST['userid'] =
            (isset($_POST['userid']) && is_numeric($_POST['userid']))
                    ? abs(intval($_POST['userid'])) : '';
    $_POST['money'] =
            (isset($_POST['money']) && is_numeric($_POST['money']))
                    ? abs(intval($_POST['money'])) : '';
    if ($_POST['userid'] && $_POST['money'])
    {
        staff_csrf_stdverify('staff_addbot',
                'staff_battletent.php?action=addbot');
        $q =
                $db->query(
                        "SELECT `user_level`, `userid`, `username`
                         FROM `users`
                         WHERE `userid` = {$_POST['userid']}");
        if ($db->num_rows($q) == 0)
        {
            $db->free_result($q);
            echo 'Non-existant user.<br />
            &gt; <a href="staff.php">Goto Main</a>';
            $h->endpage();
            exit;
        }
        $r = $db->fetch_row($q);
        $db->free_result($q);
        if ($r['user_level'] != 0)
        {
            echo 'Challenge bots must be NPCs.<br />
            &gt; <a href="staff.php">Goto Main</a>';
            $h->endpage();
            exit;
        }
        $q2 =
                $db->query(
                        "SELECT COUNT(`cb_npcid`)
                         FROM `challengebots`
                         WHERE `cb_npcid` = {$r['userid']}");
        if ($db->fetch_single($q2) > 0)
        {
            $db->free_result($q2);
            echo 'This user is already a Challenge Bot. If you wish to change the payout, edit the Challenge Bot.<br />&gt; <a href="staff.php">Goto Main</a>';
            $h->endpage();
            exit;
        }
        $db->free_result($q2);
        $db->query(
                "INSERT INTO `challengebots`
                 VALUES('{$r['userid']}', '{$_POST['money']}')");
        echo 'Challenge Bot ' . $r['username']
                . ' added.<br />
                &gt; <a href="staff.php">Goto Main</a>';
        stafflog_add("Added Challenge Bot {$r['username']}.");
    }
    else
    {
        $csrf = request_csrf_html('staff_addbot');
        echo "
        <h3>Adding a Battle Tent Challenge Bot</h3>
        <hr />
        <form action='staff_battletent.php?action=addbot' method='post'>
        	Bot: " . user_dropdown('userid')
                . "
        	<br />
        	Bounty for Beating: <input type='text' name='money' />
        	<br />
        	{$csrf}
        	<input type='submit' value='Add Challenge Bot' />
        </form>
   		";
    }
}

/**
 * @return void
 */
function editbot(): void
{
    global $db, $h;
    $_GET['step'] =
            (isset($_GET['step']) && in_array($_GET['step'], [1, 2, 3]))
                    ? abs(intval($_GET['step'])) : '';
    switch ($_GET['step'])
    {
    case '2':
        $_POST['userid'] =
                (isset($_POST['userid']) && is_numeric($_POST['userid']))
                        ? abs(intval($_POST['userid'])) : '';
        $_POST['money'] =
                (isset($_POST['money']) && is_numeric($_POST['money']))
                        ? abs(intval($_POST['money'])) : '';
        if (empty($_POST['userid']) || empty($_POST['money']))
        {
            echo 'Something went wrong.<br />&gt; <a href="staff.php">Goto Main</a>';
            $h->endpage();
            exit;
        }
        staff_csrf_stdverify('staff_editbot_2',
                'staff_battletent.php?action=editbot');
        $q =
                $db->query(
                        "SELECT `username`,`userid`
                         FROM `users`
                         WHERE `userid` = {$_POST['userid']}");
        if ($db->num_rows($q) == 0)
        {
            $db->free_result($q);
            echo 'Non-existing user.<br />
            &gt; <a href="staff.php">Goto Main</a>';
            $h->endpage();
            exit;
        }
        $r = $db->fetch_row($q);
        $db->free_result($q);
        $q2 =
                $db->query(
                        "SELECT COUNT(`cb_npcid`)
                         FROM `challengebots`
                         WHERE `cb_npcid` = {$r['userid']}");
        if ($db->fetch_single($q2) == 0)
        {
            $db->free_result($q2);
            echo 'This user is not a Challenge Bot.<br />&gt; <a href="staff.php">Goto Main</a>';
            $h->endpage();
            exit;
        }
        $db->free_result($q2);
        $db->query(
                "UPDATE `challengebots`
                 SET `cb_money` = {$_POST['money']}
                 WHERE `cb_npcid` = {$r['userid']}");
        echo 'Challenge Bot ' . $r['username']
                . ' was updated.<br />&gt; <a href="staff.php">Goto Main</a>';
        stafflog_add("Edited Challenge Bot {$r['username']}.");
        break;
    case '1':
        $_POST['userid'] =
                (isset($_POST['userid']) && is_numeric($_POST['userid']))
                        ? abs(intval($_POST['userid'])) : '';
        if (empty($_POST['userid']))
        {
            echo 'Something went wrong.<br />
            &gt; <a href="staff.php">Goto Main</a>';
            $h->endpage();
            exit;
        }
        staff_csrf_stdverify('staff_editbot_1',
                'staff_battletent.php?action=editbot');
        $q =
                $db->query(
                        "SELECT `userid`, `username`
                         FROM `users`
                         WHERE `userid` = {$_POST['userid']}");
        if ($db->num_rows($q) == 0)
        {
            echo 'Non-existant user.<br />
            &gt; <a href="staff.php">Goto Main</a>';
            $h->endpage();
            exit;
        }
        $r = $db->fetch_row($q);
        $db->free_result($q);
        $q2 =
                $db->query(
                        "SELECT `cb_money`
                         FROM `challengebots`
                         WHERE `cb_npcid` = {$r['userid']}");
        if (!$db->num_rows($q2))
        {
            $db->free_result($q2);
            echo 'This user is not a Challenge Bot.<br />
            &gt; <a href="staff.php">Goto Main</a>';
            $h->endpage();
            exit;
        }
        $mn = $db->fetch_single($q2);
        $db->free_result($q2);
        $csrf = request_csrf_html('staff_editbot_2');
        echo "
        <h3>Edit Challenge Bot</h3>
        <hr />
        You are editing the challenge bot: <b>{$r['username']}</b>
        <form action='staff_battletent.php?action=editbot&amp;step=2' method='post'>
        	Bounty for Beating: <input type='text' name='money' value='{$mn}' />
        	<br />
        	<input type='hidden' name='userid' value='{$r['userid']}' />
        	{$csrf}
        	<input type='submit' value='Edit Challenge Bot' />
        </form>
   		";
        break;
    default:
        $csrf = request_csrf_html('staff_editbot_1');
        echo "
        <h3>Edit Challenge Bot</h3>
        <hr />
        <form action='staff_battletent.php?action=editbot&amp;step=1' method='post'>
        	Bot: " . challengebot_dropdown('userid')
                . "
        	<br />
        	{$csrf}
        	<input type='submit' value='Edit Challenge Bot' />
        </form>
   		";
        break;
    }
}

/**
 * @return void
 */
function delbot(): void
{
    global $db, $h;
    $_POST['userid'] =
            (isset($_POST['userid']) && is_numeric($_POST['userid']))
                    ? abs(intval($_POST['userid'])) : '';
    $_POST['delcb'] =
            (isset($_POST['delcb']) && $_POST['delcb'] == 'Yes')
                    ? $_POST['delcb'] : '';
    if (!empty($_POST['userid']))
    {
        staff_csrf_stdverify('staff_delbot',
                'staff_battletent.php?action=delbot');
        $q =
                $db->query(
                        "SELECT `username`, `userid`
                         FROM `users`
                         WHERE `userid` = {$_POST['userid']}");
        if ($db->num_rows($q) == 0)
        {
            $db->free_result($q);
            echo 'Non-existant user.<br />
            &gt; <a href="staff.php">Goto Main</a>';
            $h->endpage();
            exit;
        }
        $r = $db->fetch_row($q);
        $db->free_result($q);
        $q2 =
                $db->query(
                        "SELECT COUNT(`cb_npcid`)
                         FROM `challengebots`
                         WHERE `cb_npcid` = {$r['userid']}");
        if ($db->fetch_single($q2) == 0)
        {
            $db->free_result($q2);
            echo 'This user is not a Challenge Bot.<br />
            &gt; <a href="staff.php">Goto Main</a>';
            $h->endpage();
            exit;
        }
        $db->free_result($q2);
        $db->query(
                "DELETE FROM `challengebots`
                 WHERE `cb_npcid` = {$r['userid']}");
        if ($_POST['delcb'] == 'Yes')
        {
            $db->query(
                    "DELETE FROM `challengesbeaten`
                     WHERE `npcid` = {$r['userid']}");
        }
        echo 'Challenge Bot ' . $r['username']
                . ' removed.<br />
              &gt; <a href="staff.php">Goto Main</a>';
        stafflog_add("Removed Challenge Bot {$r['username']}");
    }
    else
    {
        $csrf = request_csrf_html('staff_delbot');
        echo "
        <h3>Remove Challenge Bot</h3>
        <hr />
        This will not delete the user from the game, only remove their entry as a Battle Tent Challenge Bot.
        <form action='staff_battletent.php?action=delbot' method='post'>
        	Bot: " . challengebot_dropdown('userid')
                . "
        	<br />
        	Delete challengesbeaten entries for this bot?
        		<input type='radio' name='delcb' value='Yes' checked='checked' /> Yes
        		<input type='radio' name='delcb' value='No' /> No
        	<br />
        	{$csrf}
        	<input type='submit' value='Remove Bot' />
        </form>
   		";
    }
}
$h->endpage();
