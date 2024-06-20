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
 * File: mainmenu.php
 * Signature: 3bcf67b27090c7472d474029e2007a07
 * Date: Fri, 20 Apr 12 08:50:30 +0000
 */

if (!defined('JDSF45TJI'))
{
    echo 'This file cannot be accessed directly.';
    die;
}
global $db, $c, $ir, $set;
$hc = $set['hospital_count'];
$jc = $set['jail_count'];
$ec = $ir['new_events'];
$mc = $ir['new_mail'];
if ($ir['hospital'])
{
    echo "
	<a href='hospital.php'>Hospital ($hc)</a><br />
	<a href='inventory.php'>Inventory</a><br />
   	";
}
elseif ($ir['jail'])
{
    echo "<a href='jail.php'>Jail ($jc)</a><br />";
}
else
{
    echo "<a href='index.php'>Home</a><br />
	<a href='inventory.php'>Inventory</a><br />";
}
echo ($ec > 0)
        ? '<a href="events.php" style="font-weight: bold;">Events (' . $ec
                . ')</a><br />' : '<a href="events.php">Events (0)</a><br />';
echo ($mc > 0)
        ? '<a href="mailbox.php" style="font-weight: bold;">Mailbox (' . $mc
                . ')</a><br />' : '<a href="mailbox.php">Mailbox (0)</a><br />';
if ($ir['jail'] and !$ir['hospital'])
{
    echo "
	<a href='gym.php'>Jail Gym</a><br />
	<a href='hospital.php'>Hospital ($hc)</a><br />
   	";
}
elseif (!$ir['hospital'])
{
    echo "
	<a href='explore.php'>Explore</a><br />
	<a href='gym.php'>Gym</a><br />
	<a href='criminal.php'>Crimes</a><br />
	<a href='job.php'>Your Job</a><br />
	<a href='education.php'>Local School</a><br />
	<a href='hospital.php'>Hospital ($hc)</a><br />
	<a href='jail.php'>Jail ($jc)</a><br />
   	";
}
else
{
    echo "<a href='jail.php'>Jail ($jc)</a><br />";
}
echo "<a href='forums.php'>Forums</a><br />";
echo ($ir['new_announcements'])
        ? '<a href="announcements.php" style="font-weight: bold;">Announcements ('
                . $ir['new_announcements'] . ')</a><br />'
        : '<a href="announcements.php">Announcements (0)</a><br />';
echo "
<a href='newspaper.php'>Newspaper</a><br />
<a href='search.php'>Search</a><br />
   ";
if (!$ir['jail'] && $ir['gang'])
{
    echo "<a href='yourgang.php'>Your Gang</a><br />";
}
if ($ir['user_level'] > 1)
{
    echo "
	<hr />
	<a href='staff.php'>Staff Panel</a><br />
	<hr />
	<b>Staff Online:</b><br />
   	";
    $online_cutoff = time() - 900;
    $q =
            $db->query(
                    "SELECT `userid`, `username`, `laston`
                     FROM `users`
                     WHERE `laston` > ({$online_cutoff})
                     AND `user_level` > 1
                     ORDER BY `userid` ASC");
    while ($r = $db->fetch_row($q))
    {
        echo '<a href="viewuser.php?u=' . $r['userid'] . '">' . $r['username']
                . '</a> (' . datetime_parse($r['laston']) . ')<br />';
    }
    $db->free_result($q);
}
if ($ir['donatordays'])
{
    echo "
	<hr />
	<b>Donators Only</b><br />
	<a href='friendslist.php'>Friends List</a><br />
	<a href='blacklist.php'>Black List</a>
   	";
}
echo "
<hr />
<a href='preferences.php'>Preferences</a><br />
<a href='preport.php'>Player Report</a><br />
<a href='helptutorial.php'>Help Tutorial</a><br />
<a href='gamerules.php'>Game Rules</a><br />
<a href='viewuser.php?u={$ir['userid']}'>My Profile</a><br />
<a href='logout.php'>Logout</a><br /><br />
Time is now<br />
" . date('F j, Y') . '<br />' . date('g:i:s a');
