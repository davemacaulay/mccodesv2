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
 * File: smenu.php
 * Signature: 3f5a7bbb749b6730b7c33d18fee3811f
 * Date: Fri, 20 Apr 12 08:50:30 +0000
 */

/** @noinspection SpellCheckingInspection */
if (!defined('JDSF45TJI'))
{
    echo 'This file cannot be accessed directly.';
    die;
}
global $db, $c, $ir, $set;
echo "&gt; <a href='index.php'>Back To Game</a><hr />
<b>General</b><br />
&gt; <a href='staff.php'>Index</a><br />";
if (check_access('administrator', false)) {
    echo "
		&gt; <a href='staff.php?action=basicset'>Basic Settings</a><br />
		&gt; <a href='staff.php?action=announce'>Add Announcement</a><br />
		&gt; <a href='staff.php?action=fire-cron'>Fire Cron</a><br />";
}
if (check_access(['manage_roles', 'manage_staff'], false)) {
    if (check_access('manage_roles', false)) {
        echo "
            <hr>
            <b>Staff Roles</b><br>
            &gt; <a href='staff_roles.php'>View Staff Roles</a><br>
            &gt; <a href='staff_roles.php?action=add'>Create Staff Role</a><br>
            &gt; <a href='staff_roles.php?action=edit'>Edit Staff Role</a><br>
            &gt; <a href='staff_roles.php?action=remove'>Delete Staff Role</a><br>
        ";
    }
    if (check_access('manage_staff', false)) {
        echo "
            &gt; <a href='staff_roles.php?action=grant'>Grant Staff Role</a><br>
            &gt; <a href='staff_roles.php?action=revoke'>Revoke Staff Role</a><br>
        ";
    }
}
if (check_access(['manage_users', 'view_user_inventory', 'credit_user', 'manage_player_reports', 'credit_all_users', 'manage_items', 'credit_item'], false))
{
    echo ' <hr />
	<b>Users</b><br />';
    if (check_access(['manage_users'], false))
    {
        echo "&gt; <a href='staff_users.php?action=newuser'>Create New User</a><br />
		&gt; <a href='staff_users.php?action=edituser'>Edit User</a><br />
		&gt; <a href='staff_users.php?action=deluser'>Delete User</a><br />";
    }
    if (check_access('view_user_inventory', false)) {
        echo "&gt; <a href='staff_users.php?action=invbeg'>View User Inventory</a><br />";
    }
    if (check_access('credit_user', false)) {
        echo "&gt; <a href='staff_users.php?action=creditform'>Credit User</a><br />";
    }
    if (check_access('credit_all_users', false)) {
        echo "&gt; <a href='staff_users.php?action=masscredit'>Mass Payment</a><br />";
    }
    if (check_access('manage_users', false)) {
        echo "&gt; <a href='staff_users.php?action=forcelogout'>Force User Logout</a><br />";
    }
    if (check_access('manage_player_reports', false)) {
        echo "
	&gt; <a href='staff_users.php?action=reportsview'>Player Reports</a><br />";
    }
    if (check_access(['manage_items', 'credit_item'], false)) {
        echo '<hr />
        <b>Items</b><br />';
        if (check_access('manage_items', false)) {
            echo "
            &gt; <a href='staff_items.php?action=newitem'>Create New Item</a><br />
            &gt; <a href='staff_items.php?action=edititem'>Edit Item</a><br />
            &gt; <a href='staff_items.php?action=killitem'>Delete An Item</a><br />
            &gt; <a href='staff_items.php?action=newitemtype'>Add Item Type</a><br />";
        }
        echo "&gt; <a href='staff_items.php?action=giveitem'>Give Item To User</a><br />";
    }
}
if (check_access('view_logs', false)) {
    echo "<hr /><b>Logs</b><br />
    &gt; <a href='staff_logs.php?action=atklogs'>Attack Logs</a><br />
    &gt; <a href='staff_logs.php?action=cashlogs'>Cash Xfer Logs</a><br />
    &gt; <a href='staff_logs.php?action=cryslogs'>Crystal Xfer Logs</a><br />
    &gt; <a href='staff_logs.php?action=banklogs'>Bank Xfer Logs</a><br />
    &gt; <a href='staff_logs.php?action=itmlogs'>Item Xfer Logs</a><br />
    &gt; <a href='staff_logs.php?action=maillogs'>Mail Logs</a><br />
    &gt; <a href='staff_logs.php?action=cron-fails'>Cron Fail Logs</a><br />
    ";
}
if (check_access('manage_gangs', false))
{
    echo " <hr />
    <b>Gangs</b><br />
    &gt; <a href='staff_gangs.php?action=grecord'>Gang Record</a><br />
    &gt; <a href='staff_gangs.php?action=gcredit'>Credit Gang</a><br />
    &gt; <a href='staff_gangs.php?action=gwar'>Manage Gang Wars</a><br />
    &gt; <a href='staff_gangs.php?action=gedit'>Edit Gang</a><br />";
}
if (check_access('manage_shops', false)) {

    echo " <hr />
    <b>Shops</b><br />
    &gt; <a href='staff_shops.php?action=newshop'>Create New Shop</a><br />
    &gt; <a href='staff_shops.php?action=newstock'>Add Item To Shop</a><br />
    &gt; <a href='staff_shops.php?action=delshop'>Delete Shop</a><br />";
}
if (check_access('manage_polls', false)) {
    echo "<hr /><b>Polls</b><br />
    &gt; <a href='staff_polls.php?action=spoll'>Start Poll</a><br />
    &gt; <a href='staff_polls.php?action=endpoll'>End A Poll</a><br />";
}
if (check_access('manage_jobs', false)) {
    echo "<hr /><b>Jobs</b><br />
    &gt; <a href='staff_jobs.php?action=newjob'>Make a new Job</a><br />
    &gt; <a href='staff_jobs.php?action=jobedit'>Edit a Job</a><br />
    &gt; <a href='staff_jobs.php?action=jobdele'>Delete a Job</a><br />
    &gt; <a href='staff_jobs.php?action=newjobrank'>Make a new Job Rank</a><br />
    &gt; <a href='staff_jobs.php?action=jobrankedit'>Edit a Job Rank</a><br />
    &gt; <a href='staff_jobs.php?action=jobrankdele'>Delete a Job Rank</a><br />";
}
if (check_access('manage_houses', false)) {
    echo "<hr /><b>Houses</b><br />
    &gt; <a href='staff_houses.php?action=addhouse'>Add House</a><br />
    &gt; <a href='staff_houses.php?action=edithouse'>Edit House</a><br />
    &gt; <a href='staff_houses.php?action=delhouse'>Delete House</a><br />";
}
if (check_access('manage_cities', false)) {
    echo "<hr /><b>Cities</b><br />
    &gt; <a href='staff_cities.php?action=addcity'>Add City</a><br />
    &gt; <a href='staff_cities.php?action=editcity'>Edit City</a><br />
    &gt; <a href='staff_cities.php?action=delcity'>Delete City</a><br />";
}
if (check_access('manage_forums', false)) {
    echo "<hr /><b>Forums</b><br />
    &gt; <a href='staff_forums.php?action=addforum'>Add Forum</a><br />
    &gt; <a href='staff_forums.php?action=editforum'>Edit Forum</a><br />
    &gt; <a href='staff_forums.php?action=delforum'>Delete Forum</a><br />";
}
if (check_access('manage_courses', false)) {
    echo "<hr /><b>Courses</b><br />
    &gt; <a href='staff_courses.php?action=addcourse'>Add Course</a><br />
    &gt; <a href='staff_courses.php?action=editcourse'>Edit Course</a><br />
    &gt; <a href='staff_courses.php?action=delcourse'>Delete Course</a><br />";
}
if (check_access('manage_crimes', false)) {
    echo "<hr /><b>Crimes</b><br />
    &gt; <a href='staff_crimes.php?action=newcrime'>Create New Crime</a><br />
    &gt; <a href='staff_crimes.php?action=editcrime'>Edit Crime</a><br />
    &gt; <a href='staff_crimes.php?action=delcrime'>Delete Crime</a><br />
    &gt; <a href='staff_crimes.php?action=newcrimegroup'>Create New Crime Group</a><br />
    &gt; <a href='staff_crimes.php?action=editcrimegroup'>Edit Crime Group</a><br />
    &gt; <a href='staff_crimes.php?action=delcrimegroup'>Delete Crime Group</a><br />
    &gt; <a href='staff_crimes.php?action=reorder'>Reorder Crime Groups</a><br />";
}
if (check_access('manage_challenge_bots', false)) {
    echo "<hr /><b>Battle Tent</b><br />
    &gt; <a href='staff_battletent.php?action=addbot'>Add Challenge Bot</a><br />
    &gt; <a href='staff_battletent.php?action=editbot'>Edit Challenge Bot</a><br />
    &gt; <a href='staff_battletent.php?action=delbot'>Remove Challenge Bot</a><br />";
}
if (check_access('manage_punishments', false)) {
    echo "<hr />
    <b>Punishments</b><br />
    &gt; <a href='staff_punit.php?action=mailform'>Mail Ban User</a><br />
    &gt; <a href='staff_punit.php?action=unmailform'>Un-Mailban User</a><br />
    &gt; <a href='staff_punit.php?action=forumform'>Forum Ban User</a><br />
    &gt; <a href='staff_punit.php?action=unforumform'>Un-Forumban User</a><br />
    &gt; <a href='staff_punit.php?action=fedform'>Jail User</a><br />
    &gt; <a href='staff_punit.php?action=fedeform'>Edit Fedjail Sentence</a><br />
    &gt; <a href='staff_punit.php?action=unfedform'>Unjail User</a><br />
    &gt; <a href='staff_punit.php?action=ipform'>Ip Search</a><br />";
}
if (check_access(['edit_newspaper', 'mass_mail', 'manage_staff', 'manage_donator_packs'], false)) {
    echo '<hr /><b>Special</b><br />';
    if (check_access('edit_newspaper', false)) {
        echo "&gt; <a href='staff_special.php?action=editnews'>Edit Newspaper</a><br />";
    }
    if (check_access('mass_mail', false)) {
        echo "&gt; <a href='staff_special.php?action=massmailer'>Mass mailer</a><br />";
    }
    if (check_access('manage_donator_packs', false)) {
        echo "&gt; <a href='staff_special.php?action=givedpform'>Give User Donator Pack</a><br />";
    }
}
echo '<hr /><b>Staff Online:</b><br />';
$online_staff = get_online_staff();
foreach ($online_staff as $r)
{
    echo '<a href="viewuser.php?u=' . $r['userid'] . '">' . $r['username']
        . '</a> (' . datetime_parse($r['laston']) . ')<br />';
}
echo "<hr />
&gt; <a href='logout.php'>Logout</a><br /><br />
Time is now<br />
";
echo date('F j, Y') . '<br />' . date('g:i:s a');
